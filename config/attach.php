<?php

return array(
	
	/*
	|--------------------------------------------------------------------------
	| Upload remote transfer.
	|--------------------------------------------------------------------------
	|
	| Attach allow upload with remote url
	| if you upload with "http://..." you need to set remote 'true'
	|
	*/
	
	'remote' => false,
	
	/*
	|--------------------------------------------------------------------------
	| Base path.
	|--------------------------------------------------------------------------
	|
	| Full path to view your image
	| http://...image.jpg
	|
	*/
	
	'base_url' => URL::base(),
	
	/*
	|--------------------------------------------------------------------------
	| Base storage dir.
	|--------------------------------------------------------------------------
	|
	| Base directory to store uploaded files.
	|
	*/

	'base_dir' => path('public'),	
	
	/*
	|--------------------------------------------------------------------------
	| Append sub directory to 'base_dir'
	|--------------------------------------------------------------------------
	|
	| You can append a sub directories to base path
	| this allow you to use 'Closure'.
	|
	*/	
	
	'subpath' => 'uploads',
	
	/*
	|--------------------------------------------------------------------------
	| All scales to resize.
	|--------------------------------------------------------------------------
	|
	| For image uploaded you can resize to 
	| selected or whole of scales.
	|
	*/
	
	'scales' => array(
		'wm' => array(260, 180),
		'wl' => array(300, 200),
		'wx' => array(360, 270),
		'ww' => array(260, 120),
		'ws' => array(160, 120),
		'l'  => array(200, 200),
		'm'  => array(125, 125),
		's'  => array(64, 64),
		'ss' => array(45, 45)
	),
	
	/*
	|--------------------------------------------------------------------------
	| Callback on each file uplaoded.
	|--------------------------------------------------------------------------
	|
	| This should be closure to listen when each file uploaded. 
	|
	*/
	
	'onUpload' => null,
	
	/*
	|--------------------------------------------------------------------------
	| Callback on all files uplaoded.
	|--------------------------------------------------------------------------
	|
	| This should be closure to listen when all files uploaded. 
	|
	*/
	
	'onComplete' => null,
	
	/*
	|--------------------------------------------------------------------------
	| Callback on all files deleted.
	|--------------------------------------------------------------------------
	|
	| This should be closure to listen when file deleted. 
	|
	*/
	
	'onRemove' => null,
	
);