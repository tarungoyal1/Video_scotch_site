<?php
require_once 'video.php';
require_once 'functions.php';
$cat_id = isset($_GET['c']) ? trim($_GET['c']): 0;
if (!auth_cat($cat_id))$cat_id=0;
$sort = isset($_GET['s']) ? trim($_GET['s']): 'a';
function redirect_to($location){
  header("Location: ".$location);
  exit();
}
if (isset($_POST['search_submit'])) {
    $search_q = isset($_POST['search']) ? trim($_POST['search']): null;
  $url = "/s.php?q=".urlencode($search_q);
  redirect_to ($url);  
}
?>

<!doctype html>
<html>
  <head>
    <title>Videoscotch</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="myscript.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
  <?php include 'header.php'; ?>
 <div id="container">
  <div id="leftbar">
    <div id="mySidenav" class="sidenav">
      <a href="/">All</a>
      <a href="/?c=10">Music</a>
      <a href="/?c=23">Comedy</a>
      <a href="/?c=24">Entertainment</a>
      <a href="/?c=30">Movies</a>
      <a href="/?c=20">Gaming</a>
      <a href="/?c=26">How-to & Style</a>
      <a href="/?c=35">Documentary</a>
      <a href="/?c=25">News & Politics</a>
      <a href="/?c=27">Education</a>
      <a href="/?c=22">People & Blogs</a>
      <a href="/?c=28">Science & Technology</a>
      <a href="/?c=29">Sport</a>
      <a href="/?c=19">Travel & Events</a>
      <a href="/?c=2">Autos & Vehicles</a>
      <a href="/?c=15">Pets & Animals</a>
      <a href="/?c=1">Film & Animation</a>
    </div>
    <span onclick="openNav()">open</span>
  </div>
   <div id="sorter">
    <?php 
     if ($cat_id!=0) {

    ?> 
    sort by: <a href="/?c=<?php echo $cat_id; ?>&s=mv"
    <?php if ($sort=='mv') echo ' class="std"';?>
    >views</a> <a href="/?c=<?php echo $cat_id; ?>&s=ml" 
        <?php if ($sort=='ml') echo ' class="std"';?>
    >likes</a> 
    <?php
     }else {
    ?>
      sort by: <a href="/?s=mv"
<?php if ($sort=='mv') echo ' class="std"';?>
      >views</a> <a href="/?s=ml"
        <?php if ($sort=='ml') echo ' class="std"';?>

      >likes</a>

    <?php } ?>


    </div>
  <div id="content">
   
  <?php
   $page=0;
    if (!isset($page) || $page==0) {
      if ($sort=='mv'||$sort=='ml')$vids=Video::show_all_videos(0,$cat_id,$sort);
      else $vids=Video::show_all_videos(0,$cat_id,'a');
    }else{
      if ($sort=='mv'||$sort=='ml')$vids=Video::show_all_videos($page, $cat_id,$sort);
      else $vids=Video::show_all_videos($page, $cat_id,'a');
    }
    // $vids=Video::show_videos_of_channel('UCdsTjyAj9R3P6bTsFSCmOkg');
    foreach ($vids as $vid) {
      ?>
      <div id="<?php echo $vid->vid_id; ?>" class="vid_box">
        <a href="/w.php?v=<?php echo $vid->vid_id; ?>" target="_blank" class="vid_link" >
        <img width="196" height="110" src="<?php echo $vid->vid_thumb; ?>" /> 
        </a>
        <span class="vid_dur"><?php echo processDuration($vid->duration); ?></span>
        <a href="/w.php?v=<?php echo $vid->vid_id; ?>" target="_blank" class="vid_link"><p><?php echo getExcerpt($vid->vid_title); ?></p>
        </a>        
        <div class="vid_ch_title_div">
          <span><a href="/channel.php?id=<?php echo $vid->vid_channel_id; ?>" class="vid_ch_title" target="_blank"><?php echo $vid->vid_ch_title; ?></a>
          <span class="vid_date"> <?php echo timeAgo($vid->vid_date); ?></span>
          </span>
        </div>
             <table class="tb_stat">
                <tr class="stat_tr">
                <td class="stat_td">
                  <?php  if($vid->vid_viewCount>=1000000&&$vid->vid_viewCount<1000000000) {?>
                  <span style="color:#cc181e;"><?php echo nice_number_format($vid->vid_viewCount); ?></span>
                  <?php }elseif($vid->vid_viewCount>=1000000000) { ?>
                  <span style="color:#167ac6;font-weight: bolder;"><?php echo nice_number_format($vid->vid_viewCount); ?></span>
                  <?php  } else {  ?>
                  <span><?php echo nice_number_format($vid->vid_viewCount); ?></span>
                  <?php } ?>
                  </td>
                  <td class="stat_td"><?php echo nice_number_format($vid->vid_likes); ?></td> 
                  <td class="stat_td"><?php echo nice_number_format($vid->vid_comments); ?></td>
                </tr>
                <tr id="tr_<?php echo $vid->vid_id; ?>" class="st_tr">
                  <td class="stat_td"><span class="vid_v_ico"></span></td>
                  <td class="stat_td"><span class="vid_like_ico"></span></td> 
                  <td class="stat_td"><span class="vid_comm_ico"></span></td>
                </tr>
            </table>
      </div>
  <?php
    }
  ?>
  </div><div class="clear"></div>
  <?php if (isset($vids)&& !empty($vids)) {
   ?>
   <div id="pag_link"><a id="loadMore" onclick=loadMore("<?php echo $page+1; ?>"<?php echo ','; ?>"<?php echo $cat_id; ?>"<?php echo ','; ?>"<?php echo "cat"; ?>"<?php echo ','; ?>"<?php echo $sort; ?>") class="more">Load More</a></div>
  <?php } ?>
<div class="clear"></div>
  <?php include 'footer.php'; ?>
 </div>
</body>
</html>