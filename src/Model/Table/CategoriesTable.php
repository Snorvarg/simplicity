<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\RulesChecker;

class CategoriesTable extends Table
{
	
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
		$this->addBehavior('Tree');
	}

	public function GetCategoryByParentName($parentName, $name)
	{
		// TODO: Här ska du använda Tree, som letar upp alla $name med parent $parentName.
	}
	
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
	
	// TODO: Använder Tree, som är en icke-rekursiv funktion för att skapa en trädstruktur i databasen. 
	// 		Den kan med en enda query ta fram alla children för vilken del av trädet som helst. 
	//    Med en annan query kan man lika enkelt ta fram 'path to a node', tex. red-roses ger plants/roses/red-roses.
	//    Medelst en enkel liten loop kan man visuellt återskapa ett träd. 
	// En källa: 
	// http://www.sitepoint.com/hierarchical-data-database-2/
	// TreeBehaviour docs: 
	// http://book.cakephp.org/3.0/en/orm/behaviors/tree.html
	
	// Kolla in 'Node Level' och se om du behöver använda det. (fält i databasen)
	// Kolla in 'Scoping and Multi Trees' och språk. Det kanske är lika bra att använda Tree även för språk?
	//  ..måste du inte det? Du flyttar ju ut 'flowers' delen ur 'flowers/roses', så endast 'roses' (och parent_id!!) 
	//    blir kvar som identifier för RichTextElement. 
	//    Så, du måste även flytta ut språket? 
	//   <-Spontant skulle jag vilja lägga språket , i18n, som ett rotelement, och sen låta alla kategorier hamna där under. 
	//		 Men då försvinner förmågan att ha samma identifier men olika språk, det blir helt omöjligt att ha bra 
	//		 multispråkstöd. 
	//     Om jag behåller språktaggen i18n i RichTextElement, så kan samtliga språkversioner av 
	//     RödaRosor, RedRoses, RosasRojos, osv. ha samma parent_id. 
	//     Då blir det bajseligt lätt att se på vilka språk en sida finns: 
	//      Ta fram alla med samma identifier och category_id. 
	//		  OBS: Hela detta tänket förutsätter att det är ok att en url är flowers/roses/the_red_french_rose,
	//      på alla språk. 
	//       Det finns alltså tre språkversioner av RichTextElement med identifier "the_red_french_rose", och 
	//       parent_id pekar på Category "roses". En kategory är således språklös. 
	//    ..en användare är fortfarande fri att skapa kategorier på olika språk, och ange identifier på varje språk,
	//    men dels förlorar han möjligheten att se om sidan finns på alla språk, dels tror jag att det är dumt att
	//    ens försöka få in franska i en url, och både Category och RichTextElement.identifier är en del av urlen. 
	//    ..så min rekommendation för alla som vill använda mitt system är att ha samma url till samma sida, på
	//    samtliga språk. 
	// DONE: Det är alltså RIKTIGT HÖG TID att få språket på plats! Följande alternativ finnes: 
	//  -DETTA: urlen. Det kan ligga som en url-parameter: flowers/roses/red_roses?lang=en-EN
	//  -cookie. "en-EN" ligger i en cookie. 
	//  -session. "en-EN" ligger i en sessionvariabel på servern. 
	// <-urlen tillåter att man länkar till specifik sida, så det är det jag tänker använda mig av!! 
	//   Det finns ett sätt till, att mha. routing ta ut språkvalet ur urlen i början, men jag ser ingen större nackdel
	//   med att ha en url-param. (se.flowers.com är en subdomänlösning jag gillar, men det kräver av användaren 
	//   av simplicity att konfigurera upp det på sin server: krångel, alla kan inte.)
	
	
	
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
