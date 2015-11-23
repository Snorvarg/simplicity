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
		// TODO: H�r ska du anv�nda Tree, som letar upp alla $name med parent $parentName.
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
	
	// TODO: Anv�nder Tree, som �r en icke-rekursiv funktion f�r att skapa en tr�dstruktur i databasen. 
	// 		Den kan med en enda query ta fram alla children f�r vilken del av tr�det som helst. 
	//    Med en annan query kan man lika enkelt ta fram 'path to a node', tex. red-roses ger plants/roses/red-roses.
	//    Medelst en enkel liten loop kan man visuellt �terskapa ett tr�d. 
	// En k�lla: 
	// http://www.sitepoint.com/hierarchical-data-database-2/
	// TreeBehaviour docs: 
	// http://book.cakephp.org/3.0/en/orm/behaviors/tree.html
	
	// Kolla in 'Node Level' och se om du beh�ver anv�nda det. (f�lt i databasen)
	// Kolla in 'Scoping and Multi Trees' och spr�k. Det kanske �r lika bra att anv�nda Tree �ven f�r spr�k?
	//  ..m�ste du inte det? Du flyttar ju ut 'flowers' delen ur 'flowers/roses', s� endast 'roses' (och parent_id!!) 
	//    blir kvar som identifier f�r RichTextElement. 
	//    S�, du m�ste �ven flytta ut spr�ket? 
	//   <-Spontant skulle jag vilja l�gga spr�ket , i18n, som ett rotelement, och sen l�ta alla kategorier hamna d�r under. 
	//		 Men d� f�rsvinner f�rm�gan att ha samma identifier men olika spr�k, det blir helt om�jligt att ha bra 
	//		 multispr�kst�d. 
	//     Om jag beh�ller spr�ktaggen i18n i RichTextElement, s� kan samtliga spr�kversioner av 
	//     R�daRosor, RedRoses, RosasRojos, osv. ha samma parent_id. 
	//     D� blir det bajseligt l�tt att se p� vilka spr�k en sida finns: 
	//      Ta fram alla med samma identifier och category_id. 
	//		  OBS: Hela detta t�nket f�ruts�tter att det �r ok att en url �r flowers/roses/the_red_french_rose,
	//      p� alla spr�k. 
	//       Det finns allts� tre spr�kversioner av RichTextElement med identifier "the_red_french_rose", och 
	//       parent_id pekar p� Category "roses". En kategory �r s�ledes spr�kl�s. 
	//    ..en anv�ndare �r fortfarande fri att skapa kategorier p� olika spr�k, och ange identifier p� varje spr�k,
	//    men dels f�rlorar han m�jligheten att se om sidan finns p� alla spr�k, dels tror jag att det �r dumt att
	//    ens f�rs�ka f� in franska i en url, och b�de Category och RichTextElement.identifier �r en del av urlen. 
	//    ..s� min rekommendation f�r alla som vill anv�nda mitt system �r att ha samma url till samma sida, p�
	//    samtliga spr�k. 
	// DONE: Det �r allts� RIKTIGT H�G TID att f� spr�ket p� plats! F�ljande alternativ finnes: 
	//  -DETTA: urlen. Det kan ligga som en url-parameter: flowers/roses/red_roses?lang=en-EN
	//  -cookie. "en-EN" ligger i en cookie. 
	//  -session. "en-EN" ligger i en sessionvariabel p� servern. 
	// <-urlen till�ter att man l�nkar till specifik sida, s� det �r det jag t�nker anv�nda mig av!! 
	//   Det finns ett s�tt till, att mha. routing ta ut spr�kvalet ur urlen i b�rjan, men jag ser ingen st�rre nackdel
	//   med att ha en url-param. (se.flowers.com �r en subdom�nl�sning jag gillar, men det kr�ver av anv�ndaren 
	//   av simplicity att konfigurera upp det p� sin server: kr�ngel, alla kan inte.)
	
	
	
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
