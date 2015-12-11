<?php 

namespace App\Controller\Component;

use App\Controller;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use TestApp\Controller\Component\AppleComponent;
use App\Controller\AppController;

class MenuComponent extends Component
{
	public $categories; 
	public $richTextElements;
	
	public function initialize(array $config)
	{
		$this->categories = TableRegistry::get('Categories');
		$this->richTextElements = TableRegistry::get('RichTextElements');
	}
	
	/* Returns the children of the given category, including RichTextElements. If null is given, the root-nodes are returned. 
	 * If level is greater than 0, it is branched down 'level' childrens down. 
	 * 
	 * Example: A tree three levels deep:
	 * 	fruits
	 * 		apple
	 * 			yellow
	 * 			black
	 * 		pear
	 * 			hairy
	 * 			stiff
	 * 	animals
	 * 		about_animals   <-This is a RichTextElement, i.e an actual page. 
	 * 		cat
	 * 			hungry
	 * 			purring
	 * 		salmon
	 * 			swimming
	 * 			dead
	 * 
	 * Getting the children of the animals category, with level 0, would return
	 * 	cat, salmon, about_animals
	 * 
	 * If level=1 (or greater in this case), the entire sub tree would come: 
	 * 		cat
	 * 			hungry
	 * 			purring
	 * 		salmon
	 * 			swimming
	 * 			dead
	 * 	
	 */
	public function GetTree($parentCategoryId = null, $level = 0)
	{
		$tree = $this->categories->GetTree($parentCategoryId, $level)->toArray();

		// The main difference between all() and toArray() is that all() uses 'lazy loading' while toArray() uses 'eager loading'.
		// We need the result from all() realized into an array right now, so use toArray().
		// (debug($rtes) is internally performing hocus pocus, incorporating toArray().)
		// 
		//$array = array_merge($children->toArray(), $rtes->toArray());

		// Get the RichTextElements whose parents level is one less than the given $level.
		foreach($tree as &$category)
		{
			$this->_MergeContent($category, $level);
		}
		
		// At last get the RTEs for the root node.
		$rtes = $this->richTextElements->ElementsForCategory($parentCategoryId, AppController::$selectedLanguage, true);
		$rtes = $rtes->toArray();
		
		foreach($rtes as $rte)
		{
			$this->_AddPath($rte);
		}
		
		$tree = array_merge($tree, $rtes);
		
		// debug($tree);

		// Get the name of the model for an object:
// 		$repository = $element->source();
// 		debug($repository);
		
		return $tree;
	}
	
	
	/* Returns array with all categories at a given level in the tree.
	 * 
	 *  Example: A tree four levels deep: 
	 *   	cat1		cat2		cat3 		<-Level 0, categories where parent category is null
	 *   	sub1		sub2		sub3		<-Level 1, categories whose parent category is right above them.
	 *   	zub1		zub2		zub3		<-Level 2, same here.
	 * 		cub1		cub2		cub3		<-Level 3, same here. 
	 * 		
	 * Calling GetLevel(2) will return an array with all the zub elements. 
	 * Note that any category can have any number of childrens, so there could be more than 3 zub elements in this example. 
	 * 
	 */
	public function GetLevel($level)
	{
		// TODO:
		
		// TODO: Also get the RichTextElements whose parents level is one less than the given $level.
	}
	

	/* Recursively merge in pages on each level in the tree.
	 *
	 */
	protected function _MergeContent(&$category, $level)
	{
		foreach($category->children as &$child)
		{
			$this->_MergeContent($child, $level);
		}
	
		if($category->level < $level)
		{
			$rtes = $this->richTextElements->ElementsForCategory($category->id, AppController::$selectedLanguage, true);
			$rtes = $rtes->toArray();
			// 			debug($category->id);
			// 			debug($rtes);
	
			foreach($rtes as $rte)
			{
				$this->_AddPath($rte);
			}			
			
			$category->children = array_merge($category->children, $rtes);
		}
	}
	
	/* Add the url path to the given richTextElement as variable 'path'.
	 *
	 */
	protected function _AddPath(&$richTextElement)
	{
		debug($richTextElement);
		$richTextElement->path = $this->categories->PathFor($richTextElement->category_id);
		debug($richTextElement->path);
	}	
}