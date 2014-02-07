<?php

namespace simple;
use Exception;

require '../src/SimpleVideo.php';

$valid_url_youtube = 'http://www.youtube.com/watch?v=wGvZWPOpZAE';
$valid_url_vimeo = 'http://vimeo.com/718489';

try {
	
	$video = new SimpleVideo($valid_url_youtube);
	//$video = new SimpleVideo($valid_url_vimeo);
	
	echo '<p>Provider : '.$video->getProvider().'</p>';

	echo '<p><img src="'.$video->getBigThumbnailUrl().'" /></p>';

} catch (Exception $e) {
	echo '<strong>'.$e->getMessage().'</strong>';
}
