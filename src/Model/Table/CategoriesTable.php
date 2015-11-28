<?php

namespace App\Model\Table;

use Cake\ORM\Table;

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
		$this->addBehavior('Tree');
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
	
// NOT USED: Rätt onödig väl
	/* Returns the category with the given name and parent_id. 
	 * If it does not already exist, it is created, if $createIfNotExist is true.
	 * 
	 */
	public function GetCategoryByParentId($parent_id, $name, $createIfNotExist = true)
	{
		if($name == '')
			return null;
		
		$element = $this->FindCategory($parent_id, $name);
				
    if($element == null && $createIfNotExist)
    {
      // The category does not exist yet, let's create it.
      $element = $this->_CreateCategory($parent_id, $name);
    }
    
    // debug($element);
    
    return $element;
	}

	/* Tries to find the given category by its name and category.
	 * Returns null if not found.
	 * 
	 */
	protected function FindCategory($parent_id, $name)
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
		$element = $this->FindCategory($parent_id, $name);
		
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
	
	// Kolla in 'Node Level' och se om du behöver använda det. (fält i databasen)
	//     Om jag behåller språktaggen i18n i RichTextElement, så kan samtliga språkversioner av 
	//     RödaRosor, RedRoses, RosasRojos, osv. ha samma parent_id. 
	//     Då blir det bajseligt lätt att se på vilka språk en sida finns: 
	//      Ta fram alla med samma identifier och category_id. 
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
