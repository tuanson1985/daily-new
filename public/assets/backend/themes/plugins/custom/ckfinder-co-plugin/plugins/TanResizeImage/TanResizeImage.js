/*
 * CKFinder - Sample Plugins
 * ==========================
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * This file and its contents are subject to the MIT License.
 * Please read the LICENSE.md file before using, installing, copying,
 * modifying or distribute this file or part of its contents.
 */

CKFinder.define( [ 'jquery', 'backbone' ], function( jQuery, Backbone ) {
    'use strict';
    /**
     * Sample plugin that alters the "Rename File" dialog window view.
     *
     * This plugin illustrates how to:
     *  - Alter existing dialog windows by overriding the default templates.
     *  - Read values from input elements in dialog windows.
     *  - Listen to dialog events (e.g. to execute code when the "OK" button is pressed).
     *  - Alter executed command to send additional data to the server side connector.
     */
    var TanResizeImage = {
        init: function( finder ) {



            //tạo nút crop & resize ở context menu
            finder.on( 'contextMenu:file:view', function( evt ) {


                console.log(finder);

                evt.data.items.add( {
                    label: 'Resize & Crop',
                    isActive: evt.data.context.file.get( 'folder' ).get( 'acl' ).fileView,
                    icon: 'ckf-view',
                    action: function() {
                        //ví lấy url file
                        //alert( evt.data.context.file.getUrl() );
                        var file=evt.data.context.file;
                        finder.request('ResizeAndCrop', { file: file });
                    }
                } );
            } );
            //tạo nút crop & resize ở tool bar
            finder.on( 'toolbar:reset:Main:file', function( evt ) {

                console.log(finder);
                var file = evt.data.file;
                evt.data.toolbar.push( {
                    name: 'resize_crop_image',
                    label: 'Resize & Crop',
                    // Place "Share" after the "Download" button.
                    priority: 65,
                    icon: 'share',
                    action: function() {

                         finder.request('ResizeAndCrop', { file: file });

                    }
                } );
            } );





            function ResizeAndCrop( data ) {
                // Data was passed in finder.request.
                // var template="";
                // var fileName = data.file.get( 'name' );
                // // alert('chay vào: '+fileName);

                var file = data.file;
                var fileUrl = file.getUrl();
                var fileName = file.get('name');
                alert(fileName);
                var folderName = file.get('folder').getPath({ full:true });
                var templateUrl= this.path + 'dialog.php?fileUrl=' + fileUrl + '&fileName=' + fileName + '&v='+ Date.now();
                finder.request( 'dialog', {
                    name: 'ResizeCropDialog',
                    title: 'Resize and Crop',
                    template: '<iframe id="iframe_imageNotation"  style="min-width: 500px;min-height: 300px;"   frameBorder="0" src=""></iframe>',
                    buttons: [ 'ok', 'cancel' ]
                } );

                var form=$("#iframe_imageNotation").attr('src', templateUrl);



                finder.once( 'dialog:ResizeCropDialog:ok', function( evt ) {


                    // finder.on( 'command:ok:GetFiles', function( evt ) {
                    //     // Your event handler
                    //     alert('dlkm');
                    //     console.log(evt);
                    // } );

                    // var vkl = finder.request( 'file:getActive' );
                    // vkl.name="fuck.jpg";

                    var form=$("#iframe_imageNotation").contents().find("#crop_submit");
                    console.log(form.serialize());
                    $.ajax({
                        type: "POST",
                        url: './plugins/TanResizeImage/lib/Process.php',
                        data: form.serialize(),
                        global: false,
                        dataType: "text",
                        success: function(data)
                        {
                            var myObj = $.parseJSON(data);
                            if(myObj.success==true){

                                var filenameCroped=myObj.filename;

                                //refresh folder
                                var currentFolder = finder.request( 'folder:getActive' );
                                finder.request( 'folder:getFiles', { folder : currentFolder } );

                                //sau khi refresh folder xong
                                finder.once('command:after:GetFiles', function( evt ) {

                                    var allFiles = finder.request( 'files:getCurrent' ).toArray();

                                    $.each( allFiles, function( key, value ) {
                                        //check đúng tên thì select nó
                                        if(filenameCroped==value.attributes.name){
                                            finder.request( 'files:select', { files:value } );
                                            finder.request( 'dialog:destroy' );



                                            return false;
                                        }
                                    });
                                } );
                                //hủy dialog
                                finder.request( 'dialog:destroy' );
                            }
                            else{
                                alert('Lỗi không thể resize ảnh');
                            }
                        }
                    });

                    //phần dưới là các code mẫu để tham khảo
                    // vkl.submit(function(e) {
                    //
                    //     alert('vao');
                    //     console.log('vao');
                    //     var url = form.attr('action');
                    //
                    //
                    //
                    //
                    // });

                    // //refresh folder
                    // var currentFolder = finder.request( 'folder:getActive' );
                    // finder.request( 'folder:getFiles', { folder : currentFolder } );
                    //
                    //
                    //
                    // finder.once('command:after:GetFiles', function( evt ) {
                    //
                    //     var allFiles = finder.request( 'files:getCurrent' );
                    //     finder.request( 'files:select', { files: allFiles.toArray()[0] } );
                    // } );
                    // finder.request( 'dialog:destroy' );

                    // finder.request( 'files:select:toggle', { files: allFiles.toArray() } );


                    // finder.request( 'files:select', { files: ['142162663_1859566127530819_609219948910988039_o_thumbnail_200x200_thumbnail_300x300_200x200.jpg'] } );


                    // Select all files in current folder (see files:selectAll).
                    //  var allFiles = finder.request('files:getSelected' );


                    // var vl=finder.request( 'files:getDisplayed' );
                    //
                    // vl.each(function ( item) {
                    //     var path=item.getUrl();
                    //     if(path){
                    //
                    //     }
                    //     console.log();
                    //     return;
                    // });


                    // alert(allFiles);
                    //
                    //  finder.request( 'files:select:toggle', { files: allFiles.toArray() } );
                    // console.log(allFiles);


                    //  finder.request( 'dialog:destroy' );
                    //
                    // var keywordObj = {
                    //     url: "/upload/userfiles/images/abc1.jpg",
                    // };
                    //
                    //
                    // finder.request( 'files:select', { files: keywordObj } );


                    // var allFiles = finder.request( 'files:getCurrent' ).toArray();
                    //
                    //
                    // // console.log(allFiles);
                    //
                    // // var fileNew= new Backbone.File('/upload/userfiles/images/IMG_3659.JPG');
                    //
                    // // var File = CKFinder.require( 'CKFinder/Models/File' );
                    // // var fileNew=File.name="'/upload/userfiles/images/IMG_3659.JPG'";
                    // $.each(allFiles, function(  index,value ) {
                    //     console.log(value);
                    //     console.log(value.attributes.name)
                    //     // console.log(fileNew)
                    //
                    //     // finder.request( 'files:choose', { files:value} )
                    //     finder.request( 'files:choose', { name:'IMG_3659.JPG' } )
                    //     // finder.request( 'files:select', { files: fileNew } );
                    //
                    //     return false; // breaks
                    // });


                    // finder.request( 'files:select:toggle', { files: fileNew } );
                    // console.log(fileNew);
                    // finder.request( 'files:select', { files: allFiles.toArray() } );

                    // var folder = finder.request( 'file:getActive' );
                    // console.log(folder);




                    // finder.request('folder:refreshFiles') ;

                    // finder.request( 'files:deselectAll' );

                } );

                finder.once( 'dialog:ResizeCropDialog:cancel', function( evt ) {
                    finder.request( 'dialog:destroy' );
                    finder.request('folder:refreshFiles') ;
                } );




            }

            finder.setHandler( 'ResizeAndCrop', ResizeAndCrop, this );


        }
    };

    return TanResizeImage;
} );
