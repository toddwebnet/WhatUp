create table if not exists site
(
  site_id int auto_increment,
  site_name varchar(32),
  url varchar(255),
  active_ind tinyint,
  primary key (site_id)
);

create table if not exists site_ping
(
  ping_id int auto_increment,
  site_id int,
  pass_fail int,
  time_to_load int,
  ping_ts datetime,
  primary key (ping_id)
);
/*
ALTER TABLE site_ping
  ADD CONSTRAINT fk_site_ping_site FOREIGN KEY (site_id) REFERENCES site_ping (site_id);
*/
