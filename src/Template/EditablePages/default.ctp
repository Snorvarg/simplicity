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
						$element->id,
						'?' => ['korvar' => '42']
				],
				[
						'class' => 'button',
						'style' => 'margin-right: 10px;',
						'?' => ['korvar' => '12']
				]);
		echo $this->Form->postLink(
				__('Erase page'), 
				[
						'action' => 'delete', 
						$element->id,
						'?' => ['korvar' => '12'],
				],
				[
						'class' => 'button',
						'type' => 'post',
						'confirm' => __('Are you sure?'),
						'?' => ['korvar' => '42'],
				]);
	}
?>