CREATE TABLE IF NOT EXISTS  `tms_queue_category_##QID##` (
	`category_id` INT(10) NOT NULL AUTO_INCREMENT,
	`category_name` VARCHAR(100) NOT NULL DEFAULT '0' COLLATE 'latin1_swedish_ci',
	`sub_category` TEXT NOT NULL COLLATE 'latin1_swedish_ci',
	PRIMARY KEY (`category_id`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `tms_queue_escalationmatrix_##QID##` (
	`escalation_id` INT(11) NOT NULL AUTO_INCREMENT,
	`priority_id` INT(11) NULL DEFAULT NULL,
	`days` INT(11) NULL DEFAULT NULL,
	`hours` INT(11) NULL DEFAULT NULL,
	`minute` INT(11) NULL DEFAULT NULL,
	`emails` TEXT NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`mobile_no` TEXT NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`shift_id` INT(11) NULL DEFAULT NULL,
	`template_id` INT(11) NULL DEFAULT NULL,
	`send_email` TINYINT(4) NULL DEFAULT NULL,
	`send_sms` TINYINT(4) NULL DEFAULT NULL,
	`active` TINYINT(4) NULL DEFAULT NULL,
	`level` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`escalation_id`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `tms_queue_priority_##QID##` (
	`priority_id` INT(11) NOT NULL AUTO_INCREMENT,
	`priority_name` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`colour_code` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`tat` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`order_no` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`is_default` TINYINT(4) NULL DEFAULT '0',
	PRIMARY KEY (`priority_id`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `tms_queue_status_##QID##` (
	`status_id` INT(11) NOT NULL AUTO_INCREMENT,
	`status_name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`colour_code` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`ticket_status_type` INT(11) NULL DEFAULT NULL,
	`order_no` INT(11) NULL DEFAULT NULL,
	`is_default` TINYINT(4) NULL DEFAULT '0',
	`is_close_status` TINYINT(4) NULL DEFAULT '0' COMMENT '0 => \'not close\',1 => \'close\'',
	`template_id` INT(11) NULL DEFAULT NULL,
	`customer_email` TINYINT(4) NULL DEFAULT NULL,
	`customer_sms` TINYINT(4) NULL DEFAULT NULL,
	`customer_wp` TINYINT(4) NULL DEFAULT NULL,
	`user_email` TINYINT(4) NULL DEFAULT NULL,
	`user_sms` TINYINT(4) NULL DEFAULT NULL,
	`user_wp` TINYINT(4) NULL DEFAULT NULL,
	PRIMARY KEY (`status_id`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `tms_conversation_attachements_##QID##` (
  `attachement_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(100) DEFAULT NULL,
  `ticket_unique_id` INT(11) NULL DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `attachement_name` varchar(200) DEFAULT NULL,
  `content_type` varchar(200) DEFAULT NULL,
  `attachement_filepath` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`attachement_id`)  
);

CREATE TABLE IF NOT EXISTS `tms_conversation_master_##QID##` (
`conversation_id` int(11) NOT NULL AUTO_INCREMENT,
`conversation_date` DATETIME null DEFAULT NULL ,
`conversation_type` VARCHAR(50) DEFAULT NULL,
`ticket_unique_id` INT(11) DEFAULT NULL,
`ticket_id` VARCHAR(50) DEFAULT NULL,
`customer_mobile_no` VARCHAR(16) DEFAULT NULL,
`email_uid` VARCHAR(500) DEFAULT NULL,
`account_id` INT(11) DEFAULT NULL,
`from_email` VARCHAR(320) DEFAULT NULL,
`to_email` TEXT DEFAULT NULL,
`cc_email` TEXT DEFAULT NULL,
`bcc_email` TEXT DEFAULT NULL,
`subject` TEXT DEFAULT NULL,
`sms_conversation` TEXT DEFAULT NULL,
`category_id` varchar(50) DEFAULT NULL,
 `subcategory_id` varchar(50) DEFAULT NULL,
 `status_id` INT(11) DEFAULT NULL,
 `priority_id` INT(11) DEFAULT NULL,
 `queue_id` INT(11) DEFAULT NULL,
 `login_id` INT(11) DEFAULT NULL,
 `email_status` VARCHAR(30) DEFAULT NULL,
 `sms_status` VARCHAR(30) DEFAULT NULL,
 `no_of_try` INT(11) DEFAULT NULL,
 `max_try` INT(11) DEFAULT NULL,
 `email_message_id` VARCHAR(500) DEFAULT NULL,
 `sms_no_of_try` INT(11) DEFAULT NULL,
 `sms_max_try` INT(11) DEFAULT NULL,
 `fetch_xml_uid` VARCHAR(250) DEFAULT NULL,
 `hold_date` DATETIME null DEFAULT NULL,
 `json_file_name` VARCHAR(100) DEFAULT NULL,
 PRIMARY KEY (`conversation_id`)
 );

-- CREATE TABLE IF NOT EXISTS `tms_junk_conversation_attachements_##QID##` (
-- `exchange_version` INT(10) NULL DEFAULT NULL,
-- `attachement_id`  int(11) NOT NULL AUTO_INCREMENT,
-- `ticked_id` VARCHAR(50) DEFAULT NULL,
-- `conversation_id` INT(11) DEFAULT NULL,
-- `attachement_name` VARCHAR(200) DEFAULT NULL,
-- `content_type`VARCHAR(200) DEFAULT NULL,
-- `attachement_filepath` VARCHAR(200) DEFAULT NULL,
-- PRIMARY KEY (`attachement_id`)
-- );

-- CREATE TABLE IF NOT EXISTS `tms_junk_conversation_master_##QID##`(
-- `conversation_id`  int(11) NOT NULL AUTO_INCREMENT,
-- `conversation_date` DATETIME NULL DEFAULT NULL ,
-- `conversation_type` VARCHAR(50) DEFAULT NULL ,
-- `ticket_unique_id` INT(11) DEFAULT NULL ,
-- `ticket_id`  VARCHAR(50) DEFAULT NULL ,
-- `customer_mobile_no` VARCHAR(16) DEFAULT NULL ,
-- `email_uid`  VARCHAR(500) DEFAULT NULL ,
-- `account_id` INT(11) DEFAULT NULL ,
-- `from_email` TEXT DEFAULT NULL ,
-- `to_email` TEXT DEFAULT NULL ,
-- `cc_email` TEXT DEFAULT NULL  ,
-- `bcc_email` TEXT DEFAULT NULL ,
-- `subject` TEXT DEFAULT NULL , 
-- `email_conversation` TEXT DEFAULT NULL ,
-- `sms_conversation` TEXT DEFAULT NULL ,
-- `email_rawcontent` TEXT DEFAULT NULL ,
-- `category_id` INT(11) DEFAULT NULL ,
-- `subcategory_id` INT(11) DEFAULT NULL ,
-- `status_id` INT(11) DEFAULT NULL ,
-- `priority_id` INT(11) DEFAULT NULL ,
-- `queue_id` INT(11) DEFAULT NULL ,
-- `login_id` INT(11) DEFAULT NULL ,  
-- `email_status` VARCHAR(30) DEFAULT NULL ,
-- `sms_status` VARCHAR(30) DEFAULT NULL ,
-- `no_of_try` INT(11) DEFAULT NULL ,
-- `max_try` INT(11) DEFAULT NULL ,
-- `email_message_id` VARCHAR(500) DEFAULT NULL ,
-- `sno` INT(11) DEFAULT NULL ,
-- `fetch_xml_uid` VARCHAR(200) DEFAULT NULL ,
-- `hold_date` DATETIME NULL DEFAULT NULL ,
-- PRIMARY KEY (`conversation_id`)
-- );

-- CREATE TABLE IF NOT EXISTS `tms_junk_ticket_master_##QID##`(
-- `ticket_prefix` VARCHAR(10),
-- `ticket_unique_id` INT NOT NULL AUTO_INCREMENT,
-- `ticket_id` VARCHAR(50) DEFAULT NULL,
-- `customer_id` INT(11) DEFAULT NULL ,
-- `customer_name` VARCHAR(50) DEFAULT NULL,
-- `unique_id` VARCHAR(15) DEFAULT NULL,
-- `process_id` INT(11) DEFAULT NULL ,
-- `subject` VARCHAR(100) DEFAULT NULL ,
-- `email_body_text` TEXT DEFAULT NULL,
-- `sms_body_text` TEXT DEFAULT NULL ,
-- `start_date` DATETIME NULL DEFAULT NULL ,
-- `from_email` TEXT DEFAULT NULL,
-- `to_email` TEXT DEFAULT NULL,
-- `sms_to` TEXT DEFAULT NULL,
-- `cc_email` TEXT DEFAULT NULL,
-- `bcc_email`TEXT DEFAULT NULL,
-- `start_mode`VARCHAR(50) DEFAULT NULL,
-- `status_id` INT(11) DEFAULT NULL,
-- `priority_id` INT(11) DEFAULT NULL,
-- `queue_id` INT(11) DEFAULT NULL,
-- `last_updated_on` DATETIME NULL DEFAULT NULL,
-- `is_read` BIT DEFAULT NULL,
-- `read_by`INT(11) DEFAULT NULL,
-- `read_date`DATETIME NULL DEFAULT NULL,
-- `is_assigned`BIT DEFAULT NULL,
-- `assigned_to`INT(11) DEFAULT NULL,
-- `assigned_by`INT(11) DEFAULT NULL,
-- `assigned_on`DATETIME NULL DEFAULT NULL,
-- `last_updated_by`INT(11) DEFAULT NULL,
-- `customer_mobile_no`VARCHAR(16) DEFAULT NULL,
-- `category_id`INT(11) DEFAULT NULL,
-- `subcategory_id`INT(11) DEFAULT NULL,
-- `follow_update`DATETIME NULL DEFAULT NULL,
-- `ticket_created_by`INT(11) DEFAULT NULL,
-- `is_assigned_open_ticket`BIT DEFAULT NULL,
-- `remarks`VARCHAR(100) DEFAULT NULL,
-- `is_replied_mail`BIT DEFAULT NULL,
-- `is_forward_mail` BIT DEFAULT NULL,
-- `happy_code` INT(11) DEFAULT NULL,
-- `hold_date`DATETIME NULL DEFAULT NULL,
-- `incoming_mail_id`VARCHAR(100) DEFAULT NULL,
-- PRIMARY KEY (`ticket_unique_id`)
-- );

CREATE TABLE IF NOT EXISTS `tms_junkmail_master_##QID##`(
`junk_mail_id`INT NOT NULL AUTO_INCREMENT,
`junk_email`VARCHAR(100) DEFAULT NULL,
`time_stamp`TIMESTAMP ,
PRIMARY KEY (`junk_mail_id`)
);


CREATE TABLE IF NOT EXISTS `tms_queue_rule_action_##QID##`(
`rule_action_id`INT NOT NULL AUTO_INCREMENT,
`rule_id` INT(11) DEFAULT NULL,
`action` VARCHAR(50) DEFAULT NULL,
`action_value`VARCHAR(50) DEFAULT NULL,
PRIMARY KEY (`rule_action_id`)
);

CREATE TABLE IF NOT EXISTS `tms_queue_rule_condition_##QID##`(
`rule_condition_id`INT NOT NULL AUTO_INCREMENT,
`rule_id` INT(11) DEFAULT NULL,
`evaluate_on`VARCHAR(50) DEFAULT NULL,
`operator`VARCHAR(50) DEFAULT NULL,
`evaluate_value`VARCHAR(500) DEFAULT NULL,
PRIMARY KEY (`rule_condition_id`)
);

CREATE TABLE IF NOT EXISTS `tms_queue_rule_master_##QID##`(
`rule_id`INT NOT NULL AUTO_INCREMENT,
`rule_name`VARCHAR(50) DEFAULT NULL,
`match_any_condition` BIT DEFAULT NULL,
`match_all_condition` BIT DEFAULT NULL,
`order_no` INT(11) DEFAULT NULL,
`created_date`DATETIME NULL DEFAULT NULL,
`created_by` INT(11) DEFAULT NULL,
`modified_date`DATETIME NULL DEFAULT NULL,
`modified_by` INT(11) DEFAULT NULL,
`time_stamp` TIMESTAMP ,
`shift_name` VARCHAR(100) DEFAULT NULL,
`time_from` TIME DEFAULT NULL,
`time_to` TIME DEFAULT NULL,
`is_active` BIT DEFAULT NULL,
`match` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
PRIMARY KEY(`rule_id`)
);


CREATE TABLE IF NOT EXISTS `tms_ticket_master_##QID##`(
	`ticket_prefix` VARCHAR(10) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`ticket_unique_id` INT(11) NOT NULL AUTO_INCREMENT,
	`ticket_id` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`customer_name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`customer_mobile_no` VARCHAR(16) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`unique_id` VARCHAR(15) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`parent_id` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`subject` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`start_date` DATETIME NULL DEFAULT NULL,
	`from_email` VARCHAR(500) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`to_email` VARCHAR(2000) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`cc_email` VARCHAR(2000) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`bcc_email` VARCHAR(2000) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`start_mode` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`status_id` INT(11) NULL DEFAULT NULL,
	`priority_id` INT(11) NULL DEFAULT NULL,
	`queue_id` INT(11) NULL DEFAULT NULL,
	`last_updated_on` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`is_read` BIT(1) NULL DEFAULT NULL,
	`read_by` INT(11) NULL DEFAULT NULL,
	`read_date` DATETIME NULL DEFAULT NULL,
	`mail_date` DATETIME NULL DEFAULT NULL,
	`is_assigned` BIT(1) NULL DEFAULT NULL,
	`assigned_to` INT(11) NULL DEFAULT NULL,
	`assigned_by` INT(11) NULL DEFAULT '0',
	`assigned_on` DATETIME NULL DEFAULT NULL,
	`last_updated_by` INT(11) NULL DEFAULT NULL,
	`phone_no` VARCHAR(16) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`category_id` varchar(50) NULL DEFAULT NULL,
	`subcategory_id` varchar(50) NULL DEFAULT NULL,
	`follow_up_date` DATETIME NULL DEFAULT NULL,
	`ticket_created_by` INT(11) NULL DEFAULT NULL,
	`is_assigned_open_ticket` BIT(1) NULL DEFAULT NULL,
	`remarks` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`is_replied_mail` BIT(1) NULL DEFAULT NULL,
	`is_forward_mail` BIT(1) NULL DEFAULT NULL,
	`created_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`happy_code` INT(11) NULL DEFAULT NULL,
	`escalation_id` INT(11) NULL DEFAULT NULL,
	`last_escalation_on` DATETIME NULL DEFAULT NULL,
	`hold_date` DATETIME NULL DEFAULT NULL,
	`incoming_mail_id` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`is_new` TINYINT(4) NULL DEFAULT '0' COMMENT '0 => not read,1 =>read',
	`parent_ticket_id` INT(11) NULL DEFAULT NULL,
	`parent_ticket_id_text` VARCHAR(30) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`is_junk` TINYINT(4) NULL DEFAULT '0',
PRIMARY KEY(`ticket_unique_id`)
);

CREATE TABLE IF NOT EXISTS `tms_unreadconversationcount_##QID##`(
`id`INT NOT NULL AUTO_INCREMENT,
`ticket_unique_id` INT(11) DEFAULT NULL ,
`ticket_id` VARCHAR(50) DEFAULT NULL,
`conversation_date` DATETIME NULL DEFAULT NULL,
`user_id` INT(11) DEFAULT NULL,
`subject` VARCHAR(2000) DEFAULT NULL,
`conversation_type` VARCHAR(10) DEFAULT NULL,
PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `tms_dynamicform_fields_##QID##` (
	`field_id` INT(10) NOT NULL AUTO_INCREMENT,
	`field_name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`field_description` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`data_type` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`max_line` SMALLINT(5) NULL DEFAULT NULL,
	`width` SMALLINT(5) NULL DEFAULT NULL,
	`field_length` SMALLINT(5) NULL DEFAULT NULL,
	`decimal_length` SMALLINT(5) NULL DEFAULT NULL,
	`field_type` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`values_from_db` TINYINT(1) NULL DEFAULT '0',
	`unique_field` TINYINT(1) NULL DEFAULT '0',
	`allow_null` TINYINT(1) NULL DEFAULT '0',
	`primary_field` TINYINT(1) NULL DEFAULT '0',
	`primary_phone_no` TINYINT(1) NULL DEFAULT '0',
	`filter` TINYINT(1) NULL DEFAULT '0',
	`excel_export` TINYINT(1) NULL DEFAULT '0',
	`print_pdf` TINYINT(3) NOT NULL DEFAULT '0',
	`list_column` TINYINT(1) NULL DEFAULT '0',
	`required` TINYINT(1) NULL DEFAULT '0',
	`field_values` TEXT NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`db_val_column` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`db_txt_column` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`onchange` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`onkeypress` TEXT NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`onclick` VARCHAR(100) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`status` TINYINT(1) NULL DEFAULT '1',
	`created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`created_by` INT(10) NOT NULL,
	`modified_date` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`modified_by` INT(10) NULL DEFAULT NULL,
	PRIMARY KEY (`field_id`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

