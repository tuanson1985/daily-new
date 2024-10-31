<?php

use  Illuminate\Contracts\Container\Container;

// Aside menu
return [

    'items' => [
        // Dashboard
        [
            'title' => 'Dashboard',
            'root' => true,
            'permission' => '',
            'icon' => 'assets/backend/themes/media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'route' => 'admin.index',
            'page' => '',
            'new-tab' => false,
        ],
        [
            'title' => 'Thông tin tài khoản',
            'root' => true,
            'permission' => 'profile-show',
            'icon' => 'far fa-user-circle', // or can be 'flaticon-home' or any flaticon-*
            'route' => 'admin.profile',
            'page' => '',
            'new-tab' => false,
        ],
        [
            'title' => 'Bảo mật tài khoản',
            'root' => true,
            'icon' => 'flaticon-user-settings', // or can be 'flaticon-home' or any flaticon-*
            'route' => 'admin.security-2fa.index',
            'permission' =>'security-2fa',
            'page' => '',
        ],
        [
            'title' => 'Export',
            'root' => true,
            'icon' => 'menu-icon flaticon-multimedia-3 font-weight-bold', // or can be 'flaticon-home' or any flaticon-*
            'route' => 'admin.dashboard.export',
            'permission' =>'dashboard-export-excel',
            'page' => '',
        ],
        [
            'title' => 'Rút tiền ATM - Ví',
            'root' => true,
            'icon' => 'la la-bank', // or can be 'flaticon-home' or any flaticon-*
            'route' => '',
            'permission' =>'withdraw-money,withdraw-money-history,bank-account,bank-setting',
            'page' => '',
            'new-tab' => false,
            'bullet' => 'dot',
            'submenu' => [
                [
                    'title' => 'Rút tiền',
                    'route' => 'admin.withdraw.index',
                    'permission' =>'withdraw-money',
                    'page' => '',
                ],
                [
                    'title' => 'Lịch sử rút tiền',
                    'route' => 'admin.withdraw-history.index',
                    'permission' =>'withdraw-money-history',
                    'page' => '',
                ],
                [
                    'title' => 'Tài khoản ngân hàng',
                    'route' => 'admin.bank-account.index',
                    'permission' =>'bank-account',
                    'page' => '',
                ],
                [
                    'title' => 'Cấu hình rút tiền ATM',
                    'route' => 'admin.bank-setting.index',
                    'permission' =>'bank-setting',
                    'page' => '',
                ],
            ]
        ],
        [
            'section' => 'Quản lý Game',
            'permission' => '',
        ],
        [
            'title' => 'Danh sách game',
            'desc' => '',
            'icon' => 'la la-shopping-bag',
            'bullet' => 'dot',
            'route' => 'admin.provider.index',
            'permission' => 'provider-list',
        ],

        // [
        //     'title' => 'Log viewer',
        //     'root' => true,
        //     'icon' => 'fas fa-bug', // or can be 'flaticon-home' or any flaticon-*
        //     'route' => 'admin.log.viewer',
        //     'permission' => '',
        //     'page' => '',
        //     'new-tab' => false,
        // ],

//
//        [
//            'section' => 'Mini game',
//            'permission' => 'minigame-game,minigame-seedingpackage,minigame-category,minigame-type,minigame,minigame-acc,minigame-log,minigame-logacc,minigame-statitics'
//        ],
//        [
//            'title' => 'Quản lý minigame',
//            'desc' => '',
//            'icon' => 'fas fa-dice-d6',
//            'bullet' => 'dot',
//            'permission' => 'txnsvp-report-list,minigame-game,minigame-seedingpackage,minigame-category-updateitem,minigame-category-activeshop,minigame-category-deleteitem,minigame-category-replication,minigame-category-distribution,minigame-category-list,minigame-category-create,minigame-category-edit,minigame-type,minigame,minigame-acc,minigame-log,minigame-logacc,minigame-statitics',
//            'submenu' => [
//                [
//                    'title' => 'Seeding package',
//                    'route' => 'admin.minigame-seedingpackage.index',
//                    'permission' => 'minigame-seedingpackage',
//                    'page' => '',
//                ],
////                [
////                    'title' => 'Seeding chat',
////                    'route' => 'admin.minigame-seedingchat.index',
////                    'permission' => 'minigame-seedingchat',
////                    'page' => '',
////                ],
//                [
//                    'title' => 'Danh sách minigame',
//                    'route' => 'admin.minigame-category.index',
//                    'permission' => 'minigame-category-list',
//                    'page' => '',
//                ],
//                [
//                    'title' => 'Danh sách loại giải thưởng',
//                    'route' => 'admin.minigame-type.index',
//                    'permission' => 'minigame-type',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Danh sách giải thưởng',
//                    'route' => 'admin.minigame.index',
//                    'permission' => 'minigame',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Danh sách nick thưởng',
//                    'route' => 'admin.minigame-acc.index',
//                    'permission' => 'minigame-acc',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Lịch sử trúng thưởng vp',
//                    'route' => 'admin.minigame-log.index',
//                    'permission' => 'minigame-log',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Lịch sử trúng thưởng acc',
//                    'route' => 'admin.minigame-logacc.index',
//                    'permission' => 'minigame-logacc',
//                    'page' => ''
//                ],
////                [
////                    'title' => 'Thống kê minigame',
////                    'route' => 'admin.minigame-statitics.index',
////                    'permission' => 'minigame-statitics',
////                    'page' => ''
////                ],
//                [
//                    'title' => 'Biến động số dư vật phẩm',
//                    'route' => 'admin.txnsvp-report.index',
//                    'permission' => 'txnsvp-report-list',
//                    'page' => ''
//                ],
//            ],
//        ],
//        [
//            'title' => 'Rút vật phẩm',
//            'desc' => '',
//            'icon' => 'fas fa-credit-card',
//            'bullet' => 'dot',
//            'permission' => 'withdraw-item,withdraw-gametype,withdraw-package,withdraw-history,withdraw-statitics,withdraw-package-statitics,withdraw-item-auto,withdraw-package-config',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Cấu hình thông tin người rút',
//                    'route' => 'admin.gametype.index',
//                    'permission' => 'withdraw-gametype',
//                    'page' => '',
//                ],
//                [
//                    'title' => 'Cấu hình gói rút',
//                    'route' => 'admin.package.index',
//                    'permission' => 'withdraw-package',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Cấu hình gói rút cho từng shop',
//                    'route' => 'admin.package-config.index',
//                    'permission' => 'withdraw-package-config',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Lịch sử rút vật phẩm',
//                    'route' => 'admin.withdraw-item.index',
//                    'permission' => 'withdraw-item',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Lịch sử rút vật phẩm (Auto)',
//                    'route' => 'admin.withdraw-item-auto.index',
//                    'permission' => 'withdraw-item-auto',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Thống kê rút vật phẩm',
//                    'route' => 'admin.withdraw-statitics.index',
//                    'permission' => 'withdraw-statitics',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Thống kê rút vật phẩm theo gói',
//                    'route' => 'admin.withdraw-package-statitics.index',
//                    'permission' => 'withdraw-package-statitics',
//                    'page' => ''
//                ]
//            ]
//        ],



//        [
//            'section' => 'Nội dung website',
//            'permission' => 'page-category',
//        ],
//        [
//            'title' => 'Menu trang chủ',
//            'icon' => 'far fa-list-alt',
//            'bullet' => 'line',
//            'permission' => 'menu-category',
//            'route' => 'admin.menu-category.index',
//            'page' => ''
//
//        ],
//        [
//            'title' => 'Quản lý menu',
//            'desc' => '',
//            'icon' => 'far fa-list-alt',
//            'bullet' => 'dot',
//            'permission' => 'menu-category-list,menu-profile-list,menu-transaction-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Menu trang chủ',
//                    'route' => 'admin.menu-category.index',
//                    'permission' => 'menu-category-list',
//                    'page' => '',
//                ],
//                [
//                    'title' => 'Menu profile',
//                    'route' => 'admin.menu-profile.index',
//                    'permission' => 'menu-profile-list',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Menu nhanh',
//                    'route' => 'admin.menu-transaction.index',
//                    'permission' => 'menu-transaction-list',
//                    'page' => ''
//                ],
//            ]
//        ],
//        [
//            'title' => 'Trang nội dung',
//            'icon' => 'fas fa-newspaper',
//            'bullet' => 'line',
//            'permission' => 'page-category',
//            'route' => 'admin.page.index',
//            'page' => ''
//
//        ],
        //[
        //    'title' => 'Quản lý game',
        //    'desc' => '',
        //    'icon' => 'fas fa-gamepad',
        //    'bullet' => 'dot',
        //    'permission' => 'game-category-list',
        //    'root' => true,
        //    'submenu' => [
        //        [
        //            'title' => 'Tất cả game',
        //            'route' => 'admin.game.index',
        //            'permission' => 'game-list',
        //            'page' => '',
        //        ],
        //
        //        [
        //            'title' => 'Danh mục game',
        //            'route' => 'admin.game-category.index',
        //            'permission' => 'game-category-list',
        //            'page' => ''
        //        ],
        //        [
        //           'title' => 'Nhóm game',
        //           'route' => 'admin.game-group.index',
        //           'permission' => 'game-group-list',
        //           'page' => ''
        //        ],
        //    ]
        //],
//        [
//            'title' => 'Quản lý Kho nick',
//            'desc' => '',
//            'icon' => 'fas fa-gem',
//            'bullet' => 'dot',
//            'permission' => 'acc-list,acc-edit,acc-property,acc-history,acc-analytic',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'DS Nick Thường',
//                    'route' => 'admin.acc_type_1',
//                    'permission' => 'acc-list-1',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'DS Nick Random',
//                    'route' => 'admin.acc_type_2',
//                    'permission' => 'acc-list-2',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Danh mục & Thuộc tính',
//                    'route' => 'admin.acc.property',
//                    'permission' => 'acc-property',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Lịch sử bán acc',
//                    'route' => 'admin.acc.history',
//                    'permission' => 'acc-history',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Thống kê acc',
//                    'route' => 'admin.acc.analytic',
//                    'permission' => 'acc-analytic',
//                    'page' => ''
//                ],
//            ]
//        ],
//        [
//            'title' => 'Quản lý auto link',
//            'desc' => '',
//            'icon' => 'flaticon-notes',
//            'bullet' => 'dot',
//            'permission' => 'auto-link-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Tất cả auto link',
//                    'route' => 'admin.auto-link.index',
//                    'permission' => 'auto-link-list',
//                    'page' => '',
//                ],
//            ]
//        ],
//        [
//            'title' => 'Quản lý bài viết',
//            'desc' => '',
//            'icon' => 'flaticon-notes',
//            'bullet' => 'dot',
//            'permission' => 'article-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Tất cả bài viết',
//                    'route' => 'admin.article.index',
//                    'permission' => 'article-list',
//                    'page' => '',
//                ],
//
//                [
//                    'title' => 'Danh mục bài viết',
//                    'route' => 'admin.article-category.index',
//                    'permission' => 'article-category-list',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Nhóm bài viết',
//                    'route' => 'admin.article-group.index',
//                    'permission' => 'article-group-list',
//                    'page' => ''
//                ],
//
//                // [
//                //     'title' => 'Cấu hình',
//                //     'route' => 'admin.language-key.index',
//                //     'page' => ''
//                // ],
//            ]
//        ],
//        [
//            'title' => 'Quản lý quảng cáo',
//            'desc' => '',
//            'icon' => 'flaticon-multimedia-3',
//            'bullet' => 'dot',
//            'permission' => 'advertise-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Tất cả quảng cáo',
//                    'route' => 'admin.advertise.index',
//                    'permission' => 'advertise-list',
//                    'page' => '',
//                ],
////                [
////                    'title' => 'Quảng cáo ADS',
////                    'route' => 'admin.advertise-ads.index',
////                    'permission' => 'advertise-ads-list',
////                    'page' => '',
////                ],
//                // [
//                //     'title' => 'Cấu hình',
//                //     'route' => 'admin.language-key.index',
//                //     'page' => ''
//                // ],
//            ]
//        ],
        [
            'section' => 'Tài khoản',
            'permission' => 'user-list,user-qtv-list',
        ],
        [
            'title' => 'Danh sách QTV',
            'icon' => 'fas fa-user-cog',
            'bullet' => 'line',
            'permission' => 'user-qtv-list',
            'route' => 'admin.user-qtv.index',
            'page' => ''

        ],
        [
            'title' => 'Danh sách CTV',
            'icon' => 'fas fa-user-cog',
            'bullet' => 'line',
            'permission' => 'user-qtv-list',
            'route' => 'admin.user-ctv.index',
            'page' => ''

        ],
        [
            'title' => 'Danh sách thành viên',
            'icon' => 'fas fa-user',
            'bullet' => 'line',
            'permission' => 'user-list',
            'route' => 'admin.user.index',
            'page' => ''

        ],

        [
            'section' => 'Quản lý dịch vụ',
            'permission' => '',
        ],
        [
            'title' => 'Dịch vụ',
            'desc' => '',
            'icon' => 'flaticon-notes',
            'bullet' => 'dot',
            'permission' => 'service-list,service-config-list',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Tất cả dịch vụ',
                    'route' => 'admin.service.index',
                    'permission' => 'service-list',
                    'page' => '',
                ],

                [
                    'title' => 'Danh mục dịch vụ',
                    'route' => 'admin.service-category.index',
                    'permission' => 'service-category-list',
                    'page' => ''
                ],
                //[
                //    'title' => 'Nhóm dịch vụ',
                //    'route' => 'admin.service-group.index',
                //    'permission' => 'service-group-list',
                //    'page' => ''
                //],

                // [
                //     'title' => 'Cấu hình',
                //     'route' => 'admin.language-key.index',
                //     'page' => ''
                // ],

//                [
//                    'title' => 'Cấu hình dịch vụ cho từng shop',
//                    'route' => 'admin.service-config.index',
//                    'permission' => 'service-config-list',
//                    'page' => ''
//                ],
            ]
        ],


        [
            'title' => 'Tool game dịch vụ',
            'desc' => '',
            'icon' => 'la fab la-gg-circle',
            'bullet' => 'dot',
            'permission' => 'nrogem-info-bot-list,nrogem-usernap-list,nrogem-logtransaction-list,nrocoin-info-bot-list,nrocoin-usernap-list,nrocoin-logtransaction-list,langlacoin-info-bot-list,langlacoin-usernap-list,langlacoin-logtransaction-list,roblox-info-bot-list,roblox-logtransaction-list,roblox-gem-info-bot-list,roblox-gem-logtransaction-list',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Bán ngọc - NRO',
                    'route' => '',
                    'permission' => 'nrogem-info-bot-list,nrogem-usernap-list,nrogem-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Thông tin bot',
                            'route' => 'admin.nrogem-info-bot.index',
                            'permission' => 'nrogem-info-bot-list',
                            'page' => '',
                        ],

                        [
                            'title' => 'Cấu hình user nạp',
                            'route' => 'admin.nrogem-usernap.index',
                            'permission' => 'nrogem-usernap-list',
                            'page' => ''
                        ]
                        ,[
                            'title' => 'Thống kê giao dịch',
                            'route' => 'admin.nrogem-logtransaction.index',
                            'permission' => 'nrogem-logtransaction-list',
                            'page' => ''
                        ]
                    ]
                ],

                [
                    'title' => 'Bán vàng - NRO',
                    'route' => '',
                    'permission' => 'nrocoin-info-bot-list,nrocoin-usernap-list,nrocoin-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Thông tin bot',
                            'route' => 'admin.nrocoin-info-bot.index',
                            'permission' => 'nrocoin-info-bot-list',
                            'page' => '',
                        ],

                        [
                            'title' => 'Cấu hình user nạp',
                            'route' => 'admin.nrocoin-usernap.index',
                            'permission' => 'nrocoin-usernap-list',
                            'page' => ''
                        ]
                        ,[
                            'title' => 'Thống kê giao dịch',
                            'route' => 'admin.nrocoin-logtransaction.index',
                            'permission' => 'nrocoin-logtransaction-list',
                            'page' => ''
                        ]
                    ]
                ],

                [
                    'title' => 'Bán bạc - Làng lá',
                    'route' => '',
                    'permission' => 'langlacoin-info-bot-list,langlacoin-usernap-list,langlacoin-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Thông tin bot',
                            'route' => 'admin.langlacoin-info-bot.index',
                            'permission' => 'langlacoin-info-bot-list',
                            'page' => '',
                        ],

                        [
                            'title' => 'Cấu hình user nạp',
                            'route' => 'admin.langlacoin-usernap.index',
                            'permission' => 'langlacoin-usernap-list',
                            'page' => ''
                        ]
                        ,[
                            'title' => 'Thống kê giao dịch',
                            'route' => 'admin.langlacoin-logtransaction.index',
                            'permission' => 'langlacoin-logtransaction-list',
                            'page' => ''
                        ]
                    ]
                ],

                [
                    'title' => 'Bán xu - Ninja Shool',
                    'route' => '',
                    'permission' => 'ninjaxu-info-bot-list,ninjaxu-usernap-list,ninjaxu-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Thông tin bot',
                            'route' => 'admin.ninjaxu-info-bot.index',
                            'permission' => 'ninjaxu-info-bot-list',
                            'page' => '',
                        ],

                        [
                            'title' => 'Cấu hình user nạp',
                            'route' => 'admin.ninjaxu-usernap.index',
                            'permission' => 'ninjaxu-usernap-list',
                            'page' => ''
                        ]
                        ,[
                            'title' => 'Thống kê giao dịch',
                            'route' => 'admin.ninjaxu-logtransaction.index',
                            'permission' => 'ninjaxu-logtransaction-list',
                            'page' => ''
                        ]
                    ]
                ],

                [
                    'title' => 'Bán Roblox',
                    'route' => '',
                    'permission' => 'roblox-info-bot-list,roblox-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Thông tin bot',
                            'route' => 'admin.roblox-info-bot.index',
                            'permission' => 'roblox-info-bot-list',
                            'page' => '',
                        ],
                        [
                            'title' => 'Thông tin bot sàn',
                            'route' => 'admin.roblox-info-bot-san.index',
                            'permission' => 'roblox-info-bot-san-list',
                            'page' => '',
                        ],
                        [
                            'title' => 'Thống kê giao dịch',
                            'route' => 'admin.roblox-logtransaction.index',
                            'permission' => 'roblox-logtransaction-list',
                            'page' => ''
                        ]
                    ]
                ],

                [
                    'title' => 'Bán Roblox Gem',
                    'route' => '',
                    'permission' => 'roblox-gem-info-bot-list,roblox-gem-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Thông tin bot',
                            'route' => 'admin.roblox-gem-info-bot.index',
                            'permission' => 'roblox-gem-info-bot-list',
                            'page' => '',
                        ],
                        [
                            'title' => 'Thống kê giao dịch',
                            'route' => 'admin.roblox-gem-logtransaction.index',
                            'permission' => 'roblox-gem-logtransaction-list',
                            'page' => ''
                        ]
                    ]
                ],

                [
                    'title' => 'Bán rbxapi',
                    'route' => '',
                    'permission' => 'rbxapi-info-bot-list,rbxapi-logtransaction-list',
                    'bullet' => 'dot',
                    'page' => '',
                    'submenu' => [
                        [
                            'title' => 'Get stock',
                            'route' => 'admin.rbxapi-info-bot.index',
                            'permission' => 'rbxapi-info-bot-list',
                            'page' => '',
                        ],
//                        [
//                            'title' => 'Thống kê giao dịch',
//                            'route' => 'admin.rbxapi-logtransaction.index',
//                            'permission' => 'rbxapi-logtransaction-list',
//                            'page' => ''
//                        ]
                    ]
                ],

            ]
        ],

        [
            'title' => 'D.sách đơn dịch vụ tự động',
            'desc' => '',
            'icon' => 'la la-shopping-cart',
            'bullet' => 'dot',
            'route' =>'admin.service-purchase-auto.index',
            'permission' => 'service-purchase-auto-list',


        ],

        [
            'title' => 'D.sách đơn dịch vụ thủ công',
            'desc' => '',
            'icon' => 'la la-shopping-bag',
            'bullet' => 'dot',
            'route' => 'admin.service-purchase.index',
            'permission' => 'service-purchase-list',
            'label' => [
                'type'=>'label-danger label-inline serivce_purchase_label',
                'value'=>'0',

            ]


        ],

