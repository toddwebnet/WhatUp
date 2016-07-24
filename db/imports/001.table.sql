create table if not exists test_run
(
  run_id int unsigned auto_increment,
  up_down tinyint,
  run_ts datetime,
  primary key (run_id)
);

create table if not exists site
(
  site_id int unsigned auto_increment,
  address varchar(32),
  check_type char(1),
  is_active tinyint,
  primary key (site_id)
);

create table if not exists site_ping
(
  ping_id int unsigned auto_increment,
  site_id int unsigned,
  test_value int,
  ping_ts datetime,
  primary key (ping_id)
);
/*
ALTER TABLE site_ping
  ADD CONSTRAINT fk_site_ping_site FOREIGN KEY (site_id) REFERENCES site_ping (site_id);
*/
