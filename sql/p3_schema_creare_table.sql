CREATE TABLE `PostalCode` (
    `postal_code` varchar(5) NOT NULL,
    `city` varchar(128) NOT NULL,
    `latitude` decimal(10,5) NOT NULL,
    `longitude` decimal(10,5) NOT NULL,
    `state` varchar(2) NOT NULL,
    PRIMARY KEY (`postal_code`)
);

CREATE TABLE `User`(
    `email` varchar(50) NOT NULL,
    `nickname` varchar(50) NOT NULL,
    `first_name` varchar(50) NOT NULL,
    `last_name` varchar(50) NOT NULL,
    `postal_code` varchar(5) NOT NULL,
    `password` varchar(150) NOT NULL,
    PRIMARY KEY (`email`),
    UNIQUE (`nickname`),
    FOREIGN KEY (`postal_code`)
    REFERENCES `PostalCode`(`postal_code`)
);


CREATE TABLE `ListedItem`(
    `item_number` int  NOT NULL AUTO_INCREMENT,
    `title` varchar(100) NOT NULL,
    `description` varchar(500) NULL,
    `condition_type` varchar(50) NOT NULL,
    `email` varchar(50) NOT NULL,
    `type` varchar(50) NOT NULL,
    `card_count` INT(11)  NULL,
    `platform` varchar(50)  NULL,
    `media` varchar(50)  NULL,
    PRIMARY KEY(`item_number`),
    FOREIGN KEY (`email`) REFERENCES `User`(`email`)
 );
 

CREATE TABLE `VideoGamePlatform`(
    `video_game_platform_type` varchar(50) NOT NULL,
PRIMARY KEY(`video_game_platform_type`));

CREATE TABLE `ComputerGamePlatform`(
    `computer_game_platform_type` varchar(50) NOT NULL,
PRIMARY KEY(`computer_game_platform_type`));



CREATE TABLE `Trade`(
    `proposed_item_num` INT NOT NULL,
    `desired_item_num` INT NOT NULL,
    `date_of_proposal` DATE NOT NULL,
    `date_of_response` DATE DEFAULT NULL,
    `accepted` TINYINT(1) NULL,
    UNIQUE(`proposed_item_num`, `desired_item_num`, `accepted`),
    FOREIGN KEY(`proposed_item_num`) REFERENCES `ListedItem`(`item_number`),
    FOREIGN KEY(`desired_item_num`) REFERENCES `ListedItem`(`item_number`)
 );




