laravel-attach
==============

***Uploader with Resize image bundle for Laravel***

## Installation

Install this bundle by running the following CLI command:

	php artisan bundle:install attach
	
Add the following line to application/bundles.php
	
	'attach' => array('auto' => true),
	
Code example in configuration. 

```php
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
	
	'base_url' => URL::base().'/',
	
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
```

## Example Usage 

```php

// $_FILES['userfiles'];
$input = 'userfile';

// OR Remote upload.
		
// $input = 'http://farm9.staticflickr.com/8348/8248740315_2299c940a9.jpg';

$user_id = 1111;

// Inject a config and upload with resize.
$attach = Attach::inject(array(
	// For http upload set remote TRUE.
	'remote'     => preg_match('|^http|', $input) ? true : false,
	
	// Scales to resize.
	'scales' 	 => array(
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
	
	// Path extend to base.
	'subpath'    => function() use ($user_id)
	{
		return 'uploads/'.$user_id;
	},
	
	// On each upload file.
	'onUpload'   => function($result)
	{
		echo '<pre>'.print_r($result, true).'<pre>';
	},
	
	// On complete all upload files.
	'onComplete' => function($results)
	{
		echo '<pre>'.print_r($results, true).'<pre>';
	}
))->add($input)->upload()->resize(); // upload and resize all sizes.

// Specific sizes.
//->add($input)->upload()->resize(array('l', 'm', 's'));

// For upload without resize.
//->add($input)->upload();


// You still can get the results.
/*$attach->onComplete(function($results) 
{
	echo '<pre>'.print_r($results, true).'<pre>';
});*/

// Removal and Resize for a specific file see on example.
```

## Examples

You can run example at:
	
	http://domain.com/attach_examples
	
## Support or Contact

If you have some problem, Contact teepluss@gmail.com 