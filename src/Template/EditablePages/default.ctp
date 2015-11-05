<?php

use App\Controller\EditablePagesController;

//debug($element->identifier);
?>

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
						$element->id],
				[
						'class' => 'button',
						'style' => 'margin-right: 10px;'
				]);
		echo $this->Form->postLink(
				__('Erase page'), 
				[
						'action' => 'delete', 
						$element->id],
				[
						'class' => 'button',
						'type' => 'post',
						'confirm' => __('Are you sure?')
				]);
	}
?>