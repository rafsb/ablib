<?php
class Twitter extends Activity
{
	private static function auth_key()
	{
		$cfg = App::config("twitter_api");
		return base64_encode($cfg["api_key"] . ":" . $cfg["api_secret"]);
	}

	protected static function get_token()
	{
		$cfg = App::config("twitter_api");
		$c = curl_init($cfg["token_uri"]);	
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($c, CURLOPT_VERBOSE, 1);
		// curl_setopt($c, CURLOPT_HEADER, 1);
		curl_setopt($c, CURLOPT_HTTPHEADER, [ "Authorization: Basic " .static::auth_key() ]);
		$r = Convert::json(curl_exec($c));
		curl_close($c);
		if(isset($r->access_token) && strlen($r->access_token) > 1) return $r->access_token;
		else return 0;
	}

	protected static function query(String $query, $toDate=null, $fromDate=null, $maxresults=10, $api="30day")
	{
		$args_query = [
			"query" => $query
			, "maxResults" => min($maxresults, 100)
		];
		
		$ref_date = date("YmdH") . "00";
		
		if($toDate){
			if(strlen($toDate) < strlen($ref_date)) $toDate = $toDate . substr($ref_date, strlen($toDate), strlen($ref_date));
			$args_query["toDate"] = $toDate;
		}
		if($fromDate){
			if(strlen($fromDate) < strlen($ref_date)) $fromDate = $fromDate . substr($ref_date, strlen($fromDate), strlen($ref_date));
			$args_query["fromDate"] = $fromDate;
		}

		$cfg = App::config("twitter_api_$api");
		// print_r($cfg);die;
		$r = null;
		$count = 0;
		$final = [];

		do 
		{
			if($r)
			{
				if(isset($r->next)) $args_query["next"] = $r->next;
				else unset($args_query["next"]);
			}
			$c = curl_init($cfg["tweets_uri"] . "?" . http_build_query($args_query));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_HTTPHEADER, [ "Authorization: Bearer " . static::get_token() ]);
			$r = Convert::json(curl_exec($c));
			curl_close($c);
			if(isset($r->results)) $final = array_merge($final, $r->results);
			// echo sizeof($r->results);die;
			$count += $r->results ? sizeof($r->results) : $maxresults;

		} while((!isset($r->error) || isset($r->next)) && $count < $maxresults);
		
		// print_r($final);die;

		return $final;
	}

	public static function client(String $query, $todate=null, $fromdate=null, $limit=10)
	{
		$final = [];
		$query = static::query($query, $todate, $fromdate, $limit);
		$clean_array = [];

		// print_r($query); die;
		
		foreach($query as $k=>$twit)
		{

			// $referencetime = new DateTime($twit->created_at);
			if(!in_array($twit->id, $clean_array))
			{
				$final[] = Convert::atoo([
					"time" => (new DateTime($twit->created_at))->format("YmdHi")
					// "time" => $referencetime->format("YmdHi")
					, "tuid" => $twit->id
					, "text" => isset($twit->extended_tweet) && isset($twit->extended_tweet->full_text) ? $twit->extended_tweet->full_text : $twit->text
					, "hashtags" => $twit->entities->hashtags
					, "media" => [
						"uri" => isset($twit->entities->media) ? $twit->entities->media[0]->expanded_url : null
						, "type" => isset($twit->entities->media) ? $twit->entities->media[0]->type : null
					]
					, "user" => [
						"uuid" => $twit->user->id
						, "name" => $twit->user->screen_name . "/" . $twit->user->name
						, "thumb" => $twit->user->profile_image_url_https
					]
				]);
				$clean_array[] = $twit->id;
			}
		}
		return Convert::json($final);
	}

	public static function text(String $query, $todate=null, $fromdate=null, $limit=100, $showdate = false)
	{
		$tmp = (array)Convert::json(self::client($query, $todate, $fromdate, $limit));
		$str = "";
		foreach($tmp as $twit) $str .= ($showdate ? $twit->time . " " : "") . str_replace("\n", " ", $twit->text) . PHP_EOL;
		return $str;
	}

}

// Vector::each($r->results, function($twit) use (&$final){
// $referencetime = new DateTime($twit->created_at);
// $filename = $twit->id;
// $buckdir = IO::root("var/bucket");
// if(!is_dir($buckdir)) IO::mkd($buckdir);
// $folder = IO::root("var/twits/0/" . $referencetime->format("Y/m/d/H") . "/");
// if(!is_dir($folder)) IO::mkd($folder);
// $tmp = [
// 	"time" => $referencetime->format("YmdHi")
// 	, "tuid" => $twit->id
// 	, "text" => 
// 	$twit->text
// 	, "hashtags" => $twit->entities->hashtags
// 	, "media" => [
// 		"uri" => isset($twit->entities->media) ? $twit->entities->media[0]->expanded_url : null
// 		, "type" => isset($twit->entities->media) ? $twit->entities->media[0]->type : null
// 	]
// 	, "user" => [
// 		"uuid" => $twit->user->id
// 		, "name" => $twit->user->screen_name . "/" . $twit->user->name
// 		, "thumb" => $twit->user->profile_image_url_https
// 	]
// ];
// $final[] = $tmp;
// if(is_link($buckdir . DS . $filename)) link($buckdir . DS . $filename, $folder . DS . $filename);
// if(is_file($folder . DS . $filename) || is_link($folder . DS . $filename)) return;
// IO::jin($folder . DS . $filename, $tmp);
// link($folder . DS . $filename, $buckdir . DS . $filename);
// });