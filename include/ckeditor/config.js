/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

    config.toolbarGroups = [
        { name: 'basicstyles', groups: [ 'basicstyles' ] },
        { name: 'paragraph',   groups: [ 'list' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links'},
        { name: 'insert' },
        { name: 'forms' },
        { name: 'tools' },
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'others' },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'about' }
    ];
    
    // Remove buttons
	config.removeButtons = 'Anchor';
    
	// Dialog windows are also simplified.
	config.extraPlugins = 'link';
	config.extraPlugins = 'sourcearea';
	config.extraPlugins = 'sourcedialog';
	config.extraPlugins = 'codesnippet';
	config.extraPlugins = 'youtube';
	config.extraPlugins = 'link';
    config.extraPlugins = 'divarea';
    
    // Other tweaks
    config.toolbarCanCollapse = true;
};
