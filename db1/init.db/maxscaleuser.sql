flush privileges;
grant all privileges on *.* to 'DATSUSER'@'%';
grant select on mysql.* to 'MAXSUSER'@'MAX_IP' IDENTIFIED BY 'MAX_PASS';
grant replication slave on *.* to 'MAXSUSER'@'MAX_IP';
grant replication client on *.* to 'MAXSUSER'@'MAX_IP';
grant show databases on *.* to 'MAXSUSER'@'MAX_IP';
flush privileges;
