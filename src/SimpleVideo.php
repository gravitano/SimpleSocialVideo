<?php
/**
 * @package		SimpleVideo class
 * @version		1.0
 * @author		Julien HAY <jules.hay@gmail.com>
 * @license		This software is licensed under the MIT license: http://opensource.org/licenses/MIT
 * @copyright	Julien HAY
 *
 */

namespace simple;
use Exception;

/**
 * Class SimpleVideo
 * Video class for Youtube & vimeo
 * @package SimpleVideo
 *
 */
class SimpleVideo {

	/**
	 * @var string Video URL
	 *
	 */
	protected $url;

	/**
	 * @var string Video provider (youtube.com, youtube.be or vimeo)
	 *
	 */
	protected $provider;

	function __construct($url_video)
	{
		if(!filter_var($url_video, FILTER_VALIDATE_URL)){
		    throw new Exception('URL is Not valid !');
		    //return false;
		}

		$this->url = $url_video;
		$this->provider = $this->searchProvider();
	}

	/**
	 * Get thumbnail of the video
	 *
	 * @return string
	 **/
	public function getBigThumbnailUrl()
	{
		if($this->provider =='youtube.com')
		{
		    if($querystring=parse_url($this->url,PHP_URL_QUERY))
		    {  
		        parse_str($querystring);
		        if(!empty($v)) return "http://img.youtube.com/vi/$v/0.jpg";
		        else return false;
		    }
		    else return false;
		}
		elseif($this->provider  == 'youtu.be')
		{
		    $v= str_replace('/','', parse_url($this->url, PHP_URL_PATH));
		    return (empty($v)) ? false : "http://img.youtube.com/vi/$v/0.jpg" ;
		}
		else if($this->provider == 'vimeo.com')
		{
			$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".substr($url_dec['path'], 1).".php"));
			return $hash[0]["thumbnail_large"]; 
		}
		else {
			throw new Exception('Unable to find video thumbnail');
		}
	}

	/**
	 * Define video provider (youtube / vimeo)
	 *
	 * @return string
	 **/
	private function searchProvider()
	{
		$domain=parse_url($this->url,PHP_URL_HOST);
		$url_dec=parse_url($this->url);
		$provider = '';

		if($domain=='www.youtube.com' OR $domain=='youtube.com' OR $domain == 'youtu.be')
		{
		    $provider = "youtube.com";
		}
		elseif($domain == 'youtu.be')
		{
			$provider = "youtube.be";
		}
		elseif($domain == 'www.vimeo.com' || $domain == 'vimeo.com'){
			$provider = "vimeo";
		}
		else {
			 throw new Exception('Video provider not found !');
		}

		return $provider;
	}


	/**
	 * Get provider
	 *
	 * @return string
	 **/
	public function getProvider()
	{
		return $this->provider;
	}


}

