<?php 
require 'config.php';
require 'database.php';
function increment_pageviews(){
  global $db;
  $sql="UPDATE stats SET views=views+1 LIMIT 1";
     $result = $db->perform_query($sql);
     if ($db->get_affected_rows()==1) {         
        return true;      
     }else {
        return false;
     }         
}
increment_pageviews(); 
?>