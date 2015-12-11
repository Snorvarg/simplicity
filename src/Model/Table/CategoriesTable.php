<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use RuntimeException;

// TODO: All queries, more or less, fetch all rows from table, while in some cases only a fraction is needed. 
//   Update, and check the cake debug kits sql query tab.

/****
 * A Category is a part of the url, like the 'flowers' in the url mysite.now/flowers/the_rose?lang=EN-en
 * 
 * The Categories can be nested endlessly by the help of the Tree-behaviour, so an url like this: 
 *  mysite.now/space/solar-system/earth/sweden/stockholm-city?lang=SV-se
 *  would create the following tree-structure with 4 elements:
 *    space
 *    	solar-system
 *    		earth
 *    			sweden
 *    
 *    And at last, there would be a RichTextElement named stockholm-city in the language SV-se, with it's parent
 *    category set to sweden.
 *    
 *    The magic happens in the EditablePagesController, as CategoriesTable merely store and retreive the tree-structure.
 * 
 */
class CategoriesTable extends Table
{
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
		
		// We want the level, or deep, saved along with the category. 
		$this->addBehavior('Tree', ['level' => 'level']);
	}
	
	/**
	 * Returns the immediate childrens of the given category, or all root-categories if null is given. 
	 * 
	 * This can be used to fetch the immediate sub-menu-items for the currently active menu.
	 * 
	 * TODO: Dels trädstrukturen som de har 'färdig' för Tree, med länkar, 
	 * dels en Helper (eller Component?) som producerar en meny för den nivån som är aktiv. 
	 * 
	 * 1. Denna Helper/Component ska ha en MainMenu() som helt enkelt tar alla rot-element. 
	 * 2. Den ska också ha SubMenu($categoryId) som kort o gott anropar GetChildren() nedan. 
	 * 3. CrossLevelMenu($level) skapar en meny med categories med den angivna leveln. 
	 * 		<-Här är det viktigt att jag tänker ut ett praktiskt exempel, jag kommer inte på något nu dock.. :)
	 * <-Det stora arbetet denna Helper/Component har är att den också ska ta ut alla sidor. (RichTextElements)
	 *   En meny ska självklart grena ner till enskilda sidor. 
	 *   
	 *  EX: Om jag visar sidan belse/bubbels/sprayflaska, så vill jag i menyn se alla sidor och kategorier under
	 *      belse/bubbels/. Detta är vad som kallas en submeny. 
	 *  EX: Huvudmenyn är fortfarande alla sidor och kategorier utan förälder.
	 *  
	 *  OBS: Den stora frågan kvarstår: Helper/Component? Det får nog bli både och, och så får jag läsa på lite om 
	 *  		 AppController::preRender() eller vad det nu kan heta, där detta arbetet ska göras. (Kan i princip göras
	 *  		 i EditablePagesController::display() med..)
	 *  			<-Nä, användaren vill nog ha friheten att ha andra controllers ändå, som enkelt kan få menyn etc. via en
	 *  			  Component. En Helper kan sedan ta resultatet från Componenten och rendera. 
	 *  				Givetvis gör EditablePagesController::display() allt detta i ett ordnat kaos.
	 *  			<-Alltså, display() anropar komponenten o får ett gäng meny-variabler. Vyn default.ctp använder sedan
	 *  				Helpern för att rendera menyn. Då kan vilken controller som helst använda sig av meny-systemet. 
	 */
	public function GetTree($categoryId = null, $deep = 1)
	{
		if($deep < 1)
			throw new RuntimeException("Parameter deep cannot be less than one.");
		
		if($categoryId != null)
		{
			$rootElement = $this->find()->where(['id' => $categoryId])->first();
// 			debug($rootElement);
			
			$level = $rootElement->level;
// 			debug($level + $deep);
			
			// This nicely give me an array with children down to the level specified.
			// path, children, treeList <-are the 'implementedFinders' for TreeBehaviour.
/*			$query = $this->find('treeList', [
					'keyPath' => 'id',
					'valuePath' => 'name',
					'spacer' => ' '
			])
			->where([
					'level <=' => $level + $deep, 
					'parent_id >=' => $categoryId
			])
			->toArray();
*/
			
			// This is not working as I expect it to; I assume the element to climb up 3 steps in the tree-structure,
			// but nothing at all happens.
			// debug($this->moveUp($rootElement, 3));
			
			// This is a bit experimental, but the idea is to limit the selection of children to those whose level is in bounds.
			$tree = $this->find('children', ['for' => $categoryId])->
				where([
						'level <=' => $level + $deep,
				])->
				// Get only the fields we want incorporates id and parent_id for 'threaded' to work. 
		    find('threaded', ['fields' => ['name','parent_id','id','level']])->
				toArray();
		}
		else 
		{
			// The Tree behaviour don't seem to support getting 'children' where parent_id is null.  
			$rootElements = $this->find()->
				where([
						'parent_id is' => null,
						'level <=' => $deep,
				])->
				find('all', ['fields' => ['name','parent_id','id','level']])->
				toArray();
				
			$tree = array();
			foreach($rootElements as &$rootElement)
			{
				// debug($rootElement);
				
				if($deep > 1)
				{
					$subTree = $this->GetTree($rootElement->id, $deep - 1);
					// debug($subTree);
					
					$rootElement->children = $subTree;
				}
				else 
				{
					$rootElement->children = array();
				}
				
				$tree[] = $rootElement;
			}
		}
		
		return $tree;
	}
	
	/* Returns the given path as an array of category elements, the first element being the root element,
	 * and the last element being the innermost child element.  
	 * 
	 * If lastChildOnly is set, only the innermost element is returned. 
	 * 
	 * If createIfNotExist is set to true, the path will be constructed if it does not yet exist.
	 */
	public function GetPath(Array $path, $lastChildOnly = true, $createIfNotExist = true)
	{
		if(count($path) == 0)
			return null;

		$categoryPath = array();
			
		// Find (or create) the elements in the path.
		$lastCategory = null;
		while($name = array_shift($path))
		{
			// Look for child of $lastCategory with the given name. 
			if($lastCategory == null)
			{
				// Looking for a root category, no parent. 
				$category = $this->find('all')->where(['name' => $name, 'parent_id is' => null])->first();
			}
			else 
			{
				// Looking for a child category.
				$category = $this->find('all')->where(['name' => $name, 'parent_id' => $lastCategory->id])->first();
			}
			// debug($category);		
			
			if($category == null)
			{
				if($createIfNotExist)
				{
					// Create the element.
					if($lastCategory == null)
					{
						$category = $this->_CreateCategory(null, $name);
					}
					else
					{
						$category = $this->_CreateCategory($lastCategory->id, $name);
					}
					// debug($category);
				}
				else
				{
					// Could not find all parts (or none) of the path, and we are not allowed to create it.
					return null;
				}
			}
			
			// Found or created, here it is. 
			$lastCategory = $category;
			
			if($lastChildOnly == false)
			{
				$categoryPath[] = $category;
			}
		}
		
		if($lastChildOnly)
		{
			return $lastCategory;
		}
		else 
		{
			return $categoryPath;
		}
	}
	
	/* Returns the path down to root from the given category in the form of "fancy/path/to/", where "to" is the $category_id.
	 * 
	 */
	public function PathFor($category_id)
	{
		if($category_id == null)
			return "/";
		
		// This is a nice shortcut for getting all parents down to the root. 
		$crumbs = $this->find('path', ['for' => $category_id]);

		$path = "/";
		foreach($crumbs as $crumb)
		{
			$path .= $crumb->name."/"; 
		}
				
		return $path;
	}


	/* Tries to find the given category by its name and category.
	 * Returns null if not found.
	 * 
	 */
	protected function _FindCategory($parent_id, $name)
	{
		if($parent_id == null)
		{
			// null is so damn special in sql... (almost like infinity and infinity + 1, they are not equal, but both are infinite. Well infinity never equals.)
			$element = $this->find()
			->where(['parent_id is ' => null, 'name' => $name])
			->first();
		}
		else
		{
			$element = $this->find()
			->where(['parent_id' => $parent_id, 'name' => $name])
			->first();
		}
		
		return $element;
	}
	
	/**
	 * Create a category with the given parent. It must not exist when calling this function.
	 * 
	 */
	protected function _CreateCategory($parent_id, $name)
	{
		$element = $this->newEntity();
		$element->parent_id = $parent_id;
		$element->name = $name;
		
		if($this->save($element))
		{
			// debug("Saved");
		}
		else
		{
			// debug("Not saved");
		}
		 
		// Once created, lets read it back in.
		$element = $this->_FindCategory($parent_id, $name);
		
		return $element;
	}
	
	
	// DONE: Använder Tree, som är en icke-rekursiv funktion för att skapa en trädstruktur i databasen. 
	// 		Den kan med en enda query ta fram alla children för vilken del av trädet som helst. 
	//    Med en annan query kan man lika enkelt ta fram 'path to a node', tex. red-roses ger plants/roses/red-roses.
	//    Medelst en enkel liten loop kan man visuellt återskapa ett träd. 
	// En källa: 
	// http://www.sitepoint.com/hierarchical-data-database-2/
	// TreeBehaviour docs: 
	// http://book.cakephp.org/3.0/en/orm/behaviors/tree.html
	
	//     Språktaggen i18n i RichTextElement medger att jag kan ha samtliga språkversioner av 
	//     RödaRosor, RedRoses, RosasRojos, osv. med samma parent_id. 
	//     Då blir det bajseligt lätt att se på vilka språk en sida finns: GetLanguagesFor($name)
	//		  OBS: Hela detta tänket förutsätter att det är ok att en url är flowers/roses/the_red_french_rose,
	//      på alla språk. 
	//       Det finns alltså tre språkversioner av RichTextElement med identifier "the_red_french_rose", och 
	//       parent_id pekar på Category "roses". En kategory är således språklös. 
	//    ..en användare är fortfarande fri att skapa kategorier på olika språk, och ange identifier på varje språk,
	//    men dels förlorar han möjligheten att se om sidan finns på alla språk, dels tror jag att det är svårt att
	//    ens försöka få in franska i en url, och både Category och RichTextElement.identifier är en del av urlen. 
	//    ..så min rekommendation för alla som vill använda mitt system är att ha samma url till samma sida, på
	//    samtliga språk. 
}

/*
 CREATE TABLE `categories` (
 `id` INT(10) NOT NULL AUTO_INCREMENT,
 `parent_id` INT(10) NULL,
 `lft` INT(10) NOT NULL,
 `rght` INT(10) NOT NULL,
 `level` INT(10) NOT NULL,
 `name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
 `created` DATETIME NULL,
 `modified` DATETIME NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `uk_parent_id_name` (`parent_id`, `name`) 
 )
 COLLATE='utf8_unicode_ci'
 ENGINE=InnoDB
 ROW_FORMAT=COMPACT;
 */