//
//        [
//            'section' => 'Quản lý nạp tiền tự động',
//            'permission' => 'telecom-list,telecom-list,charge-report-list,transfer-list,transfer-bank-list,telecom-list,charge-report-list',
//        ],
//        [
//            'title' => 'Nạp thẻ tự động',
//            'desc' => '',
//            'icon' => 'flaticon-interface-9',
//            'bullet' => 'dot',
//            'permission' => 'telecom-list,charge-report-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Cài đặt nạp thẻ tự động',
//                    'route' => 'admin.telecom.index',
//                    'permission' => 'telecom-list',
//                    'page' => ''
//                ],
//                [
//                    'title' => 'Thống kê nạp thẻ',
//                    'route' => 'admin.charge-report.index',
//                    'permission' => 'charge-report-list',
//                    'page' => '',
//                ],
//            ]
//        ],
//        [
//            'title' => 'Nạp Ví - ATM tự động',
//            'desc' => '',
//            'icon' => 'flaticon-graphic',
//            'bullet' => 'dot',
//            'permission' => 'transfer-list,transfer-bank-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Quản lý ngân hàng',
//                    'route' => 'admin.transfer-bank.index',
//                    'permission' => 'transfer-bank-list',
//                    'page' => '',
//                ],
//                [
//                    'title' => 'Lịch sử giao dịch',
//                    'route' => 'admin.transfer.index',
//                    'permission' => 'transfer-report',
//                    'page' => ''
//                ],
//            ]
//        ],
        // [
        //     'title' => 'Nạp tiền qua cổng thanh toán',
        //     'desc' => '',
        //     'icon' => 'flaticon-bag',
        //     'permission' => 'charge-report-list',
        //     'bullet' => 'dot',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'Cài đặt chiết khấu',
        //             'route' => 'admin.deposit-bank-setting.index',
        //             'permission' => 'charge-report-list',
        //             'page' => ''
        //         ],
        //         [
        //             'title' => 'Thống kê nạp tiền',
        //             'route' => 'admin.deposit-bank-report.index',
        //             'permission' => 'charge-report-list',
        //             'page' => ''
        //         ],
        //     ]
        // ],

