# test_task

server version
==================

-Apache/2.4.29 (Win32) OpenSSL/1.1.0g PHP/7.2.3
---------------------------------------------------

create database
===================

-NAME => linck_db
-server_name => localhost
-user_name => root
-password => ''
-port => 3306
---------------------------------------------------

create table
===================

-NAME => linck_tabl
-comparison => utf8_general_ci
-id => int(11), UNSIGNED, AUTO_INCREMENT
-linck => text, utf8_general_ci
-new_linck => varchar(255), utf8_general_ci
-using_linck => int(11)
-ip_user => varchar(15), utf8_general_ci
-time => timestamp, CURRENT_TIMESTAMP, ON APDATE CURRENT_TIMESTAMP
-time_create => datetime, CURRENT_TIMESTAMP
