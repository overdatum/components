<?php namespace Layla\Module\Renderers;

use Laravel\Session;

use Layla\Module;

use Bootsparks\Form;
use Bootsparks\HTML;

class Bootstrap extends Renderer {

	public function page_header($children)
	{
		$content = Module::render($children);
		return HTML::div($content, array('class' => 'page-header'));
	}

	public function float_right($children)
	{
		return $this->float('right', $children);
	}

	public function float_left($children)
	{
		return $this->float('left', $children);
	}

	public function float($float, $children)
	{
		$content = Module::render($children);
		return HTML::div($content, array('class' => 'pull-'.$float));
	}

	public function title($title)
	{
		return HTML::h1($title);
	}

	public function search()
	{
		return HTML::div(
			Form::open('', 'GET').
				Form::text('q', null, array('placeholder' => 'Search')).
				Form::submit('<span class="icon-search icon-white"></span>', array('class' => 'btn btn-primary')).
			Form::close()
		, array('id' => 'search'));
	}

	public function table($config)
	{
		return HTML::element('table', Module::render($config, 'table'), array('class' => 'table table-striped'));
	}

	public function thead($children)
	{
		return HTML::element('thead', Module::render($children));
	}

	public function tbody($children)
	{
		return HTML::element('tbody', Module::render($children));
	}

	public function tr($children)
	{
		return HTML::element('tr', Module::render($children));
	}

	public function th($content)
	{
		return HTML::element('th', $content);
	}

	public function td($content, $attributes = array())
	{
		return HTML::element('td', $content, $attributes);
	}

	public function links($paginator)
	{
		return $paginator->links();
	}

	public function form($content, $method = 'GET', $url = '')
	{
		return
			Form::open($url, strtoupper($method), array('class' => 'form-horizontal')).
				$content.
			Form::close();
	}

	public function link($url, $title)
	{
		return HTML::link($url, $title);
	}

	public function well($children)
	{
		$content = Module::render($children);
		return HTML::div($content, array('class' => 'well'));
	}

	public function text($name, $label, $value = '')
	{
		return Form::field('text', $name, $label, array($value), array('error' => $this->errors->first($name)));
	}

	public function password($name, $label, $value = '')
	{
		return Form::field('password', $name, $label, array(), array('error' => $this->errors->first($name)));
	}

	public function multiple($name, $label, $options, $selected = array())
	{
		return Form::field('select', $name, $label, array($options, $selected, array('multiple' => 'multiple')), array('error' => $this->errors->first(str_replace('[]', '', $name))));
	}

	public function dropdown($name, $label, $options, $selected = array())
	{
		return Form::field('select', $name, $label, array($options, $selected));
	}

	public function actions($children)
	{
		$content = Module::render($children);
		return Form::actions($content);
	}

	public function submit($text, $variant = '', $size = 'large')
	{
		return Form::submit($text, array('class' => 'btn'.($variant == '' ? '' : ' btn-'.$variant).' btn-'.$size));
	}

	public function button($url, $title, $variant = '', $size = 'large')
	{
		return HTML::link($url, $title, array('class' => 'btn'.($variant == '' ? '' : ' btn-'.$variant).' btn-'.$size));
	}

}