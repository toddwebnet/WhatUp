create table if not exists notification
(
  note_id int unsigned auto_increment,
  site_id tinyint,
  message_order tinyint,
  open_date datetime,
  close_date datetime,
  primary key (note_id)
);

create table site_config
(
  site_config_id int AUTO_INCREMENT,
  site_id int,
  email varchar(255),
  primary key (site_config_id)
)