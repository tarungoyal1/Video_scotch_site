<?php
require 'config.php';
require 'database.php';

function fetchvideos($id){

  global $db;
  $max_records=50;
  $apiKey = 'AIzaSyB5YybB0lb_NYXXNCekzrV3bnFmy52E0CI';
  $channelId= trim($db->string_prep($id));
  $videos = array();

  if (!empty($channelId)) {
   //first auth channel id
    $nextPageToken=null;
   $i=0;
   do{
       if ($nextPageToken==null) {
          $url = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$channelId.'&maxResults=50&mine=true&key='.$apiKey;
        }else{
          $url = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$channelId.'&pageToken='.$nextPageToken.'&maxResults=50&mine=true&key='.$apiKey;
        }
      $json_data = @file_get_contents($url);
      if ($json_data === false) {
       echo "error fetching video, check back and try again!";
       return array();
      }   
      $data = json_decode($json_data,true);
      
      foreach ($data['items'] as $key => $value) {
         if(isset($value['id']['videoId'])){
          $videos[$i]['id']= $value['id']['videoId'];
          $videos[$i]['title']= $value['snippet']['title'];
          $videos[$i]['desc']= $value['snippet']['description'];
          $videos[$i]['date']= $value['snippet']['publishedAt'];
          $videos[$i]['thumb'] = $value['snippet']['thumbnails']['medium']['url'];
          $videos[$i]['ch_title'] = $value['snippet']['channelTitle'];
          ++$i;
        }
      }
      if (isset($data['nextPageToken']))$nextPageToken = $data['nextPageToken']; 
  }while (isset($data['nextPageToken']));
  echo '-'.$i." - videos fetched.<br />";
  return $videos;
 }else {
  return null;
 }
}

function checkIfExists($vid_id){
  global $db;
  $id =trim($db->string_prep($vid_id));
  $sql="SELECT * FROM vids WHERE  vid_id='{$id}' LIMIT 1";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? true : false;
}

function auth_channel_id($ch_id){
  global $db;
  $id =trim($db->string_prep($ch_id));
  $sql="SELECT * FROM channels WHERE  channel_id='{$id}' LIMIT 1";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? true : false;
}

function getChannelNameById($id=""){
  global $db;
  $id =trim($db->string_prep($id));
  $sql="SELECT channel_title FROM channels WHERE channel_id='{$id}' LIMIT 1";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? $array['channel_title'] : "vidnotexist";
}

function getChannelIdbyVideoId($vid_id=""){
  global $db;
  $vid_id =trim($db->string_prep($vid_id));
  $sql="SELECT vid_channel_id FROM vids WHERE vid_id='{$vid_id}' LIMIT 1";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? $array['vid_channel_id'] : "vidnotexist";
}

function getVideoInfobyId($vid_id=""){
  global $db;
  $vid_id =trim($db->string_prep($vid_id));
  $sql="SELECT * FROM vids WHERE vid_id='{$vid_id}' LIMIT 1";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? $array : null;
}

function getVidTitlebyId($vid_id=""){
  global $db;
  $vid_id =trim($db->string_prep($vid_id));
  $sql="SELECT vid_title FROM vids WHERE vid_id='{$vid_id}' LIMIT 1";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? $array['vid_title'] : "vidnotexist";
}

function suggestMoreVideosofId($id="",$curr_vid=""){
  global $db;
  $vid_channel_id =trim($db->string_prep($id));
  $sql="SELECT * FROM vids WHERE vid_channel_id='{$vid_channel_id}' AND vid_id!='{$curr_vid}' LIMIT 20";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? $array : array();

}

function insertChannel_id_title($channelId="", $channelUname="",$channelTitle=""){
  global $db;
  if (auth_channel_id($channelId))return false;
  $channelId =trim($db->string_prep($channelId));
  $channelTitle =trim($db->string_prep($channelTitle));

  $sql  = "INSERT INTO channels (channel_id, channel_username, channel_title) ";
  $sql .= "VALUES ('{$channelId}', '{$channelUname}', '{$channelTitle}')";
     $result = $db->perform_query($sql);
     if ($db->get_affected_rows()==1) {
         return true;
     }
 return false;
} 

