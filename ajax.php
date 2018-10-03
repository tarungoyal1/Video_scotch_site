<?php
if ($_SERVER['REQUEST_METHOD']=="GET") {
	exit("no");
}
$search_q = $_POST['qu'];
if (!isset($search_q) || empty($search_q)) {
	exit("sorry");
	die();
}
require ( "sphinxapi.php" );
require 'config.php';
require 'database.php';
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

$host = "127.0.0.1";
$port = "9312";
$cl = new SphinxClient();
$cl->SetServer ($host, $port);
$cl->SetArrayResult (true);
$cl->SetLimits(0, 50);
// $cl->SetFieldWeights(array('vid_title' => 10, 'vid_viewCount' => 40));
$cl->SetIndexWeights(array('vid_viewCount' => 5000,'vid_title' => 50, 'vid_desc' => 10, 'tags'=>100));
$cl->SetMatchMode(SPH_MATCH_EXTENDED2);
$cl->SetRankingMode(SPH_RANK_BM25);
$cl->SetSortMode(SPH_SORT_EXTENDED, '@relevance DESC');
if (isset($search_q)) {
	$result = $cl->Query ("{$search_q}", "test1");
	echo $cl->GetLastError() ;
	if (!isset($result['matches'])) {
        $search_q = str_replace(' ', '', $search_q);
        $result = $cl->Query ("{$search_q}", "test1");
		echo $cl->GetLastError() ;
    }
	if (isset($result['matches'])) {
			$array = $result['matches'];
			echo '<br />'.sizeof($array).' videos found :  <br /><br />';

			if (!empty($array)) {
				$view = array();
				foreach ($array as $key => $row){
    				$view[$key] = $row['attrs']['vid_viewcount'];
				}
                array_multisort($view, SORT_DESC, $array);
				foreach ($array as $value) { // foreach 1
					$query = "SELECT * FROM vids WHERE id={$value['id']}";
					$content = Video::find_by_sql($query);
					foreach ($content as $r) { // foreach 2
						$doc = array("$r->vid_title", "$r->vid_desc");
						$options = array(
					        'before_match'          => '<b>',
					        'after_match'           => '</b>',
					        'chunk_separator'       => ' ... ',
					        'limit'                 => 100,
					        'around'                => 10,
			           );
			            $res = $cl->BuildExcerpts($doc, 'test1', "{$search_q}", $options);
						
						$out='<div class="search_video_item">';
						$out.='<a href="/w.php?v='.$r->vid_id.'" target="_blank"><img width="196" height="110" src="'.$r->vid_thumb.'" /></a>';
						$out.='<div class="title"><a href="/w.php?v='.$r->vid_id.'" target="_blank" class="vid_link">'.$res[0].'</a></div>';
							$out.='<div class="search_desc">'.$res[1].'</div>';
							$out.='<div class="search_cname"><a href="/channel.php?id='.$r->vid_channel_id.'" class="vid_ch_title">'.$r->vid_ch_title.'</a></div>';
							$out.='<div class="search_vcount">Views: ';
							if($r->vid_viewCount>=1000000&&$r->vid_viewCount<1000000000)
                      $out .= '<span style="color:#cc181e;">';
                elseif($r->vid_viewCount>=1000000000)$out .= '<span style="color:#167ac6;font-weight: bolder;">';
                else $out .= '<span>';						
							$out .=  nice_number_format($r->vid_viewCount);
							$out .= '</span></div></div>';
							
							// $out.='<div class="cont">'.$res[2].'</div></div>';
						echo $out;	
					} // foreach 2
				}  // foreach 1

				}//end of if (!empty($array))
				else {
					echo "<br /><b>No results for your query</b><br />";
				}
		}//end of if isset($result['matches'])
		else {
					echo "<br /><b>No results for your query</b><br />";
				}
}//end of if isset($search_q)
?>
