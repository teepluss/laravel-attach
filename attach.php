<?php
 
class Attach {
	
	/**
	 * Config from a attach.
	 *
	 * @var array
	 */
	public $config;
	
	/**
	 * File input.
	 *
	 * This can be path URL or $_FILES.
	 *
	 * @var mixed
	 */
	private $file;
	
	/**
	 * Original file uploaded result or open file.
	 *
	 * @var array
	 */
	private $master;
	
	/**
	 * Result last file uploaded.
	 *
	 * @var array
	 */
	private $result = array();
	
	/**
	 * Result of all file uplaoded include resized.
	 *
	 * @var array
	 */
	private $results = array();
	
	/**
	 * Create a new attach instance.
	 *
	 * @param	array  $params
	 * @return  void
	 */
	public function __construct($params = array())
	{
		// Get config from file.
		$config = Config::get('attach::attach');
		
		// Merge inject parameters with default config.
		$config = array_merge($config, $params);
		
		// Config to use with process.
		$this->config = $config;
	}

	/**
	 * Inject config.
	 *
	 * @param	array  $params
	 * @return  Attach
	 */
	public static function inject($params = array())
	{	
		static $instance = null;
		if (is_null($instance))
		{	
			$instance = new static($params);	
		}
		else
		{
			// Merge config from existing.
			$instance->config = array_merge($instance->config, $params);
		}
		return $instance;
	}
	
	/**
	 * Add file to process.
	 *
	 * Input can be string URL or $_FILES
	 *
	 * @param	mixed  $file
	 * @return  Attach
	 */
	public function add($file)
	{
		$this->file = $file;
	
		return $this;	
	}
	
	/**
	 * Open the location path.
	 *
	 * $name don't need to include path.
	 *
	 * @param	string	$name
	 * @return  Attach
	 */
	public function open($name)
	{
		$location = $this->path($this->config['base_dir']).$name;
		
		// Generate a result to use as a master file.
		$result = $this->results($location);		
		$this->master = $result;
		
		return $this;
	}
	
	/**
	 * Hashed file name generate.
	 *
	 * Generate a uniqe name to be file name.
	 *
	 * @param	string	$file_name
	 * @return  string
	 */
	protected function name($file_name)
	{
		// Get extension.
		$extension = File::extension($file_name);
		
		return md5(Str::random(30).time()).'.'.$extension;
	}
	
	/**
	 * Find a base directory include appended.
	 *
	 * Destination dir to upload.
	 *
	 * @param	string	$base
	 * @return  string
	 */
	protected function path($base = null)
	{
		$path = $this->config['subpath'];
		
		// Path config can be closure.
		if ($path instanceof Closure)
		{
			return $base.$path().'/';
		}
		
		return $base.$path.'/';
	}
	
	/**
	 * Generate a view link.
	 *
	 * @param	string	$path
	 * @return  string
	 */
	protected function url($path)
	{
		return $this->config['base_url'].$path;
	}
	
	/**
	 * Uplaod a file to destination.
	 *
	 * @return Attach
	 */
	public function upload()
	{
		// Find a base directory include appended.
		$path = $this->path($this->config['base_dir']);
		
		// Method to use uplaod.
		$method = ($this->config['remote'] === true) ? 'do_transfer' : 'do_upload';
		
		// Call a method.
		$result = call_user_func_array(array($this, $method), array($this->file, $path));
		
		// If uploaded set a master add fire a result.
		if ($result !== false)
		{
			$this->master = $result;
			$this->add_result($result);
		}	
		
		// Reset values.
		$this->reset();
		
		return $this;
	}	
	
	/**
	 * Upload from a file input.
	 *
	 * @param	string	$key
	 * @param	string	$path
	 * @return  mixed
	 */
	protected function do_upload($key, $path)
	{
		// Get a file input.
		$file = Input::file($key);
		
		// Generate a file name with extension.
		$file_name = $this->name($file['name']);
		
		if (Input::upload($key, $path, $file_name))
		{
			$upload_path = $path.$file_name;
			return $this->results($upload_path);
		}
		
		return false;
	}
	
	/**
	 * Upload from a remote URL.
	 *
	 * @param	string	$file
	 * @param	string	$path
	 * @return  mixed
	 */
	protected function do_transfer($url, $path)
	{
		// Craete upload structure directory.
		if ( ! is_dir($path))
		{
			mkdir($path, 0777, true);
		}
		
		// Generate a file name with extension.
		$file_name = $this->name($url);
		
		// Get file binary.
		$contents = file_get_contents($url);
		
		// Path to write file.
		$upload_path = $path.$file_name;
		
		if (File::put($upload_path, $contents))
		{
			return $this->results($upload_path);			
		}
		
		return false;
	}
	
