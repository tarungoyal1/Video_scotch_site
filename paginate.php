<?php
if ($_SERVER['REQUEST_METHOD']=="GET") {
	header("Location: error.php");
	exit();
}
?>
<?php
include 'functions.php';
include 'video.php';
?>
<?php
$page = isset($_POST['page']) ? trim($_POST['page']): die('Please try again');
$type = isset($_POST['type']) ? trim($_POST['type']): die('Something is wrong, Please try again !');
$sort = isset($_POST['srt']) ? trim($_POST['srt']): 'a';
$cat_id=0;
$ch_id=null;
if (trim($type)=="cat") {
    $cat_id = isset($_POST['id']) ? trim($_POST['id']): 0;
}else if (trim($type)=="ch") {
    $ch_id = isset($_POST['id']) ? trim($_POST['id']): null;
}

if($ch_id==null){
    if (!auth_cat($cat_id))$cat_id=0;
    $vids = Video::show_all_videos($page, $cat_id, $sort);
}else{
    if(!auth_channel_id($ch_id))$ch_id=null;
     $vids=Video::show_videos_of_channel($page, $ch_id, $sort);
}

if (empty($vids)) {
		echo "error||No more data";
		die();
	}
	else {
		foreach ($vids as $vid) {
            if ($vid->vid_id!=null){
            	$HTML  = '<div id='.$vid->vid_id.' class="vid_box">';
                $HTML .= '<a href="/w.php?v='.$vid->vid_id.'" target="_blank" class="vid_link">';
                $HTML .= '<img width="196" height="110" src="'.$vid->vid_thumb.'" /></a>';
                $HTML .= '<span class="vid_dur">'.processDuration($vid->duration).'</span>';
                $HTML .= '<a href="/w.php?v='.$vid->vid_id.'" target="_blank" class="vid_link"><p>'.getExcerpt($vid->vid_title).'</p></a>';
                $HTML .= '<div class="vid_ch_title_div"><span><a href="/channel.php?id='.$vid->vid_channel_id.'" class="vid_ch_title" ';
                if (trim($type)!="ch") $HTML .= 'target="_blank"';
                $HTML .= '>'.$vid->vid_ch_title.'</span></a>';
                $HTML .= '<span class="vid_date">'.timeAgo($vid->vid_date).'</span></div>';
                $HTML .= '<table class="tb_stat"><tr class="stat_tr"><td class="stat_td">';
                if($vid->vid_viewCount>=1000000&&$vid->vid_viewCount<1000000000)
                      $HTML .= '<span style="color:#cc181e;">';
                elseif($vid->vid_viewCount>=1000000000)$HTML .= '<span style="color:#167ac6;font-weight: bolder;">';
                else $HTML .= '<span>';

                $HTML .= nice_number_format($vid->vid_viewCount);
                $HTML .= '</span></td><td class="stat_td">'.nice_number_format($vid->vid_likes).'</td>';
                $HTML .= '<td class="stat_td">'.nice_number_format($vid->vid_comments).'</td></tr>';
                $HTML .= '<tr id="tr_'.$vid->vid_id.'" class="st_tr">';
                $HTML .= '<td class="stat_td"><span class="vid_v_ico"></span></td>
                <td class="stat_td"><span class="vid_like_ico"></span></td> 
                <td class="stat_td"><span class="vid_comm_ico"></span></td>
              </tr>
          </table></div>';
		    	echo $HTML;
		    }

		}
	}

	
?>

