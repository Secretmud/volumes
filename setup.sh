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
        if test -f "$FILE"; then
                sudo cp $FILE /etc/hosts.bak
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
        echo "1:Run all\n2:Install docker\n3:Setup hosts\n4:Setup Docker containers"
}

setup_containers() {
        for i in 1 2 3
        do
                sudo docker run --name web$i --hostname web$i  -v ~/volumes/web/:/var/www/html -d richarvey/nginx-php-fpm

        done

	sudo docker run -d --name lb -v ~/volumes/lb:/usr/local/etc/haproxy:ro haproxy:latest

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
esac
