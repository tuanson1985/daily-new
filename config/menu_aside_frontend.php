<?php

use  Illuminate\Contracts\Container\Container;

// Aside menu
return [

    'items' => [
        // Dashboard
        [
            'title' => 'Merchant',
            'root' => true,
            'permission' => '',
            'icon' => 'assets/backend/themes/media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'route' => 'frontend.index',
            'page' => '',
            'new-tab' => false,
        ],
        [
            'title' => 'Thông tin tài khoản',
            'root' => true,
            'icon' => 'far fa-user-circle', // or can be 'flaticon-home' or any flaticon-*
            'route' => 'frontend.profile',
            'page' => '',
            'new-tab' => false,
        ],
        [
            'section' => 'Quản lý giao dịch',
            'permission' => 'txns-report-list,txns-person-report-list,txnsvp-report-list,plus-minus-money-qtv,plusmoney-report-qtv,plusmoney-report-list',
        ],
        [
            'title' => 'Biến động số dư',
            'icon' => 'flaticon-piggy-bank',
            'permission' => 'txns-report-list,txns-person-report-list,txns-report-in-shop-list',
            'bullet' => 'line',
            'route' => 'frontend.txns-report.index',
            'page' => ''
        ],
        [
            'section' => 'Quản lý dịch vụ',
            'permission' => '',
        ],
        [
            'title' => 'D.sách đơn dịch vụ tự động',
            'desc' => '',
            'icon' => 'la la-shopping-cart',
            'bullet' => 'dot',
            'route' =>'frontend.service-purchase-auto.index',
            'permission' => 'service-purchase-auto-list',


        ],

        [
            'title' => 'D.sách đơn dịch vụ thủ công',
            'desc' => '',
            'icon' => 'la la-shopping-bag',
            'bullet' => 'dot',
            'route' => 'frontend.service-purchase.index',
            'permission' => 'service-purchase-list',
            'label' => [
                'type'=>'label-danger label-inline serivce_purchase_label',
                'value'=>'0',
            ]
        ],
    ]

];
