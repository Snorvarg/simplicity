<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;
use App\Controller\AppController;

/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link http://book.cakephp.org/3.0/en/views.html#the-app-view
 */
class AppView extends View
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize()
    {
    	$this->loadHelper('Html', ['className' => 'SimplicityHtml']);
    	
    	// Each view are free to define their own variants of those blocks, but here we define the sensible defaults.
    	$this->assign('simplicity_site_title', AppController::$simplicity_site_title);
    	$this->assign('simplicity_site_description', AppController::$simplicity_site_description);
    	$this->assign('simplicity_footer_text', AppController::$simplicity_footer_text);
    	
    }
}
