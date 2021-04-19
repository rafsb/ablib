<?php
class Fetch extends Activity 
{

	private static function call( String $url, array $fields=null, $method=EProtocol::GET )
	{
		$c = curl_init();
		curl_setopt( $c, CURLOPT_URL, $url . ( $method==EProtocol::GET && $fields ? "?" . Convert::atoh( $fields ) : "" ) );
		if( $method == EProtocol::POST ) 
		{
			curl_setopt( $c, CURLOPT_POST, 1 );
			curl_setopt( $c, CURLOPT_POSTFIELDS, Convert::atoh( $fields ) );
			curl_setopt( $c, CURLOPT_CUSTOMREQUEST, EProtocol::POST );
		}
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );
		$ret = curl_exec( $c );
		// echo "<pre>"; print_r( curl_getinfo( $c ) );
		curl_close ( $c );
		return $ret;
	}


	public static function query( String $url, Closure $fn=null, $method=EProtocol::GET, array $fields=null )
	{
		if( $url )
		{
			$ret = self::call( $url, $fields, $method );
			return $fn ? $fn( $ret ) : $ret;
		}
		return Core::response( 0, "no URL given" );
	}

	public static function get( String $url, Closure $fn=null, array $fields=null )
	{
		return self::query( $url, $fn, EProtocol::GET, $fields );
	}

	public static function post( String $url, Closure $fn=null, array $fields=null )
	{
		return self::query( $url, $fn, EProtocol::POST, $fields );
	}

}