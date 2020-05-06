create user 'maxscaleuser'@'maxscale' identified by 'maxscalepass';
grant SELECT on mysql.user to 'maxscaleuser'@'maxscale';
GRANT SELECT ON mysql.db TO 'maxscaleuser'@'maxscale';
GRANT SELECT ON mysql.tables_priv TO 'maxscaleuser'@'maxscale';
GRANT SHOW DATABASES ON *.* TO 'maxscaleuser'@'maxscale';
grant REPLICATION SLAVE on *.* to 'maxscaleuser'@'maxscale';
grant REPLICATION CLIENT on *.* to 'maxscaleuser'@'maxscale';

