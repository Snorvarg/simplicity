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
 * Editable content controller
 *
 * This controller will render views from Template/EditablePages/ with content from EditablePages table.
 * 
 */
class EditablePagesController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Menu');
	}
    /**
     * Using the path as an identifier, it loads the content from database and tries to render a view 
     * with the same name. If there is no view file (.ctp) with the given identifier, it renders the 
     * default.cpt view file instead. 
     * 
     */
    public function display()
    {
			$path = func_get_args();
			//debug($path);
			
			$count = count($path);
			if(!$count) 
			{
				// Missing a path to use as identifier, just redirect home.
				return $this->redirect('/');
			}
			
			// TEST: Det funkar bra att få ut trädet så länge parent inte är null. Du måste fixa null med.
			$tree = $this->Menu->GetTree(1, 3);
// 			debug($tree);
			
			// The last element in path is always the page, all others are categories.
			$categoryNames = $path;
			$pageName = array_pop($categoryNames);			
			
			//debug($categoryNames);
			//debug($pageName);
			//debug(AppController::$selectedLanguage);

			$language = AppController::$selectedLanguage;
			
			$createIfNotExist = false;
			if(EditablePagesController::UserCanEditPages())
			{
				$createIfNotExist = true;
			}

			// If there are more parts of the url, lets make a category-tree out of it. 
			if(count($categoryNames) > 0)
			{
				$categories = TableRegistry::get('Categories');
				//debug($categories);
				
				// Get the path, or null if it does not exist and is not allowed to create it. 
	 			$lastCategory = $categories->GetPath($categoryNames, true, $createIfNotExist);
	 			// debug($lastCategory);
				
	 			if($lastCategory == null)
	 			{
	 				// The path does not exist, redirect home.
	 				$this->Flash->error(__('Path does not exist.'));
	 				return $this->redirect('/');
	 			}
	 			
	 			$categoryId = $lastCategory->id;
			}
			else 
			{
				// This page is a root page, it has no parent category.
				$categoryId = null;
			}
 			
			// Load the content of the current page.
			$richTextElements = TableRegistry::get('RichTextElements');
				
 			$element = $richTextElements->GetElement(
 					$pageName, $categoryId, $language, $createIfNotExist);
 			//debug($element);
 			
 			if($element == null)
 			{
 				// Element did not exist and visitor was not allowed to create a page.
 				$this->Flash->error(__('Page does not exist.'));
 				return $this->redirect('/');
 			}
 			
 			$this->set(compact('categoryNames', 'pageName', 'language', 'element', 'tree'));

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
				//debug($this->request->data);
				//debug($element);
				
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
					
					// Get path for the page.
					$categories = TableRegistry::get('Categories');
					$path = $categories->PathFor($element->category_id);
					$path .= $element->name;
					// debug($path);
					
					return $this->redirect($path);
				}
				else
				{
					$this->Flash->error(__('The page could not be saved.'));
				}
			}
			
			$this->set('element', $element);
		}

		public function delete($id = null)
		{
			if(EditablePagesController::UserCanEditPages() == false)
			{
				$this->Flash->error(__('You do not have permission to delete this page.'));
				return $this->redirect('/');
			}
			
			// Make sure only post and delete are allowed. Trying to load this page normally will yield an exception.
			// It is a safety-precaution as web crawlers could accidentally delete all content while exploring all links.
			$this->request->allowMethod(['post', 'delete']);
			
			$richTextElements = TableRegistry::get('RichTextElements');
				
			$element = $richTextElements->get($id);
						
			if($richTextElements->delete($element))
			{
				$this->Flash->success(__('The page was deleted.'));
				return $this->redirect('/');
			}
			
			$this->Flash->error(__('The page could not be deleted.'));
			return $this->redirect('/');
		}
		
		public static function UserCanEditPages()
		{
			// TODO: Add session logic here. 
			return true;
		}
}
