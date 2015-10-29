<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class RichTextElementsTable extends Table
{
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
	}
	
	/* 
	 * "thisuniquepage" + "en_GB" will load the english version of the page "thisuniquepage". 
	 * "thisuniquepage" + "sv-SE" will load the swedish version.
	 * 
	 * What if you want different url for the same page in Spanish? 
	 * Simple: "paginaunico" + "es_ES" will be unique. 
	 * 
	 * NOTE: What is lost here is the connection between different language-versions of a page.. 
	 *    ..I guess that is a trade-off between simplicity and total control. It is a no-fix at the moment at least, but in the future
	 *    it can be acheived by a manual connection between different pages, like a 'group' field in the table.
	 * 
	 */

  
	/* The default way of identifying a rich text element is by it's url. 
	 * Routing is setup to reroute "pages/thisuniquepage" into "editable_pages/display/thisuniquepage". 
	 * So the unique id of this page would be "thisuniquepage".
	 * 
	 * If $i18n is set, it should follow the i18n standards, like 'en-GB' for British english.
	 * The identifier + i18n forms a unique id. 
	 * 
	 * Will create an empty element with the given identifier if it does not already exists. 
	 */
	public function GetElement($identifier, $i18n = '', $createIfNotExist = true)
  {
		// Learning as we go: 
		//  The find() returns a $query object, which can take go through any number of permutations by calling
		//  different functions. 
		//  The actual database query is not executed until calling first() or find(). 
		// 
  	$element = $this->find()
			->where(['identifier' => $identifier, 'i18n' => $i18n])
			->first();
		    
    if($element == null && $createIfNotExist)
    {
      // First time visit indeed, let's create an empty text element and return it.
      $element = $this->newEntity();
      $element->identifier = $identifier;
      $element->i18n = $i18n;
      $element->content = '';
      
      if($this->save($element))
      {
      	// debug("Saved");
      }
      else
      {
      	// debug("Not saved");
      }
      
// This is not working, it is asking for 4 values, even if the table only contains two values + id.
//       $query = $this->query();
//       $query->insert(['identifier','content']);
//       $query->values([$identifier, '']);
//       $query->execute();
    	      
      // Once created, lets read it back in.
	  	$element = $this->find()
				->where(['identifier' => $identifier, 'i18n' => $i18n])
				->first();
    }
    
    // debug($element);
    
    return $element;
  }
}  

/*
NOTE: MEDIUMTEXT cannot have a default value.

CREATE TABLE `rich_text_elements` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
  `i18n` VARCHAR(12) NOT NULL COLLATE 'utf8_unicode_ci',
  `content` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`created` DATETIME NULL,
	`modified` DATETIME NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `uk_identifier_i18n` (`identifier`,`i18n`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT;
*/