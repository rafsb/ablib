<?php
class Themes extends Activity
{
	public function get(String $theme = null)
	{
        $theme = $theme ? $theme : Request::in("theme");
        $theme = $theme ? $theme : "light";
        $path = IO::root("assets/themes/$theme.theme");
        if(is_file($path)) return IO::read($path);
        else return [];
    }
  
  	public static function ls(){
      	return IO::files("assets/themes");
  	}
    
}	