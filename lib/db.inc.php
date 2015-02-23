<?php
/**Upload Contesting Database Connection File
 * Original Creation Date 04.2014
 * Wherein we connect to ye database
 */


// Database connect
$db = mysql_connect (DB_HOST, DB_USER, DB_PASS) or die('Cannot connect to the database because: ' . mysql_error());
mysql_select_db (DB_NAME);
