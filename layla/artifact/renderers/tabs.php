<?php
/**
 * Artifact - A View abstraction taken from Layla.
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

use Closure;

use Laravel\Session;

use Layla\Artifact;
use Layla\Artifact\Catcher;

use Bootsparks\Form;
use Bootsparks\HTML;

/**
 * This class provides a handy interface for working with tabs
 */
class Tabs extends Renderer {

	/**
	 * The index of the active tab
	 * 
	 * @var integer
	 */
	public $active = 1;

	/**
	 * The titles for the tabs
	 * 
	 * @var array
	 */
	public $titles = array();

	/**
	 * The contents of the tabs
	 * 
	 * @var array
	 */
	public $contents = array();

	/**
	 * Add a tab
	 * 
	 * @param string	$title		the title of the tab
	 * @param Closure	$content	the contents of the tab
	 */
	public function tab($title, Closure $content)
	{
		$this->titles[] = $title;
		$this->contents[] = $content;
	}

}