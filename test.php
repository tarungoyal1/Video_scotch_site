<?php

function array_map_assoc( $callback , $array ){
  $r = array();
  foreach ($array as $key=>$value)
    $r[$key] = $callback($key,$value);
  return $r;
}
$max_records=50;
  $apiKey = 'AIzaSyB5YybB0lb_NYXXNCekzrV3bnFmy52E0CI';
  $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&fields=items(snippet(tags,categoryId,defaultAudioLanguage),statistics(viewCount,likeCount,dislikeCount,commentCount))&id=L4IkaP8jnz8&key='.$apiKey;
          $json_data = file_get_contents($url);
         $data = json_decode($json_data,true);
          foreach ($data['items'] as $k => $v) {
            if(isset($v['snippet']['tags'])){
               // foreach ($v['snippet']['tags'] as $key => $value) {
               //   echo $value.', ';
               // }
               $tag_arr = $v['snippet']['tags'];
               $tag_str =  implode(', ',array_map_assoc(function($k,$v){return "$v";},$tag_arr));
               echo $tag_str;

                // $viewCount = $v['statistics']['viewCount'];
                // $likes = $v['statistics']['likeCount'];
                // $dislikes = $v['statistics']['dislikeCount'];
                // $comments = $v['statistics']['commentCount'];

                // echo "viewCount = ".$v['statistics']['viewCount'].'<br />';
                // echo "likes = ".$v['statistics']['likeCount'].'<br />';
                // echo "dislikes = ".$v['statistics']['dislikeCount'].'<br />';
                // echo "comments = ".$v['statistics']['commentCount'].'<br />------<br />';
            }
            if (isset($v['statistics']['viewCount'])) {
              echo "viewCount = ".$v['statistics']['viewCount'].'<br />';
                 echo "likes = ".$v['statistics']['likeCount'].'<br />';
                 echo "dislikes = ".$v['statistics']['dislikeCount'].'<br />';
                 echo "comments = ".$v['statistics']['commentCount'].'<br />------<br />';
            }
            if (isset($v['snippet']['categoryId'])) {
              echo '<br />'.$v['snippet']['categoryId'];
            }
            if (isset($v['snippet']['defaultAudioLanguage'])) {
              echo '<br />'.$v['snippet']['defaultAudioLanguage'];
            }
          }
?>