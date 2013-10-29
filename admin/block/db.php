<?php
$link = mysql_connect ('127.0.0.1', 'user', 'pass') or die('connection fail');
mysql_set_charset('utf8', $link);
mysql_select_db ('table') or die (mysql_error());
?>