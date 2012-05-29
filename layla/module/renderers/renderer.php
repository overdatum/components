<?php namespace Layla\Module\Renderers;

class Renderer {

	public function render($fields)
	{
		$html = '';
		foreach ($fields as $field)
		{
			foreach ($field as $type => $options)
			{
				$html .= call_user_func_array(array($this, $type), $options);
			}
		}

		return $html;
	}

	public function add($children)
	{
		$children = (array) $children;
		return implode("\n", $children);
	}

	public function nest($method, $children)
	{
		$arguments = func_get_args();
		$arguments = array_slice($arguments, 2);
		
		$children = $this->render($children);
		return call_user_func_array(array($this, $method), array_merge(array($children), $arguments));
	}

}