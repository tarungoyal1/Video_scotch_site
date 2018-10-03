<?php
function redirect_to($location){
  header("Location: ".$location);
  exit();
}
  $search_q = isset($_GET['q']) ? trim($_GET['q']): null;
    if (isset($_POST['search_submit'])) {
  $query = isset($_POST['search']) ? trim($_POST['search']): null;
  $url = "/s.php?q=".urlencode($query);
  redirect_to ($url);
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<style type="text/css">
		#search_item {
			border: 1px solid grey;
			padding: 15px 20px;
			margin: 10px 0;
		}
		.title{
			color: blue;
			font-size:1.1em;
		}
    a.vid_link{
   color: #167ac6;
   text-decoration: none;
   font-weight: 500;
   font-family: Roboto,arial,sans-serif;
}

a.vid_link:hover, a.vid_link:focus{
  text-decoration: underline;
}
    a.vid_ch_title{
      margin-top: 10px;
    }
    .search_cname{
      margin-top: 5px;
      padding-top: 5px;
    }
    #footer{
      width:100%!important;
    }
    .search_desc{
      line-height: 1.4;
      font-size: small;
    font-family: arial,sans-serif;
    color: #545454;
    }
	</style>
	<script type="text/javascript">
  window.onload = function(){
    ajaxFunction();
  }
function ajaxFunction(){
 var ajaxRequest;  // The variable that makes Ajax possible!
	
 try{
   // Opera 8.0+, Firefox, Safari
   ajaxRequest = new XMLHttpRequest();
 }catch (e){
   // Internet Explorer Browsers
   try{
      ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
   }catch (e) {
      try{
         ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
      }catch (e){
         // Something went wrong
         alert("Your browser broke!");
         return false;
      }
   }
 }
 // Create a function that will receive data 
 // sent from the server and will update
 // div section in the same page.
 ajaxRequest.onreadystatechange = function(){
   if(ajaxRequest.readyState == 4){
      var ajaxDisplay = document.getElementById('content');
      ajaxDisplay.innerHTML = ajaxRequest.responseText;
   }
 }
 // Now get the value from user and pass it to
 // server script.
 var searchquery = document.getElementById('search-text').value;
 var queryString = "qu=" + searchquery ;
 ajaxRequest.open("POST", "ajax.php", true);
 ajaxRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
 ajaxRequest.send(queryString);
}
</script>
<title>Videoscotch - results for "<?php echo $search_q; ?>"</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- <script type="text/javascript" src="jquery-3.1.1.min.js"></script> -->
    
     <script type="text/javascript" src="myscript.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<div id="header">
   <div id="search-box">
      <form id='search-form' action="" method="POST">
        <input id="search-text" name="search" type="text" value="<?php echo htmlentities($search_q); ?>"placeholder="type and hit enter" required/>
        <button type="submit" name="search_submit" id="search-button">SEARCH</button>
                
      </form>
   </div>
   <div id="home-logo">
     <a href="/">
     <img src="home-icon.png" width="32" height="32" />
     </a>
   </div>
</div>
 <div id="content">
			<!-- <div id="results">
		</div> -->
</div>
 <?php include 'footer.php'; ?>
</body>
</html>
