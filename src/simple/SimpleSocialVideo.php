<?php
/**
 * @package		SimpleSocialVideo class
 * @version		1.0
 * @author		Julien HAY <jules.hay@gmail.com>
 * @license		This software is licensed under the MIT license: http://opensource.org/licenses/MIT
 * @copyright	Julien HAY
 *
 */

namespace simple;
use Exception;

/**
 * Class SimpleSocialVideo
 * Video class for Youtube & vimeo
 * @package SimpleSocialVideo
 *
 */
class SimpleSocialVideo {

	/**
	 * @var string Video URL
	 *
	 */
	protected $video_url;

	/**
	 * @var string Video ID
	 *
	 */
	protected $video_id;

	/**
	 * @var string Video provider (youtube.com, youtu.be or vimeo)
	 *
	 */
	protected $provider;

	/**
	 * @var string Data from API JSON
	 *
	 * Vimeo : http://vimeo.com/api/v2/video/{ID}.json
	 * Youtube : http://gdata.youtube.com/feeds/api/videos/{ID}?v=2&alt=json
	 */
	protected $data_api;


	function __construct($url_video)
	{
		if(!filter_var($url_video, FILTER_VALIDATE_URL)){
		    throw new Exception('URL is Not valid !');
		}

		$this->video_url = $url_video;
		$this->provider = $this->searchProvider();
		$this->video_id = $this->defineVideoId();
		$this->data_api = $this->downloadJsonContent();
	}

	/**
	 * Get thumbnail of the video
	 *
	 * @param string $type 		Size of thumbnail : small (default), medium, large, max (only youtube)
	 *
	 * @return string
	 **/
	public function getThumbnailUrl($type = 'small')
	{
		if($this->provider =='youtube.com' || $this->provider == 'youtu.be')
		{
			switch ($type) {
				case 'small': 	$img = 'default';			break;
				case 'medium': 	$img = 'hqdefault';			break;
				case 'large': 	$img = 'sddefault';			break;
				case 'max':		$img = 'maxresdefault';		break;
			}

			return 'http://img.youtube.com/vi/'.$this->video_id.'/'.$img.'.jpg';
		}
		elseif($this->provider == 'vimeo.com')
		{
			switch ($type) {
				case 'small': 	$img = 'thumbnail_small';		break;
				case 'medium': 	$img = 'thumbnail_medium';		break;
				case 'large': 	$img = 'thumbnail_large';		break;
				case 'max': 	$img = 'thumbnail_large';		break;
			}

			$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.$this->video_id.'.php'));
			return $hash[0][$img];
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
		$domain = parse_url($this->video_url,PHP_URL_HOST);
		$provider = '';

		if($domain=='www.youtube.com' OR $domain=='youtube.com' OR $domain == 'youtu.be') {
		    $provider = "youtube.com";
		}
		elseif($domain == 'youtu.be') {
			$provider = "youtube.be";
		}
		elseif($domain == 'www.vimeo.com' || $domain == 'vimeo.com') {
			$provider = "vimeo.com";
		}
		else {
			 throw new Exception('Video provider not found !');
		}

		return $provider;
	}


	/**
	 * Define video ID
	 *
	 * @return string
	 **/
	private function defineVideoId()
	{
		if($this->provider =='youtube.com')
		{
		    if($querystring = parse_url($this->video_url, PHP_URL_QUERY))
		    {  
		        parse_str($querystring);
		        $id = $v;
		    }
		    else return false;
		}
		elseif($this->provider == 'youtu.be')
		{
		    $v = str_replace('/','', parse_url($this->video_url, PHP_URL_PATH));
		    $id = $v;
		}
		elseif($this->provider == 'vimeo.com')
		{
			/*$url_dec = parse_url($this->video_url);
			$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".substr($url_dec['path'], 1).".php"));
			$id = substr($url_dec['path'], 1);*/
		}
		else {
			 throw new Exception('Video ID not found !');
		}

		return $id;
	}

	/**
	 * Download json content
	 *
	 * @return string
	 **/
	private function downloadJsonContent()
	{
		if($this->provider =='youtube.com' || $this->provider =='youtu.be')
		{
		    $content = @file_get_contents('http://gdata.youtube.com/feeds/api/videos/'.$this->video_id.'?v=2&alt=json');	
		}
		elseif($this->provider == 'vimeo.com')
		{
			$content = file_get_contents('http://vimeo.com/api/v2/video/'.$this->video_id.'.json');			
		}

		$json = json_decode($content);

		return $json;
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

	/**
	 * Get video ID
	 *
	 * @return string
	 **/
	public function getVideoId()
	{
		return $this->video_id;
	}

	/**
	 * Check if video exist on the provider
	 *
	 * @return boolean
	 **/
	private function checkExist()
	{
		// TODO : To finish
		$res = parse_url($this->url);
		if ( preg_match( "/\/watch/" , $res["path"]  ) ){
		    echo "found video\n ";
		}

		return false;
	}

	/**
	 * Get video title
	 *
	 * @return string
	 **/
	public function getVideoTitle()
	{
		if($this->provider =='youtube.com' || $this->provider =='youtu.be')
			return $this->data_api->entry->title->{'$t'};
		
		// TODO : return title for vimeo
		elseif($this->provider == 'vimeo.com')
			return 'title';
	}

	/**
	 * Display video player
	 *
	 * @return string -> provider iframe
	 **/
	public function iframePlayer()
	{
		// TODO : Select size output
		if($this->provider =='youtube.com' || $this->provider =='youtu.be')
		{
		    $content = '<iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/'.$this->video_id.'?rel=0" frameborder="0" allowfullscreen></iframe>';
		}
		elseif($this->provider == 'vimeo.com')
		{
			$content = '<iframe src="//player.vimeo.com/video/'.$this->video_id.'" width="250" height="141" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';		
		}

		return $content;
	}

}

