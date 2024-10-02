CREATE TABLE `application1`.`users`
(
    `id_user`                 INT          NOT NULL AUTO_INCREMENT,
    `user_name`               VARCHAR(45)  NOT NULL,
    `user_lastname`           VARCHAR(45)  NULL,
    `user_birthday_timestamp` INT          NULL,
    `login`                   VARCHAR(45)  NOT NULL,
    `password_hash`           VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_user`)
) ENGINE = InnoDB
  DEFAULT CHARSET = 'utf8';

INSERT INTO `application1`.`users` (user_name, user_lastname, user_birthday_timestamp, login, password_hash)
VALUES ('Admin', 'Admin', NULL, 'admin', '$2b$12$QcpRFotI9f5mGBW0hc18YegewneOJ1sXyDjMEOsichPHHvgz6ZJsq');

CREATE TABLE `application1`.`user_roles`
(
    `id_user_role` INT(11) NOT NULL AUTO_INCREMENT,
    `id_user` INT(11) NOT NULL,
    `role` VARCHAR(45) NOT NULL,
    PRIMARY KEY (`id_user_role`)
) ENGINE = InnoDB
  DEFAULT CHARSET = 'utf8';

INSERT INTO `application1`.`user_roles` (id_user_role, id_user, role)
VALUES ('1', '1', 'admin');

CREATE TABLE `application1`.`remember_me_tokens`
(
    `id_user` INT(11) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_user`)
) ENGINE = InnoDB
  DEFAULT CHARSET = 'utf8';
