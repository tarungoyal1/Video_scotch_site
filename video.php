<?php
class Video {
	public $id;
	public $vid_id;
	public $vid_channel_id;
	public $vid_title;
	public $vid_desc;
	public $vid_thumb;
	public $vid_date;
	public $vid_viewCount;
	public $vid_likes;
    public $vid_dislikes;
	public $vid_comments;
	public $vid_ch_title;
	public $duration;


	public static function show_all_videos($page=0, $cat=0, $sort='a'){
      global $db;
      $LIMIT = 50;
	  $page=$page*$LIMIT;
     if($page==0&&$cat==0){
     	if ($sort=='mv')$sql="SELECT * FROM vids ORDER BY vid_viewCount DESC LIMIT {$LIMIT}";
     	else if($sort=='ml') $sql="SELECT * FROM vids ORDER BY vid_likes DESC LIMIT {$LIMIT}";
     	else $sql="SELECT * FROM vids ORDER BY vid_date DESC LIMIT {$LIMIT}";
     }
     elseif ($page!=0&&$cat==0){
     	if ($sort=='mv')$sql="SELECT * FROM vids ORDER BY vid_viewCount DESC LIMIT {$LIMIT} OFFSET {$page}";
     	else if($sort=='ml') $sql="SELECT * FROM vids ORDER BY vid_likes DESC LIMIT {$LIMIT} OFFSET {$page}";
     	else $sql="SELECT * FROM vids ORDER BY vid_date DESC LIMIT {$LIMIT} OFFSET {$page}";
     }
     else if ($page==0&&$cat!=0){
     	if ($sort=='mv')$sql="SELECT * FROM vids WHERE cat_id={$cat} ORDER BY vid_viewCount DESC LIMIT {$LIMIT}";
     	else if($sort=='ml')$sql="SELECT * FROM vids WHERE cat_id={$cat} ORDER BY vid_likes DESC LIMIT {$LIMIT}";
     	else $sql="SELECT * FROM vids WHERE cat_id={$cat} ORDER BY vid_date DESC LIMIT {$LIMIT}";
     }
     else {
     	if ($sort=='mv')$sql="SELECT * FROM vids WHERE cat_id={$cat} ORDER BY vid_viewCount DESC LIMIT 10 OFFSET {$page}";
     	else if($sort=='ml')$sql="SELECT * FROM vids WHERE cat_id={$cat} ORDER BY vid_likes DESC LIMIT 10 OFFSET {$page}";
     	else $sql="SELECT * FROM vids WHERE cat_id={$cat} ORDER BY vid_date DESC LIMIT 10 OFFSET {$page}";
     }
     $array = self::find_by_sql($sql); 
     return $array;
     // return !empty($array) ? $array : null;
 }

	public static function show_videos_of_channel($page=0,$id="", $sort='a'){
      global $db;
	  $LIMIT = 50;
	  $page=$page*$LIMIT;
	  $id =trim($db->string_prep($id));
	  $page =trim($db->string_prep($page));
	  $sort =trim($db->string_prep($sort));
	  if($page==0){
	  	if ($sort=='mv')$sql="SELECT * FROM vids WHERE vid_channel_id='{$id}' ORDER BY vid_viewCount DESC LIMIT {$LIMIT}";
	  	else if($sort=='ml')$sql="SELECT * FROM vids WHERE vid_channel_id='{$id}' ORDER BY vid_likes DESC LIMIT {$LIMIT}";
	  	else $sql="SELECT * FROM vids WHERE vid_channel_id='{$id}' ORDER BY vid_date DESC LIMIT {$LIMIT}";
	  }
	  if($page!=0){
	  	if ($sort=='mv')$sql="SELECT * FROM vids WHERE vid_channel_id='{$id}' ORDER BY vid_viewCount DESC LIMIT {$LIMIT} OFFSET {$page}";
	  	else if($sort=='ml')$sql="SELECT * FROM vids WHERE vid_channel_id='{$id}' ORDER BY vid_likes DESC LIMIT {$LIMIT} OFFSET {$page}";	  	
	  	else $sql="SELECT * FROM vids WHERE vid_channel_id='{$id}' ORDER BY vid_date DESC LIMIT {$LIMIT} OFFSET {$page}";
	  }
     $array = self::find_by_sql($sql); 
     return $array;
     // return !empty($array) ? $array : null;
 }

 function suggestMoreVideosofId($id="",$curr_vid=""){
  global $db;
  $vid_channel_id =trim($db->string_prep($id));
  $sql="SELECT * FROM vids WHERE vid_channel_id='{$vid_channel_id}' AND vid_id!='{$curr_vid}' ORDER BY vid_viewCount DESC LIMIT 50";
     $array = self::find_by_sql($sql); 
     return !empty($array) ? $array : null;

}

	public static function find_by_sql($sql="") {
		global $db;
		$result = $db->perform_query($sql);
		$object_array =array();
		 while ($row = $db->fetch_array($result)) {
		 	$object_array[] = self::instantiate($row);
		 }
		return $object_array;
	}
	private static function instantiate($record) {
		$object = new self;
		foreach($record as $attribute=>$value) {
			if ($object->has_attribute($attribute)) {
				$object->$attribute = $value;				
			}
		}
		return $object;
	}
 	private function has_attribute($attribute) {
 		$object_vars = get_object_vars($this);
 		return array_key_exists($attribute, $object_vars);
 	}
}
?>