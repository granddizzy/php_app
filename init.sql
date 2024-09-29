CREATE TABLE `application1`.`users`
(
    `id_user`                 INT NOT NULL AUTO_INCREMENT,
    `user_name`               VARCHAR(45) NULL,
    `user_lastname`           VARCHAR(45) NULL,
    `user_birthday_timestamp` INT NULL,
    PRIMARY KEY (`id_user`)
) ENGINE = InnoDB
DEFAULT CHARSET = 'utf8';