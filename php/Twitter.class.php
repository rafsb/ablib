<?php
class Twitter extends Activity
{
	private static function auth_key(){
		$cfg = App::config("twitter_api");
		return base64_encode($cfg["api_key"] . ":" . $cfg["api_secret"]);
	}

	protected static function get_token(){
		$cfg = App::config("twitter_api");
		$c = curl_init($cfg["token_uri"]);	
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($c, CURLOPT_VERBOSE, 1);
		// curl_setopt($c, CURLOPT_HEADER, 1);
		curl_setopt($c, CURLOPT_HTTPHEADER, [ "Authorization: Basic " . self::auth_key() ]);
		$r = Convert::json(curl_exec($c));
		curl_close($c);
		if(isset($r->access_token)&&strlen($r->access_token)>1) return $r->access_token;
		else return 0;
	}

	protected static function query(String $query){
		$cfg = App::config("twitter_api");
		$c = curl_init($cfg["tweets_uri"] . "?" . http_build_query([
			"query" => $query
			, "maxResults" => 100
			, "toDate" => date("YmdH") . "00"
		]));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_HTTPHEADER, [ "Authorization: Bearer " . self::get_token() ]);
		$r = Convert::json(curl_exec($c));
		curl_close($c);
		$final = [];
		if(isset($r->results)){
			Vector::each($r->results, function($twit) use (&$final){
				$referencetime = new DateTime($twit->created_at);
				$filename = $twit->id;
				$buckdir = IO::root("var/bucket");
				if(!is_dir($buckdir)) IO::mkd($buckdir);
				$folder = IO::root("var/twits/0/" . $referencetime->format("Y/m/d/H") . "/");
				if(!is_dir($folder)) IO::mkd($folder);
				$tmp = [
					"time" => $referencetime->format("YmdHi")
					, "tuid" => $twit->id
					, "text" => $twit->text
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
				];
				$final[] = $tmp;
				if(is_link($buckdir . DS . $filename)) link($buckdir . DS . $filename, $folder . DS . $filename);
				if(is_file($folder . DS . $filename) || is_link($folder . DS . $filename)) return;
				IO::jin($folder . DS . $filename, $tmp);
				link($folder . DS . $filename, $buckdir . DS . $filename);
			});
		}
		return $final;
	}

	public static function client(String $query){
		print_r(self::query($query));
	}

}