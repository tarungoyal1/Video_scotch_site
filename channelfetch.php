<?php
require_once 'video.php';
require_once 'functions.php';

 if (isset($_POST['search_submit'])) {
    $id = isset($_POST['search']) ? trim($_POST['search']): null;
    if (isset($id) && $id!=null) {
      echo $id."<br />";
      if(auth_channel_id($id)){
       echo "Channel id already exists, no need to do anything further with this channel.<br />";
       echo "Try with another channel id.";
      }else{
         $videos=fetchvideos($id);
        if($videos!=null && !empty($videos)){
            processVideos($videos,$id);
        }else{
          echo "empty or null videos array";
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
    <title>Video Fetcher by channel </title>
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
    <input id="search_input" type="text" name="search" value="" placeholder="enter youtube channel id" required/>
    <button name="search_submit" id="searchImg">submit</button>
</form>
<hr />
<a href="http://johnnythetank.github.io/youtube-channel-name-converter/" target="_blank">Youtube Username to id converter</a>
<p>
Note: After submitting
 the channel id, please keep this page open until it's done.</br>
After execution is done, you will get information about how many videos fetched, inserted, etc.
</p>
  </body>
</html>