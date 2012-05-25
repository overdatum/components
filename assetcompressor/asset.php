<?php namespace AssetCompressor;

use Laravel\HTML;
use Laravel\Config;
use Laravel\File;
use Laravel\Asset_Container as Laravel_Asset_Container;
use Laravel\Asset as Laravel_Asset;

use AssetCompressor\Drivers\MinifyCSS\Compressor;

class Asset_Container extends Laravel_Asset_Container {

	public function __construct($name)
	{
		parent::__construct($name);
		
		$this->config = Config::get('layla_thirdparty_assetcompressor::assetcompressor');
		$this->config['cache_dir'] = $this->config['cache_dir'] . '/';
		$this->config['cache_dir_path'] = path('public').$this->config['cache_dir'];

		if( ! is_dir($this->config['cache_dir_path']))
			mkdir($this->config['cache_dir_path']);
	}

	/**
	 * Return one tag with the minified scripts or styles
	 */
	protected function group($group)
	{
		if ( ! isset($this->assets[$group]) or count($this->assets[$group]) == 0) return '';

		if($this->config['enabled'])
		{
			return $this->minify($group, $this->arrange($this->assets[$group]));
		}

		$assets = '';

		foreach ($this->arrange($this->assets[$group]) as $name => $data)
		{
			$assets .= $this->asset($group, $name);
		}
		
		return $assets;
	}

	public function minify($group, $assets)
	{
		$combined_assets = array();
		foreach($assets as $name => $data)
		{
			asort($data['attributes']);
			$attributes_string = json_encode($data['attributes']);
			$combined_assets[$attributes_string][$name] = $data;
		}

		$assets_html = '';
		$compile = false;
		foreach ($combined_assets as $attributes_string => $assets)
		{
			$files_to_compile = array();
			$output_files = array();
			foreach($assets as $name => $data)
			{
				$file = path('public') . $data['source'];
				if( ! File::exists($file))
				{
					throw new Exception('The Asset you are trying to compress does not exist ('.$file.')');
				}

				$output_files[] = $name . '.' . ($group == 'script' ? 'js' : 'css');
				$files_to_compile[] = $data['source'];
			}

			$output_file = implode(',', $output_files);
			if( ! count($output_files))
				return;

			if( ! file_exists($this->config['cache_dir_path'] . $output_file))
			{
				$compile = true;
			}
			else
			{
				foreach ($files_to_compile as $file)
				{
					if(File::modified($this->config['cache_dir_path'] . $output_file) < File::modified(path('public') . $file))
					{
						$compile = true;
					}
				}
			}

			$method = 'minify_'.$group;

			$assets_html .= $this->$method($assets, $data['attributes'], $files_to_compile, $output_file, $compile);
		}

		return $assets_html;
	}

	protected function minify_style($assets, $attributes, $files_to_compile, $output_file, $compile)
	{
		if($compile)
		{
			$output_file_contents = '';
			foreach ($files_to_compile as $file) {
				$asset_content = File::get(path('public').$file);
				$output_file_contents .= Compressor::process($asset_content);
			}

			File::put($this->config['cache_dir_path'] . $output_file, $output_file_contents);
		}

		return HTML::style($this->config['cache_dir'] . $output_file . '?t=' . File::modified($this->config['cache_dir_path'] . $output_file), $attributes);
	}

	protected function minify_script($assets, $attributes, $files_to_compile, $output_file, $compile)
	{
		if($compile)
		{
			$scripts_to_minify = array();
			foreach($files_to_compile as $file)
			{
				$scripts_to_minify[] = '--js=' . path('public') . $file;
			}
			$scripts_to_minify = implode(' ', $scripts_to_minify);

			$java = 'java';

			// find java binary
			foreach($this->config['java_binary_path_overrides'] as $java_bin_path)
			{
				if(file_exists($java_bin_path)) $java = $java_bin_path;
			}
			
			// generate command line
			$jar = '-jar ' . __DIR__ . '/drivers/closure-compiler/compiler.jar';
			$out_script = '--js_output_file=' . $this->config['cache_dir_path'] . $output_file;

			// red team, go!
			exec("$java $jar $scripts_to_minify $out_script");
		}

		return HTML::script($this->config['cache_dir'] . $output_file . '?t=' . File::modified($this->config['cache_dir_path'] . $output_file), $attributes);
	}

}

class Asset extends Laravel_Asset {

	/**
	 * Let it grab the extended Asset_Container class
	 */
	public static function container($container = 'default')
	{
		if ( ! isset(static::$containers[$container]))
		{
			static::$containers[$container] = new Asset_Container($container);
		}

		return static::$containers[$container];
	}

}