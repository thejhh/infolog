CREATE TABLE `infodesk_log` (
    log_id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    updated          TIMESTAMP NOT NULL DEFAULT 0,
    created          TIMESTAMP NOT NULL DEFAULT 0,
    msg             VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY(log_id)) CHARACTER SET utf8 ENGINE=InnoDB;

