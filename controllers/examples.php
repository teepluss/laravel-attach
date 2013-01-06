<?php

class Attach_Examples_Controller extends Controller {

	public $restful = true;
	
	public function get_index()
	{
		return View::make('attach::upload');
	}
	
	public function post_index()
	{	
		$input = 'userfile';
		
		$user_id = 1111;
	
		Attach::inject(array(
			'remote'     => false,
			'subpath'    => function() use ($user_id)
			{
				return 'uploads/'.$user_id;
			},
			'onUpload'   => function($result)
			{
				echo '<pre>'.print_r($result, true).'<pre>';
			},
			'onComplete' => function($results)
			{
				echo '<pre>'.print_r($results, true).'<pre>';
			}
		))->add($input)->upload()->resize();
	}
	
	public function  get_remove()
	{
		$input = 'd55257cc6030f77e450b62c72af4a7f7.jpg';
		
		$user_id = 1111;
	
		Attach::inject(array(
			'remote'     => false,
			'subpath'    => function() use ($user_id)
			{
				return 'uploads/'.$user_id;
			},
			'onRemove'   => function($result)
			{
				echo '<pre>'.print_r($result, true).'<pre>';
			}
		))->open($input)->remove();
	}
	
}