<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
		// Set default language to your like, but make sure it is present in table languages, i18n.  
		public static $defaultLanguage = 'sv_SE';
		public static $selectedLanguage = '';
		
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        // Try get the chosen language as an url param, namely '?lang=SV-se'. 
        AppController::$selectedLanguage = $this->request->query('lang');
        
        // TODO: Try to get from browser cookie, and if no cookie, use the default language of the site. 
        if(AppController::$selectedLanguage == null)
        	AppController::$selectedLanguage = AppController::$defaultLanguage;
        
        // TESTING
        $languages = TableRegistry::get('Languages');
        // $variants = $languages->GetVariants('en');
        // debug($variants);
        
        $richTextElements = TableRegistry::get('RichTextElements');
        
        $languages = $richTextElements->GetLanguageCodes();
        
        $home = $richTextElements->GetElement('home',null,'sv_SE');
        $languagesForHome = $richTextElements->GetLanguagesFor($home->name, $home->category_id);
//         debug($home);
//         debug($languagesForHome);
        
        $categories = TableRegistry::get('Categories');
        // $children = $categories->GetChildren(4);
                           
        // TESTING END
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
    
    /* Adds the current language url parameter on any redirect.
     * 
     */
    public function redirect($url, $status = 302)
    {
    	if(!is_array($url))
    	{
    		if(strpos($url,'?lang=') === false)
    		{
    			$url .= '?lang='.AppController::$selectedLanguage;
    		}
    	}
    	else if(!isset($url['?'])) 
    	{
    		// TODO: Not tested.
    		$url['?'] = '?lang='.AppController::$selectedLanguage;
    	}
    	
    	return parent::redirect($url, $status);
    }
}
