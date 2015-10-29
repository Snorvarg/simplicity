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

use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/EditablePages/ with content from EditablePages table.
 */
class EditablePagesController extends AppController
{
    /**
     * Using the path as an identifier, it loads the content from database and tries to render a view 
     * with the same name. If there is no view file (.ctp) with the given identifier, it renders the 
     * default.cpt view file instead. 
     * 
     */
    public function display()
    {
			$path = func_get_args();
			// debug($path);

			$count = count($path);
			if(!$count) 
			{
				// Missing a path to use as identifier, just redirect home.
				return $this->redirect('/');
			}
			$page = $subpage = null;
			$identifier = '';
			
			if(!empty($path[0])) 
			{
				$page = $path[0];
				$identifier = $page;
			}
			if(!empty($path[1])) 
			{
				$subpage = $path[1];
				
				if($identifier != '')
				{
					$identifier .= '/'.$subpage;
				}
				else 
				{
					$identifier .= $subpage;
				}
			}
			$this->set(compact('page', 'subpage'));

			$language = "en-GB";
			
			$createIfNotExist = false;
			if(EditablePagesController::UserCanEditPages())
			{
				$createIfNotExist = true;
			}
			
			// Load the content of the current page.
			$richTextElements = TableRegistry::get('RichTextElements');

 			$element = $richTextElements->GetElement($identifier, $language, $createIfNotExist);
 			// debug($element);
 			
 			if($element == null)
 			{
 				// Element did not exist and visitor was not allowed to create a page.
 				$this->Flash->error(__('Page does not exist.'));
 				return $this->redirect('/');
 			}
 			
 			$this->set('element', $element);

			// Tries to render specific .ctp file. If it does not exist, fall back to the default .ctp file.
			// Using DS as we will check for a file's existence on the server.
 			$file = APP.'Template'.DS.'EditablePages'.DS.implode(DS, $path).'.ctp';
			//debug($file);
				
			if (file_exists($file)) 
			{
				// debug("File exists");
				$this->render(implode('/', $path));
			}
			else 
			{
				$this->render('default');
			}
		}
		
		public function edit($id = null)
		{
			if(EditablePagesController::UserCanEditPages() == false)
			{
				$this->Flash->error(__('You are not allowed to edit content of this page.'));
				return $this->redirect('/');
			}
			
			$richTextElements = TableRegistry::get('RichTextElements');
			
			if($richTextElements->exists(['id' => $id]) == false)
			{
				$this->Flash->error(__('The page could not be found.'));
				return $this->redirect('/');
			}

			$element = $richTextElements->get($id);
			
			if ($this->request->is(['post', 'put'])) 
			{
				// debug($this->request->data);
				// debug($element);
				
				// Copy values into the element while also validating the fields.
				$richTextElements->patchEntity($element, $this->request->data);
				
				// Now a 'dirty' flag is set for the 'content', hinting it has been modified but not yet saved.
				// The 'modified' flag is not yet updated as it happens right before saving. 
				// 
				// debug($element);
				
				// Save the element with it's changes.
				if ($richTextElements->save($element)) 
				{
					$this->Flash->success(__('Your page has been updated.'));
					
					$vals = explode('/', $element->identifier);
					
					if(count($vals) == 2)
					{
						// This is just to make it look nice in the url; if the identifier is "bread/cheese" we want the
						// url to become "display/bread/cheese" and not "display/bread%2Fost".
						// 
						return $this->redirect(['action' => 'display', $vals[0], $vals[1]]);
					}
					else
					{
						return $this->redirect(['action' => 'display', $element->identifier]);
					}
				}
				else
				{
					$this->Flash->error(__('The page could not be saved.'));
				}
			}
			
			$this->set('element', $element);
		}
		
		public static function UserCanEditPages()
		{
			// TODO: Add session logic here. 
			return true;
		}
}
