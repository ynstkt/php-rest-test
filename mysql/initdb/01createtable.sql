SET PERSIST local_infile= 1;

CREATE TABLE foodb.items (
  id varchar(255),
  name varchar(1000),
  primary key (id)
);

GRANT ALL ON foodb.items TO foo;
