CKEDITOR.editorConfig = function( config ) {
	config.toolbar = [
		{ name: 'basicstyles', items: [ 'Bold', 'Italic'] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent' ] },
		{ name: 'insert', items: ['HorizontalRule', 'Table' ] },
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
		{ name: 'links', items: [ 'Link', 'Unlink'] },
		{ name: 'styles', items: [ 'Styles' ] },
		{ name: 'styles', items: [ 'Format' ] },
		{ name: 'document', items: [ 'Source' ] }
	];

	config.format_tags = 'h4;h5';
	config.removeDialogTabs = 'link:advanced';
	config.allowedContent = true;
};