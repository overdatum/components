<?php
/**
 * Part of the Artifact builder for Layla.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file licence.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getlayla.com so I can send you a copy immediately.
 *
 * @package    Layla Components
 * @version    1.0
 * @author     Koen Schmeets <koen@getlayla.com>
 * @license    MIT License
 * @link       http://getlayla.com
 */

namespace Layla\Artifact\Renderers;

use Laravel\URI;
use Laravel\Session;

use Layla\Asset;
use Layla\Artifact;

use Bootsparks\Form;
use Bootsparks\HTML;

/**
 * This class renders the components for Twitter Bootstrap
 */
class Layla extends Renderer {

	public function full_list($callback)
	{
		$content = Artifact::render($callback);
		return HTML::div(HTML::div(HTML::div($content, array('class' => 'list')), array('class' => 'container')), array('class' => 'list-column'));
	}

	public function versions($callback)
	{
		$content = Artifact::render($callback);
		return HTML::div(HTML::div(HTML::div($content, array('class' => 'list')), array('class' => 'container')), array('class' => 'versions-column'));
	}

	public function header($callback)
	{
		$content = Artifact::render($callback);
		return HTML::div(HTML::div($content, array('class' => 'controls')), array('class' => 'header'));
	}

	public function items($callback)
	{
		$content = Artifact::render($callback);
		return HTML::element('ul', $content, array('class' => 'items'));
	}

	public function templates($templates)
	{
		return;
	}

	public function no_results($message)
	{
		return $message;
	}

	public function page_header($callback)
	{
		$content = Artifact::render($callback);
		return HTML::div($content, array('class' => 'page-header'));
	}

	public function float_right($callback, $attributes = array())
	{
		return $this->float('right', $callback, $attributes);
	}

	public function float_left($callback, $attributes = array())
	{
		return $this->float('left', $callback, $attributes);
	}

	public function float($float, $callback, $attributes = array())
	{
		$content = Artifact::render($callback);
		return HTML::div($content, merge_attributes(array('class' => 'pull-'.$float), $attributes));
	}

	public function title($title)
	{
		return HTML::element('h1', $title);
	}

	public function sub_title($title)
	{
		return HTML::div(HTML::div(HTML::element('h3', $title), array('class' => 'controls')), array('class' => 'control-group'));
	}

	public function search()
	{
		return Form::open(URI::current(), 'GET').
				Form::text('q', null, array('class' => 'search')).
			Form::close();
	}

	public function table($config)
	{
		return HTML::element('table', Artifact::driver('table')->render($config), array('class' => 'table table-striped'));
	}

	public function thead($callback)
	{
		return HTML::element('thead', Artifact::render($callback));
	}

	public function tbody($callback)
	{
		return HTML::element('tbody', Artifact::render($callback));
	}

	public function tr($callback)
	{
		return HTML::element('tr', Artifact::render($callback));
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

	public function form($callback, $method = 'GET', $url = '')
	{
		$content = Artifact::render($callback);
		return HTML::div(HTML::div(
			Form::open($url, strtoupper($method), array('class' => 'form-horizontal')).
				$content.
			Form::close()
		, array('class' => 'container')), array('class' => 'form-column'));
	}

	public function link($url, $title)
	{
		return HTML::link($url, $title);
	}

	public function well($callback)
	{
		$content = Artifact::render($callback);
		return HTML::div($content, array('class' => 'well'));
	}

	public function text($name, $label, $value = '')
	{
		return Form::field('text', $name, $label, array($value), array('error' => $this->errors->first($name)));
	}

	public function textarea($name, $label, $value = '')
	{
		return Form::field('textarea', $name, $label, array($value), array('error' => $this->errors->first($name)));
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

	public function actions($callback)
	{
		$content = Artifact::render($callback);
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

	public function fieldset($title, $view)
	{
		return HTML::element('fieldset', HTML::element('legend', $title).Artifact::render($view));
	}

	/**
	 * @todo refactor this thing
	 */
	public function tabs($callback, $variant = 'top')
	{
		$tabs = Artifact::driver('tabs');

		$tabs->apply($callback);

		$list = array();
		foreach($tabs->titles as $i => $title)
		{
			$i++;
			$attributes = ($i == $tabs->active ? array('class' => 'active') : array());
			$list[] = HTML::element('li', HTML::link('#tab'.$i, $title, array('data-toggle' => 'tab')), $attributes);
		}

		$output = HTML::ul($list, array('class' => 'nav nav-tabs'));

		$contents = '';
		foreach ($tabs->contents as $i => $content)
		{
			$i++;
			$contents .= HTML::div(Artifact::render($content), array('id' => 'tab'.$i, 'class' => 'tab-pane'.($i == $tabs->active ? ' active' : '')));
		}

		$output .= HTML::div($contents, array('class' => 'tab-content'));
	
		if($variant !== 'top')
		{
			return HTML::div($output, array('class' => 'tabbable tab-'.$variant));
		}

		return $output;
	}

	public function next_tab($text, $variant = '', $size = 'large')
	{
		Asset::container('footer')->add_js();
	
		return Form::button($text, array('class' => 'btn'.($variant == '' ? '' : ' btn-'.$variant).' btn-'.$size));
	}

}