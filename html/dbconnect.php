<?php
try {
  $db = new PDO('mysql:dbname=keijiban;host=mysql;charset=utf8', 'test', 'test');
} catch (PDOException $e) {
  print ('DB接続エラー : ' . $e->getMessage()); 
}
?>