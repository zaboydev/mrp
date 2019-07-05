<?php
//$conn = mysql_connect("119.252.163.205", "root", "") or die(mysql_error());

$conn = pg_connect("host=119.252.163.205 port=5432 dbname=BUDGETCONTROL2 user=postgres password=b1f42015");
//$conn = pg_connect("host=119.252.163.206 port=5432 dbname=MRP user=postgres password=bifa2016");

if (!$conn){
  echo "Not connected";
} else {
  echo "Connected";
}
