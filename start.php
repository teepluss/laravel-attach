<?php

/**
 * Mapping path.
 */
Autoloader::map(array(
	'WideImage' => path('bundle').'attach/vendor/wideimage/WideImage.php',
	'Attach'    => path('bundle').'attach/attach.php'
));


/**
 * Auto route example to url /attach_examples.
 */
Route::any('attach_examples/(:any?)', array(
	'as'       => 'attach_examples',
	'uses'     => 'attach::examples@(:1)',
	'defaults' => 'index'
));