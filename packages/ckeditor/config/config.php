<?php
return array
(
	'basepath' => Uri::base(FALSE).'assets/js/ckeditor/',
	'toolbar' => array(
		array('Source','-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'),
		array('Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt'),
		array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
		array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl'),
		array('Link','Unlink','Anchor'),
		array('Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'),
		array('Styles','Format','Font','FontSize'),
		array('Maximize', 'ShowBlocks','-','About'),
	),
);