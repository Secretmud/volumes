#!/bin/bash
if [[ -f "dats42-params.sh" ]];
then
    . dats42-params.sh
else
    . ~/volumes/dats42-params.sh
fi

GREEN='\033[0;32m' #green color
NC='\033[0m' #no color
machine_ip=$(hostname -I | awk '{print $1}')
#########################
# SET this manually if above command does not work
machine_ip=$machine_ip


##########Countdown to show user how long is remaining
secs=$((10))
Countdown() {
    while [ $secs -gt 0 ]; do
        echo -e  -ne "$secs\033[0K\r"
        sleep 1
        : $((secs--))
    done
}

# Installing docking and required images.
install_docker() {
    echo -e  -e "${GREEN}Installing docker..${NC}"
    sleep 1
    sudo apt install -y docker.io
    echo -e  -e "${GREEN}Pulling docker images..${NC}"
    sleep 1
    sudo docker pull richarvey/nginx-php-fpm:latest
    sudo docker pull haproxy:latest
    sudo docker pull mariadb:10.4
    sudo docker pull mariadb/maxscale:latest
}

# Adds the host with their bound ip to the /etc/hosts file on the host machine.
hosts_setup() {
    echo -e  "${GREEN}Configuring /etc/hosts..${NC}"
    sleep 1
    FILE=/etc/hosts.bak
    #makes a backup of original before adding
    if [[ -f "$FILE" ]];
    then
        sudo cp $FILE /etc/hosts
    else
        sudo cp /etc/hosts $FILE
    fi
    sudo /bin/sh -c "echo -e  $w1_IP $w1_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $w2_IP $w2_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $w3_IP $w3_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $loadbal_IP $loadbal_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $database1_IP $database1_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $database2_IP $database2_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $database3_IP $database3_host_name >> /etc/hosts"
    sudo /bin/sh -c "echo -e  $databaseproxy_IP $databaseproxy_host_name >> /etc/hosts"
}

clean_volumes() {
    echo -e  "${GREEN}Removing any conflicting docker containers..${NC}"
    sleep 1
    sudo docker stop $w1_container_name $w2_container_name $w3_container_name $loadbal_container_name \
    $database1_container_name $database2_container_name $database3_container_name $databaseproxy_container_name
    sudo docker rm $w1_container_name $w2_container_name $w3_container_name $loadbal_container_name \
    $database1_container_name $database2_container_name $database3_container_name $databaseproxy_container_name
    cd ~/
    sudo rm -rf ~/volumes/
    echo -e  "${GREEN}Cloning github repository and setting up..${NC}"
    sudo git clone 	https://github.com/Secretmud/volumes.git
    sudo mkdir -p ~/volumes/db2/datadir/mysql
    sudo mkdir -p ~/volumes/db3/datadir/mysql
    
    # Coniguring permissions for sql and database.
    sudo sed -i "s/DATSUSER/$datsusername/g; s/MAX_USERNAME/$maxuser/g; s/MAX_PASS/$maxpass/g; s/MAX_IP/$databaseproxy_IP/g" ~/volumes/db1/init.db/maxscaleuser.sql
    sudo sed -i "s/DATSUSER/$datsusername/g; s/DATSPASS/$datspassword/g" ~/volumes/web1/html/include/dbconnection.php
    sudo echo -e  ~/volumes/web2/html/include/ ~/volumes/web3/html/include/ | sudo xargs -n 1 cp ~/volumes/web1/html/include/dbconnection.php
    sudo sed -i "s/MAX_USERNAME/$maxuser/g; s/MAX_PASS/$maxpass/g; s/DB1_HOST/$database1_host_name/g;s/DB2_HOST/$database2_host_name/g;s/DB3_HOST/$database3_host_name/g" ~/volumes/dbproxy/my-maxscale.cnf
    sudo sed -i "s/DATSUSER/$datsusername/g; s/MAX_USERNAME/$maxuser/g; s/MAX_PASS/$maxpass/g; s/MAXSCALE_IP/$databaseproxy_IP/g" ~/volumes/db1/init.db/maxscaleuser.sql
    sudo sed -i "s/DB1_HOST/$w1_IP/g; s/DB2_HOST/$w2_IP/g; s/DB3_HOST/$w3_IP/g; s/DB1_HOST_NAME/$w1_container_name/g" ~/volumes/db1/conf.d/my.cnf
    sudo sed -i "s/DB1_HOST/$w1_IP/g; s/DB2_HOST/$w2_IP/g; s/DB3_HOST/$w3_IP/g; s/DB2_HOST_NAME/$w2_container_name/g" ~/volumes/db2/conf.d/my.cnf
    sudo sed -i "s/DB1_HOST/$w1_IP/g; s/DB2_HOST/$w2_IP/g ;s/DB3_HOST/$w3_IP/g; s/DB3_HOST_NAME/$w3_container_name/g" ~/volumes/db3/conf.d/my.cnf
}

