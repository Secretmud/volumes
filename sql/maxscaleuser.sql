grant select on mysql.* to 'maxscaleuser'@'172.17.0.9' IDENTIFIED BY 'maxscalepass';
grant replication slave on *.* to 'maxscaleuser'@'172.17.0.9';
grant replication client on *.* to 'maxscaleuser'@'172.17.0.9';
grant show databases on *.* to 'maxscaleuser'@'172.17.0.9';
flush privileges;
