<?php
namespace App\View\Helper;

use Cake\View\Helper;

class MenuHelper extends Helper
{
	// Lathund för cake3.
	// http://cake3.codaxis.com/#html-helper

	public $helpers = ['Html'];
	
	/* Render the html for the given menu tree. 
	 * 
	 */
	public function GetMenu($menuTree, $ulClass = 'simplicity', $liClass = 'simplicity')
	{
		// Finns det någon anledning att ha en flagga för no-pages, eller only-pages? (I component då)
		
// 		debug($menuTree);
		$html = '';
				
		$html .= '<ul class="'.$ulClass.' root level_1">';
		$first = 'first';
		foreach($menuTree as &$element)
		{
			$html .= $this->_GetMenu($element, $ulClass, $liClass, $first, 1);
			$first = '';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	/* Recursively build the menu out of <ul> and <li> elements.
	 * 
	 */
	protected function _GetMenu(&$element, $ulClass, $liClass, $first, $level)
	{
		$html = '<li class="'.$liClass.' '.$first.' level_'.$level.'">';
		
		$repository = $element->source();
		
		if($repository == 'Categories')
		{
			$html .= $element->name;
			
			$html .= '<ul class="'.$ulClass.' child level_'.($level + 1).'">';
			$first = 'first';
			foreach($element->children as &$child)
			{
				$html .= $this->_GetMenu($child, $ulClass, $liClass, $first, $level + 1);
				$first = '';
			}
			$html .= '</ul>';
		}
		else // RichTextElements 
		{
			$html .= $this->Html->link($element->name, $element->path.$element->name);
		}
		
		$html .= '</li>';
	
		return $html;
	}
}
