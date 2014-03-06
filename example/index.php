<?php

namespace simple;
use Exception;

require '../src/simple/SimpleSocialVideo.php';

$valid_url_youtube = 'http://www.youtube.com/watch?v=wGvZWPOpZAE';
$valid_url_vimeo = 'http://vimeo.com/718489';

try {
	
	//$video = new SimpleSocialVideo($valid_url_youtube);
	$video = new SimpleSocialVideo($valid_url_vimeo);
	
	echo '<p>Provider : '.$video->getProvider().'</p>';
	echo '<p>Video ID : '.$video->getVideoId().'</p>';
	echo '<p>Video title : '.$video->getVideoTitle().'</p>';

	echo '<p>Video : '.$video->iframePlayer(800, 600, true, false, true).'</p>';
	
	echo '<p>Small Thumb : <img src="'.$video->getThumbnailUrl().'" /></p>';
	echo '<p>Medium Thumb : <img src="'.$video->getThumbnailUrl('medium').'" /></p>';
	echo '<p>Large Thumb : <img src="'.$video->getThumbnailUrl('large').'" /></p>';
	echo '<p>Max Thumb : <img src="'.$video->getThumbnailUrl('max').'" /></p>';


} catch (Exception $e) {
	echo '<strong>'.$e->getMessage().'</strong>';
}
