/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    config.language = 'vi';
    config.entities_greek = false;
    config.entities_latin = false;
    config.toolbar = [

        { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
        { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        { name: 'links', items: [ 'Youtube','Link', 'Unlink' ] },
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
        { name: 'insert', items: [ 'Image', 'Table', 'Iframe' ] },
        { name: 'document', items: [ 'Source' ] },
        { name: 'tools', items: [ 'Maximize' ] },
        // { name: 'editing', items: [ 'Scayt' ] }
    ];

    config.allowedContent = {
        $1: {
            // Use the ability to specify elements as an object.
            elements: CKEDITOR.dtd,
            attributes: true,
            styles: true,
            classes: true
        }
    };
    config.disallowedContent = 'script; *[on*]; javascript* :';




    config.filebrowserBrowseUrl = '/assets/backend/themes/plugins/custom/ckfinder-laravel/ckfinder.html';
    config.filebrowserImageBrowseUrl = '/assets/backend/themes/plugins/custom/ckfinder-laravel/ckfinder.html?type=Images';
    config.filebrowserFlashBrowseUrl = '/assets/backend/themes/plugins/custom/ckfinder-laravel/ckfinder.html?type=Flash';

    config.filebrowserUploadUrl = '/assets/backend/themes/plugins/custom/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
    config.filebrowserImageUploadUrl = '/assets/backend/themes/plugins/custom/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
    config.filebrowserFlashUploadUrl = '/assets/backend/themes/plugins/custom/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';

    config.removePlugins = 'image';
    config.extraPlugins = 'image2,youtube';

};
