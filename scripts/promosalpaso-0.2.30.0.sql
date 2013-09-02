ALTER TABLE `promosalpaso`.`category_promotion` CHANGE COLUMN `category_promotion_id` `category_promotion_id` BIGINT(20) NOT NULL AUTO_INCREMENT  ;

ALTER TABLE `promosalpaso`.`price_rule` CHANGE COLUMN `value1` `value1` DECIMAL(4,2) NOT NULL DEFAULT -1  , CHANGE COLUMN `value2` `value2` DECIMAL(4,2) NOT NULL DEFAULT -1  , CHANGE COLUMN `value3` `value3` DECIMAL(4,2) NOT NULL DEFAULT -1  , CHANGE COLUMN `value4` `value4` DECIMAL(4,2) NOT NULL DEFAULT -1  , CHANGE COLUMN `created` `created` DATETIME NULL  , ADD COLUMN `value5` DECIMAL(4,2) NOT NULL DEFAULT -1  AFTER `value4` , ADD COLUMN `value6` DECIMAL(4,2) NOT NULL DEFAULT -1  AFTER `value5` , ADD COLUMN `value7` DECIMAL(4,2) NOT NULL DEFAULT -1  AFTER `value6` , ADD COLUMN `value8` DECIMAL(4,2) NOT NULL DEFAULT -1  AFTER `value7` , ADD COLUMN `value9` DECIMAL(4,2) NOT NULL DEFAULT -1  AFTER `value8` ;

ALTER TABLE `promosalpaso`.`promotion` MODIFY COLUMN `promo_value` DECIMAL(7,2) NOT NULL;

