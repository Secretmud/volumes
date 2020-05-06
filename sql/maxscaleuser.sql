grant select on mysql.* to 'maxscaleuser'@'maxscale' IDENTIFIED BY 'maxscalepass';
grant replication slave on *.* to 'maxscaleuser'@'maxscale';
grant replication client on *.* to 'maxscaleuser'@'maxscale';
grant show databases on *.* to 'maxscaleuser'@'maxscale';
flush privileges;
