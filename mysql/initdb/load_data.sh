#!/bin/sh

DBUSER=foo
PASS=foopassword
DBNAME=foodb
CSVPATH=/tmp/data/items.csv

# MySQLをバッチモードで実行
CMD_MYSQL='mysql -ufoo -pfoopassword foodb --local_infile=1'

# ヒアドキュメントでSQL文を一括で実行
$CMD_MYSQL <<EOF
TRUNCATE TABLE items;
LOAD DATA LOCAL INFILE "${CSVPATH}"
INTO TABLE items
FIELDS
  TERMINATED BY ','
  OPTIONALLY ENCLOSED BY '"'
  ESCAPED BY ''
LINES
  STARTING BY ''
  TERMINATED BY '\n'
(
  id,
  name
);
EOF
