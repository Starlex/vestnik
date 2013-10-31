/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.skin = 'office2003';
	config.removePlugins = 'about, save, iframe, flash, newpage, print, smiley, forms';
	config.filebrowserUploadUrl = 'ckupload.php';
	config.width = '100%';
};
