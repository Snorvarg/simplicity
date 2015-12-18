<?php 
/* Edit form for EditablePages.
 * 
 */

echo $this->TinyMCE->GetScript();
?>

<h1><?= __("Edit Page") ?></h1>

<p>
	<?= __('The page\'s current language is: ').'"'.$availableLanguageCodes[$element->i18n].'"'; ?>
</p>
<?php
    echo $this->Form->create($element);

    echo $this->Form->label(
    		'i18n', 
    		__('This page is missing in the following languages').' [?]', 
    		[
    				'title' => __('To create the page for a new language; Select a language below, edit and save. This will be saved as a new page.')
    		]);
    echo $this->Form->input(
    		'i18n', 
    		[
    				'options' => $missingLanguages, 
    				'label' => false,
    				'empty' => __('Select to create a new page in the choosen language...'),
    		]);
    
    echo $this->Form->input('content');
    echo $this->Form->button(__('Save Page'));
    echo $this->Form->end();
?>