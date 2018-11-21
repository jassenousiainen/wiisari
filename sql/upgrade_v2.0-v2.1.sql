
ALTER TABLE `audit`     ADD `modified_office`   varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '';
ALTER TABLE `info`      ADD `punchoffice`       varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '';

UPDATE `dbversion` SET `dbversion` = '2.1';