function processSingleVideo($inputId="",$shouldAuth=true){
      if ($shouldAuth) {
       echo '<br />'.'id entered:'.$inputId.'<br />';
        if (checkIfExists($inputId)) {
                echo $inputId." already exists <br />";
                return false;
        }
       }
        $apiKey = 'AIzaSyB5YybB0lb_NYXXNCekzrV3bnFmy52E0CI';
        ////
        $title = "";
        $id = "";
        $date = "";
        $thumb = "";
        $desc = "";
        $ch_id = "";
        $ch_title = "";
        $viewCount=0;
        $likes=0;
        $dislikes=0;
        $comments=0;
        $tag_str="";
        $cat_id=0;
        $audio_lang="en";
        $duration="n/a";
        $definition="sd";

          $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&fields=items(id,snippet(title,publishedAt,channelId,channelTitle,description,thumbnails,tags,categoryId,defaultAudioLanguage),statistics(viewCount,likeCount,dislikeCount,commentCount),contentDetails(duration,definition))&id='.$inputId.'&key='.$apiKey;

          $json_data = file_get_contents($url);
          if ($json_data === false) {
             echo "error fetching video information, check back and try again!";
            return false;
         } 
         $data = json_decode($json_data,true);
          foreach ($data['items'] as $k => $v) {
           if(isset($v['id']))$id = $v['id'];
               // foreach ($v['snippet']['tags'] as $key => $value) {
               //   echo $value.', ';
               // }
               if (!empty($v['snippet']['channelId'])) $ch_id=$v['snippet']['channelId'];
               if (!empty($v['snippet']['channelTitle'])) $ch_title=$v['snippet']['channelTitle'];

               if (!empty($v['snippet']['title'])) {
                  $title=$v['snippet']['title'];
               }
               if (!empty($v['snippet']['description'])) {
                  $desc=$v['snippet']['description'];
               }
               if (!empty($v['snippet']['publishedAt'])) {
                  $date=$v['snippet']['publishedAt'];
               }
               if (!empty($v['snippet']['thumbnails'])) {
                  $thumb=$v['snippet']['thumbnails']['medium']['url'];
               }
            if(isset($v['snippet']['tags'])){
               if (!empty($v['snippet']['tags'])) {
                 $tag_str =  implode(', ',array_map_assoc(function($k,$v){return "$v";},$v['snippet']['tags']));
               }
            }
            if (isset($v['statistics']['viewCount']))$viewCount = $v['statistics']['viewCount'];
            if (isset($v['statistics']['likeCount']))$likes = $v['statistics']['likeCount'];
          if (isset($v['statistics']['dislikeCount']))$dislikes = $v['statistics']['dislikeCount'];
          if (isset($v['statistics']['commentCount']))$comments = $v['statistics']['commentCount'];

            if (isset($v['contentDetails']['duration']))$duration = $v['contentDetails']['duration'];
            if (isset($v['contentDetails']['definition']))$definition = $v['contentDetails']['definition'];

            if (isset($v['snippet']['categoryId']))$cat_id = $v['snippet']['categoryId'];
            if (isset($v['snippet']['defaultAudioLanguage']))$audio_lang = $v['snippet']['defaultAudioLanguage'];

          }
          // one video prepared now apply algo
          if (applyAlgo($viewCount,$likes,$dislikes)) {
            try{
               insertChannel_id_title($ch_id,"",$ch_title);
             }
            catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            if(insertVideo($title,$id,$ch_id,$date,$thumb,$desc,$viewCount,$likes,$dislikes,$comments,$ch_title, $cat_id, $audio_lang, $tag_str, $duration, $definition)){
              if ($shouldAuth){
                  echo '<br />'.$title.'<br />';
                  echo "<br />Video qualified and fetched successfully.<br />";
              }
              return true;
            }
          }else {
            if ($shouldAuth)echo 'Video couldnt qualify as per algo, try with different video.';
          }
}

