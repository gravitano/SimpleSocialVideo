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
			$provider = "youtu.be";
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
			$url_dec = parse_url($this->video_url);
			$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".substr($url_dec['path'], 1).".php"));
			$id = substr($url_dec['path'], 1);
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
		
		elseif($this->provider == 'vimeo.com')
			return $this->data_api[0]->title;
	}

	/**
	 * Construct url Parameters ( ex : ?value=1&value2=true )
	 *
	 * @param Array $params 		Params list key value
	 *
	 * @return string
	 **/
	private function constructUrlParams($params)
	{
		$ret = '';
		$first = true;
		foreach ($params as $key => $value) {
			if(!$first) $ret .= '&amp;';
			$ret .= $key.'='.$value;
			$first = false;
		}

		return ($ret != '') ? '?'.$ret : '';
	}

	/**
	 * Display video player
	 *
	 * @param string $width 			Player width (default : 640)
	 * @param string $height 			Video height (default : 360)
	 * @param boolean $autoplay 		Autoplay Video, Youtube autoplay don't work on Firefox (default : false)
	 * @param boolean $suggest 			Show suggested videos when the video finishes (default : false)
	 * @param boolean $privacy 			Enable privacy-enhanced mode (default : true)
	 *
	 * @return string -> provider iframe
	 **/
	public function iframePlayer($width = 640, $height = 360, $autoplay = false, $suggest = false, $privacy = true)
	{
		$url_params = array();

		if($autoplay) $url_params['autoplay'] = 1;

		if($this->provider =='youtube.com' || $this->provider =='youtu.be')
		{
		    if(!$suggest) $url_params['rel'] = 0;

		    $privacy = ($privacy) ? '-nocookie' : '';
		    $content = '<iframe width="'.$width.'" height="'.$height.'" src="//www.youtube'.$privacy.'.com/embed/'.$this->video_id.$this->constructUrlParams($url_params).'" frameborder="0" allowfullscreen></iframe>';
		}
		elseif($this->provider == 'vimeo.com')
		{
			$content = '<iframe src="//player.vimeo.com/video/'.$this->video_id.$this->constructUrlParams($url_params).'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';		
		}

		return $content;
	}

}
