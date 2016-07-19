create table if not exists local_upcheck_site
(
  site_id int auto_increment,
  site_name varchar(32),
  url varchar(255),
  active_ind tinyint,
  primary key (site_id)
);

insert into local_upcheck_site(site_name, url, active_ind)
values ('google', 'http://www.google.com', 1);
insert into local_upcheck_site(site_name, url, active_ind)
values ('archive', 'https://archive.org/', 1);