<?php
require_once 'video.php';
require_once 'functions.php';
if (isset($_POST['search_submit'])) {
    $id = isset($_POST['search']) ? trim($_POST['search']): null;
    if (isset($id) && $id!=null) {
      $flag=false;
      if (filter_var($id, FILTER_VALIDATE_URL) == TRUE) {
          echo 'Valid URL<br />';
          if(parse_url($id, PHP_URL_HOST)=='www.youtube.com'
            ||parse_url($id, PHP_URL_HOST)=='youtube.com'){
              $id = parse_url($id, PHP_URL_QUERY);
             $id=substr($id,2);
             $flag=true;
          }else{
              echo "You entered an invalid url. If you're facing issues submitting full youtube url, you could go ahead with just video id.";
              $flag=false;
          }
      }else{
        // echo "You entered an invalid url. If you're facing issues submitting full youtube url, you could go ahead with just video id.";
              $flag=true;
      }   
      if ($flag) {
          // echo $id."<br />";
          if(checkIfExists($id)){
           echo "Video with this id already exists, no need to do anything further with this video.<br />";
           echo "Try with another video id.";
          }else{
            processSingleVideo($id,true);
          }
       } 
    }else{
      echo "id is not set or is null";
    }
 }
?>
<!doctype html>
<html>
  <head>
    <title>Fetch a youtube video - Videoscotch</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style type="text/css">
    	#fetchform input {
    		width: 300px;
    		height: 25px;
    	}
    	#fetchform button {
    		height: 30px;
    		line-height: 1.4em;
    	}
    </style>
  </head>
  <body>
  <?php include 'header.php'; ?>
<form action="" method="post" id="fetchform">
    <input id="search_input" type="text" name="search" value="" placeholder="enter youtube video id or url" required/>
    <button name="search_submit" id="searchImg">submit</button>
</form>
<hr />
<p>
Note: After submitting the video id, please keep this page open until it's done.</br>
After execution is done, you will get information about whether video fetched, inserted, etc.
</p>
  </body>
</html>