	/**
	 * Add a new result uplaoded.
	 *
	 * @return void
	 */
	protected function add_result($result)
	{
		// Fire a result to callback.
		$onUpload = $this->config['onUpload'];
		if ($onUpload instanceof Closure) 
		{
			$onUpload($result);
		}
		
		$this->results[$result['scale']] = $result;
	}
	
	/**
	 * Generate file result format.
	 *
	 * @param	string	$location
	 * @param	string	$scale
	 * @return  array
	 */
	protected function results($location, $scale = null)
	{
		// Scale of original file.
		if (is_null($scale))
		{
			$scale = 'original';
		}
		
		// Try to get size of file.
		$file_size = @filesize($location);
		
		// If cannot get size of file stop processing.
		if (empty($file_size))
		{
			return false;
		}
		
		// Get pathinfo.
		$pathinfo = pathinfo($location);
	
		// Append path without base.
		$path = $this->path();
		
		// Get an file extension.
		$file_extension = $pathinfo['extension'];
		
		// File name without extension.
		$file_name = $pathinfo['filename'];
		
		// Base name include extension.
		$file_base_name = $pathinfo['basename'];
		
		// Append path with file name.
		$file_path = $path.$file_base_name;

		// Mime type.
		$mime = File::mime($file_extension);
		
		// Dimension for image.
		$dimension = null;
		if (preg_match('|image|', $mime))
		{
			$meta = getimagesize($location);			
			$dimension = $meta[0].'x'.$meta[1];
		}
		
		// Master of resized file.
		$master = null;
		if ($scale !== 'original')
		{
			$master = str_replace('_'.$scale, '', $file_name);
		}
		
		return array(
			'scale'          => $scale,
			'master'         => $master,
			'subpath'        => $path,
			'location'       => $location,			
			'file_name'      => $file_name,
			'file_extension' => $file_extension,
			'file_base_name' => $file_base_name,
			'file_path'      => $file_path,
			'file_size'      => $file_size,			
			'url'            => $this->url($file_path),
			'mime'           => $mime,
			'dimension'      => $dimension
		);
	}
	
	/**
	 * Resize master image file.
	 *
	 * @param	array	$sizes
	 * @return  Attach
	 */
	public function resize($sizes = null)
	{		
		// A master file to resize.
		$master = $this->master;
		
		// Master image valid.
		if ( ! is_null($master) and preg_match('|image|', $master['mime']))
		{
			// Path with base dir.
			$path = $this->path($this->config['base_dir']);
			
			// All scales available.
			$scales = $this->config['scales'];
			
			// If empty mean generate all sizes from config.
			if (empty($sizes))
			{
				$sizes = array_keys($scales);
			}
			
			// If string mean generate one size only.
			if (is_string($sizes))
			{
				$sizes = (array) $sizes;
			}
			
			if (count($sizes)) foreach ($sizes as $size)
			{
				// Scale is not in config.
				if ( ! array_key_exists($size, $scales)) continue;
				
				// Get width and height.
				list ($w, $h) = $scales[$size];
				
				// Path with the name include scale and extension.
				$upload_path = $path.$master['file_name'].'_'.$size.'.'.$master['file_extension'];
				
				// Use WideImage to make resize and crop.
				WideImage::load($master['location'])
					->resize($w, $h, 'outside')
					->crop('center', 'middle', $w, $h)
					->saveToFile($upload_path);
				
				// Add a result and fired.
				$result = $this->results($upload_path, $size);
				
				// Add a result.
				$this->add_result($result);
			}		
		}
	
		return $this;
	}
	
	/**
	 * Remove master image file.
	 *
	 * @return  Attach
	 */
	public function remove()
	{
		$master = $this->master;
	
		if ( ! is_null($master))
		{
			$location = $master['location'];
			File::delete($location);
			
			// Fire a result to callback.
			$onRemove = $this->config['onRemove'];
			if ($onRemove instanceof Closure) 
			{
				$onRemove($master);
			}			
		}
		
		return $this;
	}
	
	/**
	 * Reset after uploaded master.
	 *
	 * @return void
	 */
	protected function reset()
	{
		$this->file = null;
	}
	
	/**
	 * Return all process results to callback.
	 *
	 * @return mixed
	 */
	public function onComplete($closure = null)
	{
		return ($closure instanceof Clousure) ? $closure($this->results) : $this->results;
	}
	
	/**
	 * After end of all process fire results to callback.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$onComplete = $this->config['onComplete'];
		if ($onComplete instanceof Closure) 
		{
			$onComplete($this->results);
		}
	}
	
}