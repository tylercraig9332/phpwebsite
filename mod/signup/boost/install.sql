CREATE TABLE signup_sheet (
    id int not null default 0,
    key_id int not null default 0,
    title varchar(80) not null,
    description text,
    start_time int not null default 0,
    end_time int not null default 0,
    primary key (id)
);

CREATE TABLE signup_peeps (
  id int NOT NULL default 0,
  sheet_id int NOT NULL default 0,
  slot_id int NOT NULL default 0,
  first_name varchar(60) NOT NULL,
  last_name varchar(60) NOT NULL,
  email varchar(100) NOT NULL,
  phone varchar(30) NOT NULL,
  hash char(32) default NULL,
  timeout int NOT NULL default 0,
  registered smallint NOT NULL default 0,
  PRIMARY KEY  (id)
);


CREATE TABLE signup_slots (
    id int not null default 0,
    sheet_id int not null default 0,
    title varchar(80) not null,
    openings int not null default 0,
    s_order smallint not null default 1,
    primary key (id)
);