setup_containers() {
    
    ###########################
    #   webserver setup       #
    ###########################
    echo -e  "${GREEN}Setting up web server number 1..${NC}"
    sudo docker run --name $w1_container_name --hostname $w1_host_name --add-host $databaseproxy_host_name:$databaseproxy_IP -v ~/volumes/web1/html/:/var/www/html -d richarvey/nginx-php-fpm
    sleep 1
    echo -e  "${GREEN}Setting up web server number 2..${NC}"
    sudo docker run --name $w2_container_name --hostname $w2_host_name --add-host $databaseproxy_host_name:$databaseproxy_IP -v ~/volumes/web2/html/:/var/www/html -d richarvey/nginx-php-fpm
    sleep 1
    echo -e  "${GREEN}Setting up web server number 3..${NC}"
    sudo docker run --name $w3_container_name --hostname $w3_host_name --add-host $databaseproxy_host_name:$databaseproxy_IP -v ~/volumes/web3/html/:/var/www/html -d richarvey/nginx-php-fpm
    sleep 1
    
    
    
    
    ###########################
    #   Load balancer setup   #
    ###########################
    echo -e  "${GREEN}Configuring the loadbalancer..${NC}"
    sleep 1
    sudo docker run -d --name $loadbal_container_name --hostname $loadbal_host_name -p $machine_ip:80:80 \
    --add-host $w1_host_name:$w1_IP --add-host $w2_host_name:$w2_IP --add-host $w3_host_name:$w3_IP \
    --add-host $databaseproxy_host_name:$databaseproxy_IP -v ~/volumes/lb:/usr/local/etc/haproxy:ro haproxy:latest
    echo -e  "${GREEN}Web servers are set up. Setting up databases might take a while:${NC}"
    
    
    
    ###########################
    #   Database setup        #
    ###########################
    
    sudo docker run -d --name $database1_container_name --hostname $database1_host_name \
    -e MYSQL_ROOT_PASSWORD=$root_password -e MYSQL_USER=$maxuser -e MYSQL_PASSWORD=$maxpass -e MYSQL_USER="$datsusername" -e MYSQL_PASSWORD="$datspassword" \
    -v ~/volumes/db1/datadir:/var/lib/mysql -v ~/volumes/db1/conf.d:/etc/mysql/mariadb.conf.d \
    -v ~/volumes/db1/init.db/maxscaleuser.sql:/docker-entrypoint-initdb.d/maxscaleuser.sql:ro \
    -v ~/volumes/db1/init.db/studentinfo.sql:/docker-entrypoint-initdb.d/studentinfo.sql:ro \
    mariadb:10.4
    
    
    echo -e  "${GREEN}Setting up db1..${NC} This may take 1-3 min depending on your cpu."
    while true;
    do
        echo -e  -n "#"
        sudo /bin/bash -c "sudo docker logs db1 >& logger.txt"
        if grep -q "Temporary server stopped" logger.txt; then
            echo -e  ""
            echo -e  "10 secs left..!"
            sudo rm logger.txt
            sleep 10
            break;
        fi
        sleep 10
        
    done
    
    sudo docker run -d --name $database2_container_name --hostname $database2_host_name \
    -e MYSQL_ROOT_PASSWORD=$root_password \
    -e MYSQL_USER=$maxuser \
    -e MYSQL_PASSWORD=$maxpass \
    -v ~/volumes/db2/datadir:/var/lib/mysql \
    -v ~/volumes/db2/conf.d:/etc/mysql/mariadb.conf.d \
    mariadb:10.4
    
    echo -e  "${GREEN}Setting up db2.. ${NC}Finished in:"
    secs=$((40))
    x=$((2))
    Countdown
    sudo docker run -d --name $database3_container_name --hostname $database3_host_name \
    -e MYSQL_ROOT_PASSWORD=$root_password \
    -e MYSQL_USER=$maxuser\
    -e MYSQL_PASSWORD=$maxpass \
    -v ~/volumes/db3/datadir:/var/lib/mysql \
    -v ~/volumes/db3/conf.d:/etc/mysql/mariadb.conf.d \
    mariadb:10.4
    
    echo -e  "${GREEN}Setting up db3.. ${NC}Finished in:"
    secs=$((25))
    Countdown
    
    ###########################
    #   Dataproxy setup       #
    ###########################
    sudo docker run -d --name $databaseproxy_container_name --hostname $databaseproxy_host_name \
    --add-host $database1_host_name:$database1_IP \
    --add-host $database2_host_name:$database2_IP \
    --add-host $database3_host_name:$database3_IP \
    -v ~/volumes/dbproxy/my-maxscale.cnf:/etc/maxscale.cnf.d/my-maxscale.cnf mariadb/maxscale:latest
    echo -e  "${GREEN}Setting up dbproxy.. ${NC}Finished in:"
    secs=$((10))
    Countdown
    exit 0
}

echo -e  "${GREEN}Welcome to the docker setup script. Enjoy..${NC}"
clean_volumes
install_docker
hosts_setup
echo -e  "${GREEN}Preparing to set up containers:${NC}"
sleep 5
setup_containers
