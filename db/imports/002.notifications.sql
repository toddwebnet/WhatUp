create table if not exists notifcation
(
  note_id int unsigned auto_increment,
  site_id tinyint,
  event_start_ts datetime,
  first_is_sent tinyint,
  second_is_sent tinyint,
  third_is_sent tinyint,
  primary key (note_id)
);

create table site_config
(
  site_config_id int AUTO_INCREMENT,
  site_id int,
  email varchar(255),
  primary key (site_config_id)
)