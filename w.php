<?php
require_once 'video.php';
require_once 'functions.php';
$id = isset($_GET['v']) ? trim($_GET['v']): null;
function redirect_to($location){
  header("Location: ".$location);
  exit();
}
if ($id==null||empty($id)) redirect_to('/');
$vid_title = getVidTitlebyId($id);
if ($vid_title=='vidnotexist')redirect_to('/');
if (isset($_POST['search_submit'])) {
    $search_q = isset($_POST['search']) ? trim($_POST['search']): null;
  $url = "/s.php?q=".urlencode($search_q);
  redirect_to ($url);  
}
?>
<!doctype html>
<html> 
  <head>
    <title><?php echo $vid_title; ?> - Videoscotch - Youtube</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="myscript.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
      body {
        margin-top:10px;
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 0;
        padding:0;
      }

      #content{
      margin-top:10px;
      margin-bottom: 0;
      width: 100%;
      background-color: #000;
      padding:5px;
      /*float: right;*/
      /*border:1px solid red;*/
     }

     #vcount{
      float: right;
      font-family: Roboto,arial,sans-serif;
     }
     #ratingbox{
      float: right;
      margin-top: 20px;

     }
     #vlikes{
      margin-right: 15px;
      opacity: 0.9;
     }
     #vdislikes{
      /*margin-right: 15px;*/
      opacity: 0.9;
     }
     .videoinfo{
      width:80%;
  max-width: 640px;
  margin: 0 auto;
  margin-top: 10px;
  /*background-color: yellow;*/
  padding:10px;
  /*height: 80px;*/
     }
     #title{
      font-size: 18px;
      font-weight: normal;
      opacity: 2;
     font-family: Roboto,arial,sans-serif;
      width: 75%;
     }
#moreVids{
  width: 90%;
  margin: 0 auto;
}

#footer{
  width: 100%;
}

    </style>
  </head>
  <body>
<?php include 'header.php'; ?>
 <div id="content">
    <div class="youtubevideowrap">
     <div class="video-container">
      <iframe width="640" height="352" src="https://www.youtube.com/embed/<?php echo $id; ?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
     </div>
    </div>
</div>
<div class="clear"></div>
<?php $videoinfo=getVideoInfobyId($id); ?>
<div class="videoinfo">
  <div id="title">
    <?php echo $videoinfo['vid_title']; ?>
  </div>
 <div id="vcount"><?php echo number_format($videoinfo['vid_viewCount']); ?> views</div>
 <div class="clear"></div>
 <div id="ratingbox"> 
    <span id="vlikes"><img src="like.png" width="16" height="16" style="margin-right: 5px" /><?php echo number_format($videoinfo['vid_likes']); ?></span>
    <span id="vdislikes"><img src="dislike.png" width="16" height="16" style="margin-right: 5px" /><?php echo number_format($videoinfo['vid_dislikes']); ?></span>
  </div>
  <p></p>
</div>
<div class="clear"></div>
 <div id="moreVids">
 <p class="moreLable">
   More from this channel:
 </p>
    <?php if ($videoinfo!=null&&!empty($videoinfo)) {
            //suggest more vids of this user
            $channelId = $videoinfo['vid_channel_id'];

            //suggest more vids of this user
            $moreVids = Video::suggestMoreVideosofId($channelId, $id);
            if (!empty($moreVids)) {
              foreach ($moreVids as $vid) {
    ?>

    <div id="<?php echo $vid->vid_id; ?>" class="vid_box">
        <a href="/w.php?v=<?php echo $vid->vid_id; ?>" class="vid_link" >
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
            }
          }

     ?>
</div>
<div class="clear"></div>
  <div id="footer">    
   <div id="logo">
        <a href="/">VideoScotch</a>
    </div>
    <div id="foot_links">
         Links:<br>
         <a class="submit-link" href="/channelfetch.php" target="_blank">Fetch Videos of channel</a><br>
         <a class="submit-link" href="/vidfetcher.php" target="_blank">Fetch a single video</a><br>
    </div> 
  </div>
</body>
</html>