flush privileges;
grant all privileges on *.* to 'DATSUSER'@'%';
grant select on mysql.* to 'MAX_USERNAME'@'MAXSCALE_IP' IDENTIFIED BY 'MAX_PASS';
grant replication slave on *.* to 'MAX_USERNAME'@'MAXSCALE_IP';
grant replication client on *.* to 'MAX_USERNAME'@'MAXSCALE_IP';
grant show databases on *.* to 'MAX_USERNAME'@'MAXSCALE_IP';
flush privileges;
