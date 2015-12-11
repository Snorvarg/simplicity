<?php

use App\Controller\EditablePagesController;

//debug($element->identifier);
?>

<h3>Home menu</h3>
<?= $this->Menu->GetMenu($homeTree); ?>
<h3>Context menu</h3>
<?= $this->Menu->GetMenu($tree); ?>

<div>
	<?= $element->content ?>
</div>
<div>
	<?php $element->created ?>
</div>
<div>
	<?php $element->modified ?>
</div>

<?php
	// TODO: Nu ska RichTextElementsHelper komma till nytta, för den ska ha en funktion
	// för att rendera en edit-knapp. Resten sköter ju vyn om, renderingen etc.

	if(EditablePagesController::UserCanEditPages())
	{
		echo $this->Html->link(
				__('Edit page'), 
				[
						'action' => 'edit', 
						$element->id,
						'?' => ['korvar' => '42']
				],
				[
						'class' => 'button',
						'style' => 'margin-right: 10px;'
				]);
		
		// A postlink does not seem to be able to have "?lang=smurfiska".
		echo $this->Form->postLink(
				__('Erase page'), 
				[
						'action' => 'delete', 
						$element->id,
						'?' => ['franken' => 'stein']
				],
				[
						'class' => 'button',
						'type' => 'post',
						'confirm' => __('Are you sure?')
				]);
	}
?>