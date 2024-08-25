CREATE DATABASE prodmanage;

USE prodmanage;

CREATE TABLE part (
part_id INT NOT NULL AUTO_INCREMENT,
part_name VARCHAR(20) NOT NULL,
part_desc VARCHAR(1000),
CONSTRAINT PK_part_id PRIMARY KEY (part_id)
);

CREATE TABLE workstation (
workstation_id INT NOT NULL AUTO_INCREMENT,
workstation_capacity INT NOT NULL,
workstation_status INT NOT NULL,
is_active INT NOT NULL,
part_id INT NOT NULL,
CONSTRAINT PK_workstation_id PRIMARY KEY (workstation_id),
CONSTRAINT FK_part_id_workstation_table FOREIGN KEY (part_id) REFERENCES part (part_id)
);

CREATE TABLE user (
user_id INT NOT NULL AUTO_INCREMENT,
user_name VARCHAR(20) NOT NULL,
password VARCHAR(100) NOT NULL,
user_type VARCHAR(20) NOT NULL,
workstation_id INT,
CONSTRAINT PK_user_id PRIMARY KEY (user_id),
CONSTRAINT U_user_name UNIQUE (user_name),
CONSTRAINT FK_workstation_id_user_table FOREIGN KEY (workstation_id) REFERENCES workstation (workstation_id)
);

CREATE TABLE orders (
order_id INT NOT NULL AUTO_INCREMENT,
part_id INT NOT NULL,
quantity INT NOT NULL,
workstation_id INT NOT NULL,
order_status INT NOT NULL,
CONSTRAINT PK_order_id PRIMARY KEY (order_id),
CONSTRAINT FK_part_id_order_table FOREIGN KEY (part_id) REFERENCES part (part_id),
CONSTRAINT FK_workstation_id_order_table FOREIGN KEY (workstation_id) REFERENCES workstation (workstation_id)
);
