<?php
require_once 'video.php';
require_once 'functions.php';
$id = isset($_GET['id']) ? trim($_GET['id']): null;
$sort = isset($_GET['s']) ? trim($_GET['s']): 'a';
function redirect_to($location){
  header("Location: ".$location);
  exit();
}
if(!auth_channel_id($id))redirect_to('/');
if (isset($_POST['search_submit'])) {
    $search_q = isset($_POST['search']) ? trim($_POST['search']): null;
  $url = "/s.php?q=".urlencode($search_q);
  redirect_to ($url);  
}
?>

<!doctype html>
<html>
  <head>
    <title>Videoscotch -  <?php echo getChannelNameById($id); ?> - Youtube</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="myscript.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
      #content{
        width: 95%;
        margin: 0 auto!important;
      }
      .moreLable{
        /*padding: 5px 10px;*/
        margin: 20px;
      }
      #footer{
      width:100%!important;
    }
    </style>
  </head>
  <body>
<?php include 'header.php'; ?>

<div id="sorter">
    <?php 
     if ($id!=null) {
    ?> 
    sort by: <a href="/channel.php?id=<?php echo $id; ?>&s=mv"
<?php if ($sort=='mv') echo ' class="std"';?>
    >views</a> <a href="/channel.php?id=<?php echo $id; ?>&s=ml"
<?php if ($sort=='ml') echo ' class="std"';?>
    >likes</a> 
    <?php } ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</div>

<div id="content">
<p class="moreLable">
   <?php echo getChannelNameById($id); ?>
 </p>
  <?php
   if ($id!=null) {
       $page=0;
       if (!isset($page) || $page==0) {
          if ($sort=='mv'||$sort=='ml')$vids=Video::show_videos_of_channel(0,$id,$sort);
          else $vids=Video::show_videos_of_channel(0, $id, 'a');
       }else{
          if ($sort=='mv'||$sort=='ml')$vids=Video::show_videos_of_channel($page,$id,$sort);
          else $vids=Video::show_videos_of_channel($page, $id, 'a');
       }
       foreach ($vids as $vid) {
  ?>
      <div id="<?php echo $vid->vid_id; ?>" class="vid_box">
        <a href="/w.php?v=<?php echo $vid->vid_id; ?>" class="vid_link">
        <img width="196" height="110" src="<?php echo $vid->vid_thumb; ?>" /> 
        </a>
        <span class="vid_dur"><?php echo processDuration($vid->duration); ?></span>
        <a href="/w.php?v=<?php echo $vid->vid_id; ?>" class="vid_link"><p><?php echo getExcerpt($vid->vid_title); ?></p>
        </a>        
        <div class="vid_ch_title_div">
          <span><a href="/channel.php?id=<?php echo $vid->vid_channel_id; ?>" class="vid_ch_title"><?php echo $vid->vid_ch_title; ?></a>
          <span class="vid_date"> <?php echo timeAgo($vid->vid_date); ?></span>
          </span>
        </div>
             <table class="tb_stat">
                  <tr class="stat_tr">
                <td class="stat_td">
                  <?php  if($vid->vid_viewCount>=1000000&&$vid->vid_viewCount<1000000000) {?>
                  <span style="color:#cc181e"><?php echo nice_number_format($vid->vid_viewCount); ?></span>
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
   <div id="pag_link"><a id="loadMore" onclick=loadMore("<?php echo $page+1; ?>"<?php echo ','; ?>"<?php echo $id; ?>"<?php echo ','; ?>"<?php echo "ch"; ?>"<?php echo ','; ?>"<?php echo $sort; ?>") class="more">Load More</a></div>
   <div class="clear"></div>
  <?php } //endof if
    else{
      echo "The channel you're looking for doesn't exists in our databases.<br />";
      echo "Submit this channel id by clicking here. ";
      echo  '<a href="/vidfetcher.php" target="_blank">Fetch videos</a>';
    }
  ?>
   <?php
     include 'footer.php';
   ?>
</body>
</html>