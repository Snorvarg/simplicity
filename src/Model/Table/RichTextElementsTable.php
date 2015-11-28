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

	/* Returns array of language codes used in total.
	 * 
	 */
	public function GetLanguageCodes()
	{
		$query = $this->
						 find('list', ['keyField' => 'i18n', 'valueField' => 'i18n'])->
						 group('i18n')->
						 order(['i18n']);
		
		$all = $query->toArray();
		
		// debug($all);
		
		return $all;
	}	
	
	/* Return array of language codes the given page name exists in.
	 *  
	 * This is useful for the administrator of a multi-language site, so he can see in 
	 * which languages the current page exists.
	 * 
	 * To get which languages the page does not exists, subtract the two arrays, 
	 * from GetLanguageCodes() and GetLanguagesFor(). 
	 *  
	 */
	public function GetLanguagesFor($name)
	{
		$languages = $this->find('list', ['keyField' => 'i18n', 'valueField' => 'i18n'])
									->where(['name' => $name])
									->toArray();
		
		// debug($languages);
		
		return $languages;
	}
	
	/* If language is set, only links of the given language is returned.
	 * If category is set, only links starting with that is returned. Example: 'flowers' returns 'flowers/tulipans'.  
	 * 
	 * Returns a tree with the following format, ordered by the i18n language flag, then the identifier.
	 *  array(
	 *  	'en-GB' => array(
	 *  		id => 'flowers/tulipan',
	 *  		id => '..'
	 *  	),
	 *  	..
	 *  )
	 *  
	 *  TODO: En funktion som trädar ner "flowers/tulipan" i flowers => tulipan
	 *    för enkel meny-ifiering. 
	 *  TODO: När innehållet blir stort, så bör man välja ut ett språk och en kategori i taget .
	 *  TODO: En funktion som ger antalet kategorier i databasen. 
	 *  TODO: En dokumentation med exempel som beskriver hur två arbetssätt: 
	 *  	1. Man anger en url på det språk sidan är på, och får leva med att det blir svårt att se om motsvarande sida
	 *       finns på de andra språken.
	 *       (Men var tydlig med att menyerna funkar lika bra ändå)
	 *    2. Man ser till att urlen (identifier) är densamma på samtliga språk, och får därmed fördelen att enkelt kunna
	 *    	 se vilka fler språk sidan finns på.
	 */
	public function GetTree($i18n = null, $category = null)
	{
		$conditions = array();
		if($i18n != null)
		{
			$conditions['i18n'] = $i18n;
		}
		if($category != null)
		{
			$conditions['identifier like '] = $category.'/%';
		}
		
		$query = $this->
						find('list', ['valueField' => 'identifier', 'groupField' => 'i18n', 'conditions' => $conditions])->
						order(['i18n','identifier']);
								
		$all = $query->toArray();

		// debug($all);
		
		return $all;
	}
  
	/* The default way of identifying a rich text element is by it's url. 
	 * Routing is setup to reroute "a/path/to/thisuniquepage?lang=sv-SE" 
	 * into "editable_pages/display/a/path/to/thisuniquepage?lang=sv-SE". 
	 * So the name of this page would be "thisuniquepage".
	 * 
	 * $categoryId in the same example would point to the "to" category.  
	 * 
	 * If $i18n is set, it should follow the i18n standards, like 'en-GB' for British english.
	 * In the same example the url parameter 'lang' is extracted, which is 'sv-SE'
	 * 
	 * The three parts, categoryId + name + i18n forms a unique id.
	 * In the same example it would be "to" + "thisuniquepage" + "sv-SE".
	 * It means that "thisuniquepage" can exist in several languages.
	 * It also means that the name "thisuniquepage" can exist on different paths, 
	 * like "some/other/path/to/thisuniquepage", or "/thisuniquepage".  
	 * 
	 * If $createIfNotExist is true, an empty element will be created if it does not already exists.
	 *  
	 */
	public function GetElement($name, $categoryId = null, $i18n = '', $createIfNotExist = true)
  {
		$element = $this->_Get($name, $categoryId, $i18n);
		    
    if($element == null && $createIfNotExist)
    {
      // First time visit indeed, let's create an empty text element and return it.
      $element = $this->newEntity();
      $element->category_id = $categoryId;
      $element->name = $name;
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
          	      
      // Once created, lets read it back in.
      $element = $this->_Get($name, $categoryId, $i18n);
    }
    
    // debug($element);
    
    return $element;
  }
  
  /* Load and returns the element if it exists, otherwise returns null.
   * 
   */
  protected function _Get($name, $categoryId, $i18n)
  {
  	// Learning as we go:
  	//  The find() returns a $query object, which can go through any number of permutations by calling
  	//  different functions.
  	//  The actual database query is not executed until calling first() or find().
  	  
  	if($categoryId != null)
  	{
  		$element = $this->find()
  		->where(['category_id' => $categoryId, 'name' => $name, 'i18n' => $i18n])
  		->first();
  	}
  	else
  	{
  		// null is null, but null != null.
  		$element = $this->find()
  		->where(['category_id is' => null, 'name' => $name, 'i18n' => $i18n])
  		->first();
  	}
  	
  	return $element;
  }
}  

/*
CREATE TABLE `rich_text_elements` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
  `category_id` INT(10) NULL,
  `i18n` VARCHAR(12) NOT NULL COLLATE 'utf8_unicode_ci',
  `content` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`created` DATETIME NULL,
	`modified` DATETIME NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `uk_category_id_name_i18n` (`category_id`, `name`,`i18n`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT;
*/