//        [
//            'section' => 'Quản lý mua thẻ',
//            'permission' => 'store-card-report-list,store-telecom-list',
//        ],
//        [
//            'title' => 'Cấu hình nhà mạng',
//            'icon' => 'flaticon-interface-3',
//            'bullet' => 'line',
//            'permission' => 'store-telecom-list',
//            'route' => 'admin.store-telecom.index',
//            'page' => ''
//
//        ],
//        [
//            'title' => 'Thống kê mua thẻ',
//            'icon' => 'flaticon-interface-3',
//            'bullet' => 'line',
//            'permission' => 'store-card-report-list',
//            'route' => 'admin.store-card-report.index',
//            'page' => ''
//
//        ],
//        [
//            'section' => 'Quản lý mã nhận thưởng',
//            'permission' => 'gift-code-report-list',
//        ],
//        [
//            'title' => 'Cấu hình mã nhận thưởng',
//            'icon' => 'flaticon-interface-3',
//            'bullet' => 'line',
//            'permission' => 'gift-code-list',
//            'route' => 'admin.gift-code.index',
//            'page' => ''
//        ],
//        [
//            'title' => 'Thống kê nhận thưởng',
//            'icon' => 'flaticon-interface-3',
//            'bullet' => 'line',
//            'permission' => 'gift-code-report-list',
//            'route' => 'admin.gift-code-report.index',
//            'page' => ''
//        ],
//        [
//            'section' => 'Quản lý tích điểm',
//            'permission' => 'point-report-list',
//        ],
//        [
//            'title' => 'Cấu hình tích điểm',
//            'icon' => 'flaticon-interface-3',
//            'bullet' => 'line',
//            'permission' => 'point-list',
//            'route' => 'admin.point.index',
//            'page' => ''
//        ],
//        [
//            'title' => 'Thống kê tích điểm',
//            'icon' => 'flaticon-interface-3',
//            'bullet' => 'line',
//            'permission' => 'point-report-list',
//            'route' => 'admin.point-report.index',
//            'page' => ''
//        ],
        [
            'section' => 'Quản lý giao dịch',
            'permission' => 'txns-report-list,txns-person-report-list,txnsvp-report-list,plus-minus-money-qtv,plusmoney-report-qtv',
        ],

        [
            'title' => 'Cộng trừ/tiền QTV(CTV)',
            'icon' => 'flaticon-plus',
            'permission' => 'plus-minus-money-qtv',
            'bullet' => 'line',
            'route' => 'admin.get_money_qtv',
            'page' => ''
        ],
        [
            'title' => 'Lịch sử Cộng trừ/tiền thành viên Shop',
            'icon' => 'flaticon-time-1',
            'permission' => 'plusmoney-report-qtv-list',
            'bullet' => 'line',
            'route' => 'admin.plusmoney-report-qtv.index',
            'page' => ''
        ],


        [
            'title' => 'Cộng trừ/tiền thành viên Shop',
            'icon' => 'flaticon-plus',
            'permission' => 'getMoney',
            'bullet' => 'line',
            'route' => 'admin.get_money',
            'page' => ''
        ],

        [
            'title' => 'Lịch sử Cộng trừ/tiền QTV(CTV)',
            'icon' => 'flaticon-time-1',
            'permission' => 'getMoney',
            'bullet' => 'line',
            'route' => 'admin.plusmoney-report.index',
            'page' => ''
        ],

        [
            'title' => 'Biến động số dư',
            'icon' => 'flaticon-piggy-bank',
            'permission' => 'txns-report-list,txns-person-report-list,txns-report-in-shop-list',
            'bullet' => 'line',
            'route' => 'admin.txns-report.index',
            'page' => ''
        ],
