#!/bin/sh

install_docker() {
        sudo apt install -y docker.io
        sudo docker pull richarvey/nginx-php-fpm
        sudo docker pull haproxy
        sudo docker pull mariadb
        sudo docker pull mariadb/maxscale
}

hosts_setup() {
        FILE=/etc/hosts.bak
        if [[ -f "$FILE" ]];
	then
        	sudo cp $FILE /etc/hosts
        else
		sudo cp /etc/hosts $FILE
        fi
        sudo /bin/sh -c 'echo 172.17.0.2 web1 >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.3 web2 >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.4 web3 >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.5 haproxy >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.6 dbgc1 >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.7 dbgc2 >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.8 dbgc3 >> /etc/hosts'
        sudo /bin/sh -c 'echo 172.17.0.9 maxscale >> /etc/hosts'
}

print_menu() {
	echo "1:Run all\n2:Install docker\n3:Setup hosts\n4:Setup Docker containers\n5:Clean all volumes - not included in Run all(1)."
}

clean_volumes() {
	sudo docker kill web1 web2 web3 lb db1 db2 db3 dbproxy
	sudo docker container rm web1 web2 web3 lb db1 db2 db3 dbproxy
	cd ~/
	sudo rm -rf volumes/
	sudo git clone 	https://github.com/Secretmud/volumes.git
}

setup_containers() {
	
	
        for i in 1 2 3
        do
                sudo docker run --name web$i --hostname web$i  -v ~/volumes/webapp/:/var/www/html -d richarvey/nginx-php-fpm

        done

	sudo docker run -d --name lb --add-host web1:172.17.0.2 --add-host web2:172.17.0.3 --add-host web3:172.17.0.4 -v ~/volumes/lb:/usr/local/etc/haproxy:ro haproxy:latest
	sleep 10
	sudo docker run -d --name db1 --hostname dbgc1 \
	  -e MYSQL_ROOT_PASSWORD="rootpass" \
	  -e MYSQL_USER=maxscaleuser \
	  -e MYSQL_PASSWORD=maxscalepass \
	  -v ~/volumes/db1/datadir:/var/lib/mysql \
	  -v ~/volumes/db1/conf.d:/etc/mysql/mariadb.conf.d \
	  mariadb:10.4 \
	   --wsrep-new-cluster

	sleep 40
	sudo docker run -d --name db2 --hostname dbgc2 \
	  -e MYSQL_ROOT_PASSWORD="rootpass" \
	  -e MYSQL_USER=maxscaleuser \
	  -e MYSQL_PASSWORD=maxscalepass \
	  -v ~/volumes/db2/datadir:/var/lib/mysql \
	  -v ~/volumes/db2/conf.d:/etc/mysql/mariadb.conf.d \
	  mariadb:10.4

	sleep 40
	sudo docker run -d --name db3 --hostname dbgc3 \
	  -e MYSQL_ROOT_PASSWORD="rootpass" \
	  -e MYSQL_USER=maxscaleuser \
	  -e MYSQL_PASSWORD=maxscalepass \
	  -v ~/volumes/db3/datadir:/var/lib/mysql \
	  -v ~/volumes/db3/conf.d:/etc/mysql/mariadb.conf.d \
	  mariadb:10.4
	sleep 10
	sudo docker run -d --name dbproxy --hostname maxscale -v ~/volumes/dbproxy/my-maxscale.cnf:/etc/maxscale.cnf.d/my-maxscale.cnf mariadb/maxscale:latest
}

print_menu
echo "Enter numerical option"
read input
case $input in
        1)
                install_docker
                hosts_setup
                setup_containers
        ;;

        2)
                install_docker
        ;;

        3)
                hosts_setup
        ;;

        4)
                setup_containers
        ;;
	5)
		clean_volumes
	;;
esac

