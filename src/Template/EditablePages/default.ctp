<?php

use App\Controller\EditablePagesController;
/* 
 * 
 */

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
	// TODO: Nu ska RichTextElementsHelper komma till nytta, f�r den ska ha en funktion
	// f�r att rendera en edit-knapp. Resten sk�ter ju vyn om, renderingen etc.

	if(EditablePagesController::UserCanEditPages())
	{
		echo $this->Html->link(__('Edit page'), array('action' => 'edit', $element->id));
	}
?>