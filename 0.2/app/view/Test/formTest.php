<?php

//header('Content-type: text/plain');

ephFrame::loadClass('ephFrame.lib.component.Form.Form');
$form = new Form('index.php');

$form->add(
	$form->newField('text', 'ballon', 'this should be just a test')->label('Text'),
	$form->newField('textArea', 'flugzeug', 'this should be just a test')->label('Textarea'),
	$form->newField('hidden', 'franklin', 'hallo text'),
	$form->newField('integer', 'master', 500),
	$form->newField('float', 'car', 2.3),
	$form->newField('URL', 'go'),
	$form->newField('checkbox', 'checkicheck', 'mein arsch brennt')->label('Checkbox')
);

echo $form;

?>