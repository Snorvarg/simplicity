<?php
/**
 * Simplicity (https://github.com/Snorvarg/simplicity)
 * Copyright (c) Jon Lennryd (http://jonlennryd.madskullcreations.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/EditablePages/ with content from EditablePages table.
 */
class EditablePagesController extends AppController
{

    /**
     * Displays a view
     *
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display()
    {
			$path = func_get_args();
			debug($path);

			$count = count($path);
			if (!$count) {
				return $this->redirect('/');
			}
			$page = $subpage = null;

			if (!empty($path[0])) {
				$page = $path[0];
			}
			if (!empty($path[1])) {
				$subpage = $path[1];
			}
			$this->set(compact('page', 'subpage'));

			// TODO: Try to load the content of the given page.
			
// TODO: Se http://stackoverflow.com/questions/33338038/best-way-to-see-if-a-view-file-ctp-exists-in-cake-php
// <-Tanken här är helt enkelt att tillåta användaren att skapa en .ctp fil per sida om det behövs, som för PagesController,
//   men för det mesta kan det räcka med att rendera standard .ctp fil, som endast laddar in sidans innehåll från databasen. 
// 

			// Tries to render specific .ctp file. If it does not exist, fall back to the default .ctp file. 
			try 
			{
				$this->render(implode('/', $path));
			} 
			catch (MissingTemplateException $e) 
			{
				$this->render('default');
			}
	}
}
