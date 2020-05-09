#!/bin/bash
#general project info
export project_name="dats42-project"
#Users and passwords
export root_password="rootpass"
export datsusername="dats42"
export datspassword="stream doctor come"
export maxuser="maxscaleuser"
export maxpass="maxscalepass"
#Webservers
export w1_container_name="web1"
export w1_host_name="web1"
export w1_IP="172.17.0.2"
export w2_container_name="web2"
export w2_host_name="web2"
export w2_IP="172.17.0.3"
export w3_container_name="web3"
export w3_host_name="web3"
export w3_IP="172.17.0.4"
#loadbal
export loadbal_container_name="lb"
export loadbal_host_name="haproxy"
export loadbal_IP="172.17.0.5"
#databases
export database1_container_name="db1"
export database1_host_name="dbgc1"
export database1_IP="172.17.0.6"
export database2_container_name="db2"
export database2_host_name="dbgc2"
export database2_IP="172.17.0.7"
export database3_container_name="db3"
export database3_host_name="dbgc3"
export database3_IP="172.17.0.8"
#dbproxy
export databaseproxy_container_name="dbproxy"
export databaseproxy_host_name="maxscale"
export databaseproxy_IP="172.17.0.9"
