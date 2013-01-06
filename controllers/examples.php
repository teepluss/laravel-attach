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
		
		$input = 'http://farm9.staticflickr.com/8348/8248740315_2299c940a9.jpg';
		
		$user_id = 1111;
	
		$attach = Attach::inject(array(
			'remote'     => preg_match('|^http|', $input) ? true : false,
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
		$input = '4b6faa1913c3402b28f4bf13a1fe6b92.jpg';
		
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