//        [
//            'title' => 'Biến động số dư vật phẩm',
//            'icon' => 'flaticon-app',
//            'permission' => 'txnsvp-report-list',
//            'bullet' => 'line',
//            'route' => 'admin.txnsvp-report.index',
//            'page' => ''
//        ],
//        [
//            'title' => 'Biến động số dư cộng trừ vật phẩm',
//            'icon' => 'flaticon-app',
//            'permission' => 'txnsvp-qtv-report-list',
//            'bullet' => 'line',
//            'route' => 'admin.txnsvp-qtv-report.index',
//            'page' => ''
//        ],
        [

            'title' => 'Duyệt rút tiền',
            'icon' => 'flaticon-coins',
            'permission' => 'confirm-withdraw',
            'bullet' => 'line',
            'route' => 'admin.confirm-withdraw.index',
            'page' => '',
            'label' => [
                'type'=>'label-danger label-inline confirm_withdraw_label',
                'value'=>'0',

            ]

        ],
//        [
//            'section' => 'Góp ý, Feedback',
//            'permission' => 'feedback-list',
//        ],
//        [
//            'title' => 'Cấu hình mục ý kiến',
//            'icon' => 'flaticon-plus',
//            'permission' => 'feedback-config-list',
//            'bullet' => 'line',
//            'route' => 'admin.feedback-config.index',
//            'page' => ''
//        ],
//        [
//            'title' => 'Tạo ý kiến',
//            'icon' => 'flaticon-plus',
//            'permission' => 'feedback-create',
//            'bullet' => 'line',
//            'route' => 'admin.feedback',
//            'page' => ''
//        ],
//
//        [
//            'title' => 'Danh sách ý kiến',
//            'icon' => 'flaticon-time-1',
//            'permission' => 'feedback-list',
//            'bullet' => 'line',
//            'route' => 'admin.feedback-list',
//            'page' => ''
//        ],

        // [
        //    'section' => 'Quản lý rút tiền',
        //    'permission' => 'withdraw-bank-list',
        // ],
        // [
        //    'title' => 'Duyệt lệnh rút tiền',
        //    'icon' => 'flaticon-refresh',
        //    'permission' => 'withdraw-list',
        //    'bullet' => 'line',
        //    'route' => 'admin.withdraw.index',
        //    'page' => ''
        // ],

