/* Create user and set privileges */

DROP USER hmtuser@'127.0.0.1';
CREATE USER hmtuser@'127.0.0.1' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON handymantool.* TO 'hmtuser'@'127.0.0.1';
FLUSH PRIVILEGES;