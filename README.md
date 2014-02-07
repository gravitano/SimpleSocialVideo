SimpleSocialVideo PHP Class
===========================

*Develop by Julien HAY*
*Licensed under the MIT license : http://opensource.org/licenses/MIT*

Usage
------

```php
<?php

namespace simple;
use Exception;

require '../src/SimpleSocialVideo.php';

try {
    
    $video = new SimpleSocialVideo('http://www.youtube.com/watch?v=wGvZWPOpZAE');
    
    echo '<p>Provider : '.$video->getProvider().'</p>';
    
    echo '<p><img src="'.$video->getBigThumbnailUrl().'" /></p>';

} catch (Exception $e) {
    echo '<strong>'.$e->getMessage().'</strong>';
}
```


