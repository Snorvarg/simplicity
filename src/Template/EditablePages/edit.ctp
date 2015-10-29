<?php 
/* Edit form for EditablePages.
 * 
 */
?>

<h1><?= __("Edit Page") ?></h1>

<?php
    echo $this->Form->create($element);
    echo $this->Form->input('content');
    echo $this->Form->button(__('Save Page'));
    echo $this->Form->end();
?>