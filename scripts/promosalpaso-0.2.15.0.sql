ALTER TABLE `promosalpaso`.`messages` ADD COLUMN `location` VARCHAR(60) NULL  AFTER `created`;ALTER TABLE `promosalpaso`.`message` CHANGE COLUMN `ip` `ip` VARCHAR(36) NULL DEFAULT NULL  ;