function processVideos($videos,$channelId){
  $max_records=50;
  $apiKey = 'AIzaSyB5YybB0lb_NYXXNCekzrV3bnFmy52E0CI';
  if(!empty($videos)){
    $c=0;
    $e=0;
    //first insert channel id and it's title in channels table
    try{
     insertChannel_id_title($channelId,"", $videos[0]['ch_title']);
      echo '<br />'.$videos[0]['ch_title'];
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    foreach ($videos as $key => $value) {
        if (checkIfExists($value['id'])) {
          echo $value['id']." already exists <br />";
          continue;
        }
        $title = $value['title'];
        $id = $value['id'];
        $date = $value['date'];
        $thumb = $value['thumb'];
        $desc = $value['desc'];
        $ch_title = $value['ch_title'];
        $viewCount=0;
        $likes=0;
        $dislikes=0;
        $comments=0;
        $tag_str="";
        $cat_id=0;
        $audio_lang="en";
        $duration="n/a";
        $definition="sd";

          $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&fields=items(snippet(tags,categoryId,defaultAudioLanguage),statistics(viewCount,likeCount,dislikeCount,commentCount),contentDetails(duration,definition))&id='.$value['id'].'&key='.$apiKey;

          $json_data = @file_get_contents($url);
          if ($json_data === false) {
             echo "error fetching video information, check back and try again!";
            return false;
         } 
         $data = json_decode($json_data,true);
          foreach ($data['items'] as $k => $v) {
           if(isset($v['snippet']['tags'])){
               // foreach ($v['snippet']['tags'] as $key => $value) {
               //   echo $value.', ';
               // }
               if (!empty($v['snippet']['tags'])) {
                 $tag_str =  implode(', ',array_map_assoc(function($k,$v){return "$v";},$v['snippet']['tags']));
               }
            }
            if (isset($v['statistics']['viewCount'])) {
                $viewCount = $v['statistics']['viewCount'];
                $likes = $v['statistics']['likeCount'];
                $dislikes = $v['statistics']['dislikeCount'];
                $comments = $v['statistics']['commentCount'];
            }

            if (isset($v['contentDetails']['duration']))$duration = $v['contentDetails']['duration'];
          if (isset($v['contentDetails']['definition']))$definition = $v['contentDetails']['definition'];

            if (isset($v['snippet']['categoryId']))$cat_id = $v['snippet']['categoryId'];
            if (isset($v['snippet']['defaultAudioLanguage']))$audio_lang = $v['snippet']['defaultAudioLanguage'];

          }
          // one video prepared now apply algo
          if (applyAlgo($viewCount,$likes,$dislikes)) {
            if(insertVideo($title,$id,$channelId,$date,$thumb,$desc,$viewCount,$likes,$dislikes,$comments,$ch_title, $cat_id, $audio_lang, $tag_str, $duration, $definition))++$c;
          }else{
            ++$e;
          }
    }
        echo $c." videos inserted.<br />";
        echo $e." videos did not met creteria.<br />";
  }else {
    return false;
  }
}
/////
function DataUpdater(){

        $title = "";
        $id = "";
        $date = "";
        $thumb = "";
        $desc = "";
        $ch_id = "";
        $ch_title = "";
        $viewCount=0;
        $likes=0;
        $dislikes=0;
        $comments=0;
        $tag_str="";
        $cat_id=0;
        $audio_lang="en";
        $duration="n/a";
        $definition="sd";

        //counters
        $insertcounter=0;
        $updatecounter=0;
        $deletecounter=0;



 //first get all channel ids
  $channels = getAllChannels();
  foreach ($channels as $channel) {
    $i=0;
    $channelId =  trim($channel['channel_id']);
    $localvids = getAllvideosChannelId($channelId);
    $temparray = array();

    $videos = fetchvideos($channelId);

    foreach ($videos as $vid) {
      array_push($temparray, $vid['id']);
    }

    foreach ($localvids as $localv) {
      if (!in_array($localv['vid_id'], $temparray)){
          if (deleteVideo($localv['vid_id']))++$deletecounter;
      }
    }


    foreach ($videos as $vid) {
      $id = $vid['id'];
      if(checkIfExists($id)){
        //update it's info
        $info = getlatestInfovideo($id);
        if (empty($info))continue;
        foreach ($info['items'] as $k => $v) {
          if (isset($v['statistics']['viewCount']))$viewCount = $v['statistics']['viewCount'];
          if (isset($v['statistics']['likeCount']))$likes = $v['statistics']['likeCount'];
          if (isset($v['statistics']['dislikeCount']))$dislikes = $v['statistics']['dislikeCount'];
          if (isset($v['statistics']['commentCount']))$comments = $v['statistics']['commentCount'];
          // next step is most important
          if (!applyAlgo($viewCount,$likes,$dislikes)) {
             //video doesn't qualify any more so delete it
            if (deleteVideo($id))++$deletecounter;
          }else{
            //video still qualify hence, update it's info
            $title=$v['snippet']['title'];
            $desc=$v['snippet']['description'];
            $thumb=$v['snippet']['thumbnails']['medium']['url'];
            $ch_title=$v['snippet']['channelTitle'];
            // if(isset($v['snippet']['tags'])){
            //    if (!empty($v['snippet']['tags'])) {
            //      $tag_str =  implode(', ',array_map_assoc(function($k,$v){return "$v";},$v['snippet']['tags']));
            //    }
            // }
            try {
              if (update_info($id,$title,$desc,$thumb,$ch_title,$viewCount,$likes,$dislikes,$comments))$updatecounter++;              
            } catch (Exception $e) {
              
            }
          }
        }
      }else{
        //new video, apply algo, then insert
        try {
          if (processSingleVideo($id,false))++$insertcounter;
        } catch (Exception $e) {
          
        }
      }
    }
    
  }
        echo 'inserted = '.$insertcounter.'<br />';
        echo 'updated = '.$updatecounter.'<br />';
        echo 'deleted = '.$deletecounter.'<br />';
}

function update_info($id="",$title="",$desc="",$thumb="",$ch_title="",$viewCount=0,$likes=0,$dislikes=0,$comments=0){
       global $db;
       $id =trim($db->string_prep($id));
       $title =trim($db->string_prep($title));
       $desc =trim($db->string_prep($desc));
       $thumb =trim($db->string_prep($thumb));
       $ch_title =trim($db->string_prep($ch_title));
       $viewCount =trim($db->string_prep($viewCount));
       $likes =trim($db->string_prep($likes));
       $dislikes =trim($db->string_prep($dislikes));
       $comments =trim($db->string_prep($comments));
       $sql="UPDATE vids SET vid_title='{$title}', vid_desc='{$desc}', vid_thumb='{$thumb}', vid_ch_title='{$ch_title}', vid_viewCount={$viewCount}, vid_likes={$likes}, vid_dislikes={$dislikes}, vid_comments={$comments} WHERE vid_id='{$id}' LIMIT 1";
     $result = $db->perform_query($sql);
     if ($db->get_affected_rows()==1) {         
        return true;      
     }else {
        return false;
     }         
}

function deleteVideo($id=""){
           global $db;
           $sanitized_id = trim($db->string_prep($id));
            $query = "DELETE FROM vids WHERE vid_id='{$sanitized_id}' LIMIT 1";
           $result = $db->perform_query($query);
           if ($db->get_affected_rows()==1) {  
                return true;           
           }      
           return false;
    }

function getlatestInfovideo($inputId=""){
    $apiKey = 'AIzaSyB5YybB0lb_NYXXNCekzrV3bnFmy52E0CI';
    $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&fields=items(snippet(title,channelTitle,description,thumbnails,tags,categoryId,defaultAudioLanguage),statistics(viewCount,likeCount,dislikeCount,commentCount))&id='.$inputId.'&key='.$apiKey;
    $json_data = file_get_contents($url);
          if ($json_data === false) {
             echo "error fetching video information, check back and try again!";
            return false;
         } 
         $data = json_decode($json_data,true);
    return $data;

}

function fetchInfoVideo($inputId=""){

  $apiKey = 'AIzaSyB5YybB0lb_NYXXNCekzrV3bnFmy52E0CI';

  $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&fields=items(id,snippet(title,publishedAt,channelId,channelTitle,description,thumbnails,tags,categoryId,defaultAudioLanguage),statistics(viewCount,likeCount,dislikeCount,commentCount),contentDetails(duration,definition))&id='.$inputId.'&key='.$apiKey;

          $json_data = file_get_contents($url);
          if ($json_data === false) {
             echo "error fetching video information, check back and try again!";
            return false;
         } 
         $data = json_decode($json_data,true);
         return $data;
}



////

function getAllChannels(){
  global $db;
  $sql="SELECT channel_id FROM channels WHERE status=1";
     $result = $db->perform_query($sql);
     $array = $db->fetchAll($result);
     return $array;
}

function getAllvideosChannelId($chid=""){
  global $db;
  $channelId = trim($db->string_prep($chid));
  $sql="SELECT vid_id FROM vids WHERE vid_channel_id='{$channelId}'";
     $result = $db->perform_query($sql);
     $array = $db->fetchAll($result);
     return $array;
}

function applyAlgo($views, $likes, $dislikes){
  if($likes<=0)$likes=1;
  if($dislikes<=0)$dislikes=1;

  $lx = $views/$likes;
  $dlx = $views/$dislikes;

  if ($views>=20000){
    if($dislikes>$likes){
      //if no. of dislikes are more than likes 
      return false;    
    }else{
      if(($lx*10)<$dlx){
       return true;
     }
    }
  }
  return false;
}

function insertVideo($title="",$id="",$channelId="",$date="",$thumb="",$desc="",$viewCount,$likes,$dislikes,$comments, $ch_title="", $cat_id, $audio_lang="", $tags="", $duration, $definition){
   //insert single video
        global $db;
        $title = trim($db->string_prep($title));
        $channelId = trim($db->string_prep($channelId));
        $id =trim($db->string_prep($id));
        $date  = trim($db->string_prep($date));
        $thumb = trim($db->string_prep($thumb));
        $desc  = trim($db->string_prep($desc));
        $viewCount = trim($db->string_prep($viewCount));
        $likes = trim($db->string_prep($likes));
        $dislikes = trim($db->string_prep($dislikes));
        $comments = trim($db->string_prep($comments));
        $ch_title = trim($db->string_prep($ch_title));
        $cat_id = trim($db->string_prep($cat_id));
        $audio_lang = trim($db->string_prep($audio_lang));
        $tag_str = trim($db->string_prep($tags));
        $duration = trim($db->string_prep($duration));
        $definition = trim($db->string_prep($definition));

        $sql  = "INSERT INTO vids (vid_id, vid_channel_id, vid_title, vid_desc, vid_thumb, vid_date, vid_viewCount, vid_likes, vid_dislikes, vid_comments, vid_ch_title, cat_id, vid_audio_lang, tags, duration, definition) ";
     $sql .= "VALUES ('{$id}', '{$channelId}', '{$title}', '{$desc}', '{$thumb}', '{$date}', {$viewCount}, {$likes}, {$dislikes}, {$comments}, '{$ch_title}', {$cat_id}, '{$audio_lang}', '{$tag_str}', '{$duration}', '{$definition}')";
     $result = $db->perform_query($sql);
     if ($db->get_affected_rows()==1) {
         return true;
     }
  return false;
}

function nice_number_format($number){
  if($number>=1000&&$number<1000000){
    return round(($number/1000), 1)."K";
  }elseif ($number>=1000000&&$number<1000000000) {
    return round(($number/1000000), 1)."M";
  }else if($number>=1000000000){
    return round(($number/1000000000), 1)."B";
  }
  return $number;
}

function getExcerpt($str, $startPos=0, $maxLength=50) {
  if(strlen($str) > $maxLength) {
    $excerpt   = substr($str, $startPos, $maxLength);
    $lastSpace = strrpos($excerpt, ' ');
    $excerpt   = substr($excerpt, 0, $lastSpace);
    $excerpt  .= '...';
  } else {
    $excerpt = $str;
  }
  
  return $excerpt;
}


function timeAgo($time_ago){
    $cur_time   = mktime(true);
    $time_elapsed   = $cur_time - strtotime($time_ago);
    $seconds  = $time_elapsed ;
    $minutes  = round($time_elapsed / 60 );
    $hours    = round($time_elapsed / 3600);
    $days     = round($time_elapsed / 86400 );
    $weeks    = round($time_elapsed / 604800);
    $months   = round($time_elapsed / 2600640 );
    $years    = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
      return "$seconds seconds ago";
    }
    //Minutes
    else if($minutes <=60){
      if($minutes==1){
        return "one minute ago";
      }
      else{
        return "$minutes minutes ago";
      }
    }
    //Hours
    else if($hours <=24){
      if($hours==1){
        return "an hour ago";
      }else{
        return "$hours hours ago";
      }
    }
    //Days
    else if($days <= 7){
      if($days==1){
        return "yesterday";
      }else{
        return "$days days ago";
      }
    }
    //Weeks
    else if($weeks <= 4.3){
      if($weeks==1){
        return "a week ago";
      }else{
        return "$weeks weeks ago";
      }
    }
    //Months
    else if($months <=12){
      if($months==1){
        return "a month ago";
      }else{
        return "$months months ago";
      }
    }
    //Years
    else{
      if($years==1){
        return "one year ago";
      }else{
        return "$years years ago";
      }
    }
}

function array_map_assoc( $callback , $array ){
  $r = array();
  foreach ($array as $key=>$value)
    $r[$key] = $callback($key,$value);
  return $r;
}

function auth_cat($cat_id){
  if ($cat_id==null&&!is_numeric($cat_id))return false;
  $auth = array(1,2,10,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44);
  if ($cat_id<0||$cat_id>50)return false;
  if(in_array($cat_id, $auth))return true;   
  return false;
}

function processDuration($duration){
  if ($duration!=null) {
    $d= substr($duration,2);
    $d= str_replace("H",":",$d);
    $d= str_replace("M",":",$d);
    $d = str_replace("S","",$d);
    return $d;
  }
  return $duration;
}

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

function show_pageviews(){
  global $db;
  $sql="SELECT views FROM stats";
     $result = $db->perform_query($sql);
     $array = $db->fetch_array($result);
     return !empty($array) ? $array['views'] : 0;

}
?>