//        [
//            'section' => 'Quản lý shop truy cập',
//            'permission' => 'shop-group-list,client-list,update-git-client',
//        ],
//        [
//            'title' => 'Cấu hình shop truy cập',
//            'icon' => 'flaticon-browser',
//            'permission' => 'client-list',
//            'bullet' => 'line',
//            'route' => 'admin.shop.index',
//            'page' => ''
//        ],
//        [
//            'title' => 'Nhóm shop',
//            'icon' => 'flaticon-web',
//            'permission' => 'shop-group-list',
//            'bullet' => 'line',
//            'route' => 'admin.shop-group.index',
//            'page' => ''
//        ],
//        [
//            'title' => 'Auto Deploy Github',
//            'icon' => 'flaticon-web',
//            'permission' => 'update-git-client',
//            'bullet' => 'line',
//            'route' => 'admin.shop-git.index',
//            'page' => ''
//        ],

        [
            'section' => 'Hệ thống',
            'permission' => 'setting-list',
        ],
//
//        [
//            'title' => 'Ngôn ngữ',
//            'desc' => '',
//            'icon' => 'fas fa-language',
//            'bullet' => 'dot',
//            'permission' => 'setting-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Ngôn ngữ hệ thống',
//                    'route' => 'admin.language-nation.index',
//                    'page' => '',
//                ],
//
//                [
//                    'title' => 'Từ khóa',
//                    'route' => 'admin.language-key.index',
//                    'page' => ''
//                ],
//
//                [
//                    'title' => 'Biên dịch',
//                    'route' => 'admin.language-mapping.index',
//                    'page' => ''
//                ]
//            ]
//        ],
//        [
//            'title' => 'Server',
//            'desc' => 'server',
//            'icon' => 'menu-icon flaticon-interface-3',
//            'bullet' => 'dot',
//            'permission' => 'setting-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Nhà phát hành',
//                    'route' => 'admin.server-category.index',
//                    'page' => '',
//                ],
//                [
//                    'title' => 'Danh mục server',
//                    'route' => 'admin.server-catalog.index',
//                    'page' => '',
//                ],
////                [
////                    'title' => 'Mảng server',
////                    'route' => 'admin.server-type.index',
////                    'page' => '',
////                ],
//                [
//                    'title' => 'Danh sách server',
//                    'route' => 'admin.server.index',
//                    'page' => '',
//                ]
//            ]
//        ],
//        [
//            'title' => 'Theme',
//            'desc' => 'theme',
//            'icon' => 'menu-icon flaticon-interface-3',
//            'bullet' => 'dot',
//            'permission' => 'theme-list,theme-attribute-list,theme-client-list',
//            'root' => true,
//            'submenu' => [
//                [
//                    'title' => 'Danh sách theme',
//                    'route' => 'admin.theme.index',
//                    'permission' => 'theme-list',
//                    'page' => '',
//                ],
//
//                [
//                    'title' => 'Thuộc tính theme',
//                    'permission' => 'theme-attribute-list',
//                    'route' => 'admin.theme-attribute.index',
//                    'page' => ''
//                ]
//                ,[
//                    'title' => 'Cấu hình theme cho client',
//                    'permission' => 'theme-client-list',
//                    'route' => 'admin.theme-client.index',
//                    'page' => ''
//                ]
//            ]
//        ],
        [
            'title' => 'Nhóm vai trò',
            'icon' => 'fas fa-crown',
            'bullet' => 'line',
            'route' => 'admin.role.index',
            'page' => ''

        ],
        [
            'title' => 'Quyền truy cập',
            'icon' => 'assets/backend/themes/media/svg/icons/Code/Git4.svg',
            'bullet' => 'line',
            'route' => 'admin.permission.index',
            'page' => ''

        ],
        [
            'title' => 'Log hoạt động',
            'icon' => 'assets/backend/themes/media/svg/icons/Devices/Diagnostics.svg',
            'bullet' => 'line',
            'route' => 'admin.activity-log.index',
            'page' => ''

        ],
        [
            'title' => 'Log error',
            'icon' => 'assets/backend/themes/media/svg/icons/Devices/Diagnostics.svg',
            'bullet' => 'line',
            'route' => 'admin.log.viewer',
            'page' => ''

        ],
//        [
//            'title' => 'API Document',
//            'icon' => 'assets/backend/themes/media/svg/icons/Devices/Diagnostics.svg',
//            'bullet' => 'line',
//            'route' => 'admin.api.document',
//            'page' => ''
//
//        ],
//        [
//            'title' => 'Cấu hình chung',
//            'icon' => 'assets/backend/themes/media/svg/icons/Code/Settings4.svg',
//            'bullet' => 'line',
//            'permission' => 'setting-list',
//            'route' => 'admin.setting.index',
//            'page' => ''
//        ],
    ]

];
