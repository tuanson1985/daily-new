/*
 Copyright (c) 2007-2019, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or https://ckeditor.com/sales/license/ckfinder
 */
//xem cấu hình ở đây: https://ckeditor.com/docs/ckfinder/ckfinder3/?mobile=/api/CKFinder.Config
var config = {};

// Set your configuration options below.

// Examples:
// config.language = 'pl';
// config.skin = 'jquery-mobile';
// config.resizeImages=false;


config.defaultSortBy = 'date';
config.defaultSortByOrder = 'desc';
config.plugins = [

    //'CustomDialog', //mẫu share social
    // 'AlterDialogWindow', //mẫu sửa tên
    // 'CustomPage',
    // 'ImageInfo',
    'TanResizeImage',
    // 'MyPlugin',


];

CKFinder.define( config );
