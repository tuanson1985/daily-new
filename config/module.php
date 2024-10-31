<?php


return [
    'media_s3' => 'https://khoanh.sgp1.cdn.digitaloceanspaces.com/',
    'point' => [
        'sticky' => [
            '1' => 'Nhận điểm cố định',
            '2' => 'Nhận điểm random',
        ],
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động'
        ],
        'condition' => [
            '0' => 'Không cần điểu kiện',
            '1' => 'Số tiền >= 10,000',
            '2' => 'Số tiền >= 20,000',
            '3' => 'Số tiền >= 50,000',
            '4' => 'Số tiền >= 100,000',
            '5' => 'Số tiền >= 200,000',
            '6' => 'Số tiền >= 500,000',
            '7' => 'Số tiền >= 1000,000'
        ]
    ],
    'feedback' => [
        'type' => [
            '1' => 'Lỗi hệ thống',
            '2' => 'Cải thiện hệ thống',
            '3' => 'Mở rộng kinh doanh',
            '4' => 'Khác'
        ],
        'status' => [
            '1' => 'Mở',
            '2' => 'Đóng',
            '3' => 'Đã tiếp nhận',
            '4' => 'Đang xử lý',
            '5' => 'Đã xử lý'
        ],
        'status1' => [
            '1' => 'Mở',
            '0' => 'Xóa'
        ],
        'view' =>[
            '1' => 'Đã xem',
            '0' => 'Chưa xem'
        ]
    ],
    'feedback-config' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ]
    ],
    'server-category' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ]
    ],
    'server-catalog' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ]
    ],
    'server-type' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ]
    ],
    'server' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ],
        'cf_status' => [
            '0' => 'Không trỏ qua CF',
            '1' => 'Có trỏ qua CF',
        ]
    ],
    'theme' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ],
    ],
    'theme-page' => [
        'key' => 'page-build',
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ],
    ],
    'theme-attribute' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hoạt động',
            '0' => 'Đã xóa'
        ],
        'is_image' => [
            '0' => 'Không',
            '1' => 'Có(Sẽ chọn ảnh ở mục Cấu hình theme cho client)'
        ]
    ],
    'minigame' => [
        'minigame-category' => ['title'=>"Danh mục minigame"],
        'minigame-seedingpackage' => ['title'=>"sedding package"],
        'minigame-acc' => ['title'=>"Danh sách nick thưởng minigame"],
        'minigame-log' => ['title'=>"Lịch sử quay thưởng trúng vật phẩm"],
        'minigame-logacc' => ['title'=>"Lịch sử quay thưởng trúng acc"],
        'minigame_type' => [
            'rubywheel' => 'Vòng quay',
            'flip' => 'Lật hình',
            'slotmachine' => 'Quay xèng',
            'slotmachine5' => 'Quay xèng 5 giải',
            'squarewheel' => 'Quay vòng vòng',
            'smashwheel' => 'Đập lu, rung cây, gieo quẻ',
        ],
        'game_type' => [
            '1' => 'Liên quân mobile (Quân huy)',
            '2' => 'Freefire (kim cương)',
            '3' => 'Liên minh',
            '4' => 'Hải tặc tí hon',
            '5' => 'Hiệp sĩ Online',
            '6' => 'Roblox (Thủ công)',
            '7' => 'Freefire (Thủ công)',
            '8' => 'Pubg mobile',
            '9' => 'Genshin Impact',
            '10' => 'BlockMango',
            '11' => 'Ninjaxu school',
            '12' => 'Ngọc rồng (Ngọc)',
            '13' => 'Roblox',
            '14' => 'Ngọc rồng (Vàng)',
        ],
        'game_type_map' => [
            '1' => 'lienquan',
            '2' => 'freefire',
            '3' => 'lienminh',
            '4' => 'ruby',
            '5' => 'knightageonline',
            '6' => 'roblox_ads',
            '7' => 'freefire_ads',
            '8' => 'pubgm',
            '9' => 'genesis_crystal',
            '10' => 'blockmango',
            '11' => 'ninjaxu',
            '12' => 'nrogem',
            '13' => 'roblox_buyserver',
            '14' => 'nrocoin',
        ],
        'payment_gateway'=>[
            '1' => 'SMS',
            '2' => 'GARENA'
        ],
        'gift_type' => [
            '0' => 'Thưởng vật phẩm',
            '1' => 'Thưởng nick'
        ],
        'winbox' => [
            '0' => 'Không trúng',
            '1' => 'Có trúng',
        ],
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Khóa'
        ],
        'item' => [
            'title'=>"Danh sách giải thưởng",
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
            ],
        ],
        'option' => [
            '0' => 'Không',
            '1' => 'Có',
        ],
        'itemtype' => [
            'title'=>"Danh sách loại giải thưởng",
        ],
        'package' => [
            'title'=>"Gói vật phẩm"
        ],
        'gametype' => [
            'title'=>"Cấu hình thông tin người rút"
        ],
        'withdraw-item' => [
            'title'=>"Lịch sử rút vật phẩm"
        ],
        'minigame-statitics' => [
            'title'=>"Thống kê minigame"
        ],
        'withdraw-statitics' => [
            'title'=>"Thống kê rút vật phẩm"
        ],
        'withdraw-package-statitics' => [
            'title'=>"Thống kê rút vật phẩm theo gói"
        ],
        'module' => [
            'package' => 'package',
            'withdraw-item' => 'withdraw-item',
            'withdraw-service-item' => 'withdraw-service-item',
            'gametype' => 'gametype'
        ],
        'minute_crom_order' => [
            'recharge' => 12,
            'delete' => 12,
        ],
        'fibonacci_recheck_time_order' => [
            '1' => 2,
            '2' => 3,
            '3' => 5,
            '4' => 8,
            '5' => 60,
        ],
        'withdraw_status' => [
            '0'=>'Chờ xử lý',
            '1'=>'Hoàn thành',
            '2'=>'Thanh toán lỗi',
            '3'=>'Giao dịch lỗi (xem tiến độ)',
            '7'=>'Kết nối NCC thất bại (7)',
            '9'=>'Kết nối NCC thất bại (9)',
//            '12'=>'Đã hoàn tiền'
        ],
        'category' => [
            'params_field'=>[
                [
                    'label' => 'User chơi theo quy luật số thứ tự (Cách nhau bởi dấu ,)', // you know what label it is
                    'name' => 'params[user_wheel]', // unique name for field
                    'type' => 'text', // input fields type
                    'data' => 'string', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                    'class' => '', // any class for input
                    'value' => '' // default value if you want
                ],
                [
                    'label' => 'Thứ tự các phần thưởng xuất hiện(Cách nhau bởi dấu ,)', // you know what label it is
                    'name' => 'params[user_wheel_order]', // unique name for field
                    'type' => 'text', // input fields type
                    'data' => 'string', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                    'class' => '', // any class for input
                    'value' => '' // default value if you want
                ],
                [
                    'label' => 'Thể lệ', // you know what label it is
                    'name' => 'params[thele]', // unique name for field
                    'type' => 'ckeditor', // input fields type
                    'data' => '', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                    'class' => '', // any class for input
                    'value' => '', // default value if you want
                    'height' => '300' // default height if you want

                ],
                [
                    'label' => 'Phần thưởng', // you know what label it is
                    'name' => 'params[phanthuong]', // unique name for field
                    'type' => 'ckeditor', // input fields type
                    'data' => '', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                    'class' => '', // any class for input
                    'value' => '', // default value if you want
                    'height' => '300' // default height if you want

                ],
                [
                    'label' => 'Loại tiền thanh toán', // you know what label it is
                    'name' => 'params[type_charge]', // unique name for field
                    'type' => 'select', // input fields type
                    'data' => '', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-4', // div parent class for input
                    'class' => '', // any class for input
                    'value' => 'demo', // default value if you want
                    'height' => '', // default height if you want ckfinder
                    'options' => [
                        0 => "Tiền thật",
                        1 => "Tiền khóa"
                    ] // default height if you want ckfinder
                ],
                [
                    'label' => 'Chơi thử', // you know what label it is
                    'name' => 'params[is_try]', // unique name for field
                    'type' => 'select', // input fields type
                    'data' => '', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-4', // div parent class for input
                    'class' => '', // any class for input
                    'value' => 'demo', // default value if you want
                    'height' => '', // default height if you want ckfinder
                    'options' => [
                        0 => "Không",
                        1 => "Có"
                    ] // default height if you want ckfinder
                ],
                [
                    'label' => 'Mã giảm giá', // you know what label it is
                    'name' => 'params[voucher]', // unique name for field
                    'type' => 'select', // input fields type
                    'data' => '', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-4', // div parent class for input
                    'class' => '', // any class for input
                    'value' => 'demo', // default value if you want
                    'height' => '', // default height if you want ckfinder
                    'options' => [
                        0 => "Tắt",
                        1 => "Bật"
                    ] // default height if you want ckfinder
                ],
                [
                    'label' => 'Point', // you know what label it is
                    'name' => 'params[point]', // unique name for field
                    'type' => 'select', // input fields type
                    'data' => '', // data type, string, int, boolean
                    'rules' => '', // validation rule of laravel
                    'div_parent_class' => 'col-12 col-md-4', // div parent class for input
                    'class' => '', // any class for input
                    'value' => 'demo', // default value if you want
                    'height' => '', // default height if you want ckfinder
                    'options' => [
                        0 => "Tắt",
                        1 => "Bật"
                    ] // default height if you want ckfinder
                ],
            ]
        ],
        'number_of_items' => [
            'number_of_items_smashwheel'=> 8,
            'number_of_items_flip'=> 9,
            'number_of_items_rubywheel'=> 8,
            'number_of_items_slotmachine'=> 7,
            'number_of_items_slotmachine5'=> 9,
            'number_of_items_squarewheel'=> 12,
        ],
    ],
    'withdraw-service-workflow' => [
        'key'=>"withdraw-service-workflow",
        'title'=>"",

    ],
    'withdraw-service-workname' => [
        'key'=>"withdraw-service-workname",
        'title'=>"Tên công việc dịch vụ",
    ],
    'roles' => [
        'type' => [
            '2' => 'QTV',
            '1' => 'Admin',
        ],
        'type_information' => [
            '0' => 'Việt Nam',
            '1' => 'Global',
            '2' => 'Sàn',
        ],
    ],
    'account_type' => [
        '1' => 'Cộng tác viên nhà',
        '3' => 'Cộng tác viên khách'
    ],

    'media' => [
        'url' => env('MEDIA_URL'),
    ],

    'user-qtv' => [
        'need_set_permission'=>true,
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Chờ kích hoạt',
            '0' => 'Khóa'
        ],
        'account_type' => [
            '1' => 'Quản trị viên (Nội bộ)',
            '3' => 'Cộng tác viên'
        ],
        'type_information_ctv' => [
            '1' => 'QTV/CTV nhà',
            '2' => 'QTV/CTV viên khách'
        ],
        'type_information' => [
            '0' => 'Việt Nam',
            '1' => 'Global',
            '2' => 'Sàn',
        ],
        'required_login_gmail' => [
            '0' => 'Không yêu cầu',
            '1' => 'Yêu cầu đăng nhập với Gmail',
        ],
    ],
    'user' => [
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Chờ kích hoạt',
            '0' => 'Khóa'
        ],
        'account_type' => [
            '1' => 'Quản trị viên (Nội bộ)',
            '2' => 'Thành viên',
            '3' => 'Cộng tác viên'
        ],
        'encryt' => env('ENCRYPT_USER'),
        'is_idol' => [
            '0' => 'Không',
            '1' => 'Có'
        ],
        'type_idol' => [
            '1' => 'Idol',
            '2' => 'Đang chờ phê duyệt'
        ],
        'effect_profile' => [
            '0' => 'Hiệu ứng tuyết rơi',
            '1' => 'Hiệu ứng trái tim rơi',
            '2' => 'Hiệu ứng mưa'
        ],
        'image_fake' => [
            '0' => 'https://media.passionzone.net/storage/upload/images/default-placeholder%20111(9).png',
            '1' => 'https://media.passionzone.net/storage/upload/images/logo%20pubg%20mobile.png',
            '2' => 'https://media.passionzone.net/storage/upload/images/toc-chien.jpg',
            '3' => 'https://media.passionzone.net/storage/upload/images/playtogether.jpg',
        ],
        'promotional' => [
            '1' => 'Gói tiên phong',
            '2' => 'Gói hỗ trợ',
        ],
        'payment_limit'=>1000000,
        'limit_fail_charge'=>5
    ],
    'shop' => [
        'title' => 'Danh sách điểm bán',
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
        'secret_client_backup' => env('SECRET_KEY_CLIENT_BACKUP'),
        'secret_key_client' => env('HASH_SECRET_KEY_CLIENT'),
        'secret_key_very_client' => env('SECRET_KEY_VERY_CLIENT'),
        'clone_module' => [
            '0' => [
                'key' => 'charge',
                'title' => 'Cấu hình nạp thẻ',
            ],
            '1' => [
                'key' => 'store_card',
                'title' => 'Cấu hình mua thẻ',
            ],
            '2' => [
                'key' => 'service',
                'title' => 'Cấu hình dịch vụ',
            ],
            '3' => [
                'key' => 'menu-category',
                'title' => 'Cấu hình menu trang chủ',
            ],
            '4' => [
                'key' => 'menu-profile',
                'title' => 'Cấu hình menu profile',
            ],
            '5' => [
                'key' => 'menu-transaction',
                'title' => 'Cấu hình menu nhanh',
            ],
            '6' => [
                'key' => 'article',
                'title' => 'Cấu hình bài viết',
            ],
            '7' => [
                'key' => 'theme',
                'title' => 'Cấu hình pages build',
            ],
            '8' => [
                'key' => 'setting',
                'title' => 'Cấu hình hình chung',
            ],
        ],
        'type_information' => [
            '0' => 'Shop nhà',
            '1' => 'Shop khách',
        ],
        'is_get_data' => [
            '0' => 'Không',
            '1' => 'Có',
        ],
    ],
    'shop-group' => [
        'title' => 'Nhóm điểm bán',
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
        'timezone' => [
            'GMT +7 (Hanoi, Bangkok)' => 'GMT +7 (Hanoi, Bangkok)'
        ],'currency' => [
            'VND' => 'VNĐ'
        ],
        'rate' =>[
            // phần này cập nhật lại theo ver đầu tiên của nhóm shop, vì hiện tại các module tính toán đang làm theo luồng cũ nhóm shop
            '0' => [
                'title' => 'Toàn bộ hệ thống',
                'key' => 'all',
                'params' => [
                    'additional_amount' => [
                        'title' => 'Số tiền cộng thêm',
                        'key' => 'additional_amount',
                        'type' => 'int',
                    ],
                    'ratio_percent' => [
                        'title' => 'Tỷ lệ %',
                        'key' => 'ratio_percent',
                        'type' => 'decimal',
                    ],
                    'card_display' => [
                        'title' => 'Hiển thị giá card',
                        'key' => 'card_display',
                        'type' => 'decimal',
                    ],
                ],
            ],
            '1' => [
                'title' => 'Bán nick',
                'key' => 'nick',
                'params' =>[
                    'additional_amount' => [
                        'title' => 'Số tiền cộng thêm',
                        'key' => 'additional_amount',
                        'type' => 'int',
                    ],
                    'ratio_percent' => [
                        'title' => 'Tỷ lệ %',
                        'key' => 'ratio_percent',
                        'type' => 'decimal',
                    ],
                ],
            ],
            '2' => [
                'title' => 'Mua thẻ',
                'key' => 'store_card',
                'params' => [
                    'additional_amount' => [
                        'title' => 'Số tiền cộng thêm',
                        'key' => 'additional_amount',
                        'type' => 'int',
                    ],
                    'ratio_percent' => [
                        'title' => 'Tỷ lệ %',
                        'key' => 'ratio_percent',
                        'type' => 'decimal',
                    ],
                ],
            ],
            '3' => [
                'title' => 'Dịch vụ',
                'key' => 'service',
                'params' => [
                    'additional_amount' => [
                        'title' => 'Số tiền cộng thêm',
                        'key' => 'additional_amount',
                        'type' => 'int',
                    ],
                    'ratio_percent' => [
                        'title' => 'Tỷ lệ %',
                        'key' => 'ratio_percent',
                        'type' => 'decimal',
                    ],
                ]
            ],
            '4' => [
                'title' => 'MiniGame',
                'key' => 'minigame',
                'params' => [
                    'additional_amount' => [
                        'title' => 'Số tiền cộng thêm',
                        'key' => 'additional_amount',
                        'type' => 'int',
                    ],
                    'ratio_percent' => [
                        'title' => 'Tỷ lệ %',
                        'key' => 'ratio_percent',
                        'type' => 'decimal',
                    ],
                ]
            ],
        ],
    ],
    'user-action' => [
        'action' => [
            'vote' => 'Vote',
            'comment' => 'Comment',
            'block' => 'Block'
        ],
    ],
    'language-nation' => [
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],

        'is_default' => [
            '0' => 'Không',
            '1' => 'Mặc định',
        ],
    ],

    'language-key' => [
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'menu-category' => [
        'key'=>"menu-category",
        'title'=>"Menu Trang Chủ",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],

    'menu-profile' => [
        'key'=>"menu-profile",
        'title'=>"Menu profile",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'menu-transaction' => [
        'key'=>"menu-transaction",
        'title'=>"Menu Giao Dịch",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],

    //-------------------- game --------------------//
    'game-category' => [
        'title'=>"Danh mục game",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],

    'game-group' => [
        'title'=>"Nhóm game",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'game' => [
        'title'=>"Tất cả game",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],

        'params_field'=>[
            [
                'label' => 'Thể loại game', // you know what label it is
                'name' => 'params[game_type]', // unique name for field
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => '' // default value if you want
            ],


            [
                'label' => 'Năm phát hành', // you know what label it is
                'name' => 'params[release_at]', // unique name for field
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => '' // default value if you want
            ],

            [
                'label' => 'Idol đang chơi', // you know what label it is
                'name' => 'params[idol_count]', // unique name for field
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => '' // default value if you want
            ],
            [
                'label' => 'Lượt thuê Idol với game này', // you know what label it is
                'name' => 'params[rent_count]', // unique name for field
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => '' // default value if you want
            ],
        ],

    ],


    //-------------------- article --------------------//
    'article-category' => [
        'key'=>"article-category",
        'title'=>"Danh mục bài viết",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],

    'article-group' => [
        'key'=>"article-group",
        'title'=>"Nhóm bài viết",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'article' => [
        'key'=>"article",
        'title'=>"Tất cả bài viết",
        'status' => [
            '1' => 'Hoạt động',
            '2' => 'Ngừng hiển thị',
            '0' => 'Ngừng hoạt động',
        ],
        'log_edit' => [
            '0' => 'Chỉnh sửa',
            '1' => 'Phục hồi',
        ],
        'params_field'=>[
            [
                'label' => 'Google html', // you know what label it is
                'name' => 'params[article_type]', // unique name for field
                'type' => 'ckeditor-source', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => '' // default value if you want
            ],
        ],
    ],

    //-------------------- service --------------------//
    'service-category' => [
        'key'=>"service-category",
        'title'=>"Danh mục dịch vụ",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],

    'service-group' => [
        'key'=>"service-config",
        'title'=>"Cấu hình dịch vụ cho shop",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'provider' => [
        'key'=>"provider",
        'title'=>"Tất cả nhà phát hành",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],
    'service' => [
        'key'=>"service",
        'idkey'=>[
            "nrogem"=>"DAILY - AUTO - BÁN NGỌC NRO",
            "nrocoin"=>"DAILY - AUTO - BÁN VÀNG NRO",
            "langlacoin"=>"DAILY - AUTO - BÁN VÀNG LÀNG LÁ",
            "ninjaxu"=>"DAILY - AUTO - BÁN XU NINJA",
            "roblox_gem_pet"=>"DAILY - AUTO - BÁN GEM ROBUX",
            "roblox_buyserver"=>"DAILY - AUTO - BÁN ROBUX DẠNG MUA SERVER",
            "roblox_buygamepass"=>"DAILY - AUTO - BÁN ROBUX DẠNG MUA GAMEPASS",
            "huge_psx_auto"=>"DAILY - AUTO - BÁN HUGE PSX",
            "pet_99_auto"=>"DAILY - AUTO - PET 99",
            "robux_premium_auto"=>"DAILY - AUTO - ROBUX CHÍNH HÃNG",
            "item_pet_go_auto"=>"DAILY - AUTO - BÁN ITEM PET GO",
//            "unist_auto"=>"DAILY - AUTO - BÁN UNIST TOILET TOWER DEFENSE",
//            "huge_99_auto"=>"DAILY - AUTO - HUGE 99",
            "gem_unist_auto"=>"DAILY - AUTO - BÁN GEM UNIST TOILET TOWER DEFENSE",
            "huge_psx"=>"DAILY - THỦ CÔNG - BÁN HUGE PSX",
            "robux_premium"=>"DAILY - THỦ CÔNG - ROBUX CHÍNH HÃNG",
            "gamepass_roblox"=>"DAILY - THỦ CÔNG - ROBUX GAMEPASS",
            "toilet_tower_defense"=>"DAILY - THỦ CÔNG - TOILET TOWER DEFENSE",
            "item_punch_simulator"=>"DAILY - THỦ CÔNG - BÁN ITEM PUNCH SIMULATOR",
            "arm_wrestling_simulator"=>"DAILY - THỦ CÔNG - ARW WRESTLING SIMULATOR",
            "murder_mystery"=>"DAILY - THỦ CÔNG - ITEM MURDER MYSTERY",
            "anime_adventures"=>"DAILY - THỦ CÔNG - ANIME ADVENTURES",
            "item_death_ball"=>"DAILY - THỦ CÔNG - ITEM DEATH BALL",
            "pet_99"=>"DAILY - THỦ CÔNG - PET 99",
            "grand_piece_online"=>"DAILY - THỦ CÔNG - GRAND PIECE ONLINE",
            "item_game_king_legacy"=>"DAILY - THỦ CÔNG - ITEM GAME KING LEGACY",
            "item_all_star_tower_defense"=>"DAILY - THỦ CÔNG - ITEM ALL STAR TOWER DEFENSE",
            "item_muscle_legends"=>"DAILY - THỦ CÔNG - ITEM MUSCLE LEGENDS",
            "item_pet_99"=>"DAILY - THỦ CÔNG - ITEM PET 99",
            "item_jaibreak"=>"DAILY - THỦ CÔNG - ITEM JAIBREAK",
            "adopt"=>"DAILY - THỦ CÔNG - ADOPT",
            "unit_skibidi_tower"=>"DAILY - THỦ CÔNG - UNIT SKIBIDI TOWER",
            "item_pet_catcher"=>"DAILY - THỦ CÔNG - BÁN ITEM PET CATCHER",
            "item_creatures_of_sonaria"=>"DAILY - THỦ CÔNG - BÁN ITEM CREATURES OF SONARIA",
            "farming_toilet_tower_defense"=>"DAILY - THỦ CÔNG - CÀY THUÊ TOILET TOWER DEFENSE",
            "blox_fruits"=>"DAILY - THỦ CÔNG - BÁN TRÁI ÁC QUỶ RƯƠNG",
            "item_anime_defenders"=>"DAILY - THỦ CÔNG - BÁN ITEM ANIME DEFENDERS",
            "item_meme_sea"=>"DAILY - THỦ CÔNG - BÁN ITEM MEME SEA",
            "item_anime_last_stand"=>"DAILY - THỦ CÔNG - BÁN ITEM ANIME LAST STAND",
            "item_blade_ball"=>"DAILY - THỦ CÔNG - BÁN ITEM BLADE BALL",
            "five_nights_td"=>"DAILY - THỦ CÔNG - FIVE NIGHTS TD",
            "play_anime_vanguards"=>"DAILY - THỦ CÔNG - CÀY THUÊ AV",
            "play_anime_defenders"=>"DAILY - THỦ CÔNG - CÀY THUÊ ANIME DEFENDERS",
            "item_pet_go"=>"DAILY - THỦ CÔNG - BÁN ITEM PET GO",
        ],
        'bot_units_gem' =>[
            'PC-1_MEmu-1',
            'PC-1_MEmu-2',
            'PC-1_MEmu-3',
            'PC-1_MEmu-4',
            'PC-1_MEmu-5',
            'PC-1_MEmu-6',
            'PC-1_MEmu-7',
            'PC-1_MEmu-8',
            'PC-1_MEmu-9',
            'PC-1_MEmu-10',
            'PC-1_MEmu-11',
            'PC-1_MEmu-12',
        ],
        'payment_limit'=>10000000,
        'title'=>"Tất cả dịch vụ",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
            '2' => 'Tạm ẩn',
        ],
        'secret_key' => "KeRorjUOd7JvcLy2fIKQZMOGQM9a8S",
        'type_bot' =>[
            '1' => 'TOILET TOWER DEFENSE',
            '2' => 'ANIME DEFENDERS',
        ],
        'type_item' =>[
            '1' => 'UNITS',
            '2' => 'GEM',
            '3' => 'ITEM',
        ],
        'game_type' =>[
            '1' => 'Item',
            '2' => 'Cày thuê',
            '3' => 'Gamepass',
            '4' => 'Vật phẩm',
        ],
    ],
    'service-purchase' => [
        'key'=>"service-purchase",
        'title'=>"",
        'status' => [
            '0' => 'Đã hủy',
            '1' => 'Đang chờ',
            '2' => 'Đang thực hiện',
            '3' => 'Từ chối',
            '4' => 'Hoàn tất',
            '5' => 'Thất bại',
            '9' => 'Xử lý thủ công',
            '10' => 'Hoàn tất đợi xác nhận',
            '11' => 'Yêu cầu hoàn tiền',
            '12' => 'Đã hoàn tiền',
        ],
        'mistake_error_by' => [
            '0' => 'Account Password Incorrect',
            '1' => 'Verify 3 Recent Games',
            '2' => 'Please turn off 2-Step Verification',
            '3' => 'An error occurred, please try again',
            '4' => 'This product is out of stock',
            '5' => 'Your Product Already Exists',
            '6' => 'Password Reset Error',
            '7' => 'Please Turn Off 2-step Verification And Make A Red Emal',
            '8' => 'Not Reach Required Level',
        ],
        'minute_order_cron' => [
            'complete' => 24,
        ],
        'mistake_by' => [
            '1' => 'Khách',
            '0' => 'QTV',
            '2' => 'Game',
        ],
        'monney' => [
            '18B' => 18000000000,
            '57B' => 57000000000,
            '241B' => 241000000000,
            '675B' => 675000000000,
            '1500B' => 1500000000000,
            '2620B' => 2620000000000,
            '10B' => 10000000000,
            '50B' => 50000000000,
            '100B' => 100000000000,
            '300B' => 300000000000,
            '500B' => 500000000000,
            '1000B' => 1000000000000,
            '2000B' => 2000000000000,
        ],
    ],
    'service-refund'=>[
        'key'=>"service-purchase",
        'title'=>"",
        'status' => [
            '0' => 'Đã hủy',
            '1' => 'Hoàn thành',
            '2' => 'Đang chờ xử lý',
            '3' => 'Từ chối',
        ],
    ],
    'service-purchase-auto' => [
        'key'=>"service-purchase-auto",
        'title'=>"",
        'status' => [
            '0' => 'Đã hủy',
            '1' => 'Đang chờ',
            '2' => 'Đang thực hiện',
            '3' => 'Từ chối',
            '4' => 'Hoàn tất',
            '5' => 'Thất bại',
            '6' => 'Mất item',
            '7' => 'Kết nối NCC thất bại.',
            '9' => 'Xử lý thủ công',
            '77' => 'Mất item không hoàn tiền',
            '88' => 'Mất item có hoàn tiền',
            '89' => 'Xử lý rechang',
            '999' => 'Lỗi logic xử ly',
            '899' => 'Treo đơn kiểm tra thủ công',
        ],
        'mistake_by' => [
            '1' => 'Khách',
            '0' => 'QTV',
            '2' => 'Game',
        ],
        'supplier' => [
            '1' => 'ĐẠI LÝ',
//            '30' => 'RBX API MIN 2 - MAX 3.0',
//            '31' => 'RBX API MIN 2 - MAX 3.1',
            '32' => 'RBX API MIN 2 - MAX 3.2',
            '33' => 'RBX API MIN 2 - MAX 3.3',
            '34' => 'RBX API MIN 2 - MAX 3.4',
            '35' => 'RBX API MIN 2 - MAX 3.5',
            '36' => 'RBX API MIN 2 - MAX 3.6',
            '37' => 'RBX API MIN 2 - MAX 3.7',
            '38' => 'RBX API MIN 2 - MAX 3.8',
            '39' => 'RBX API MIN 2 - MAX 3.9',
            '40' => 'RBX API MIN 2 - MAX 4.0',
            '41' => 'RBX API MIN 2 - MAX 4.1',
//            '42' => 'RBX API MIN 2 - MAX 4.2',
//            '43' => 'RBX API MIN 2 - MAX 4.3',
//            '44' => 'RBX API MIN 2 - MAX 4.4',
//            '45' => 'RBX API MIN 2 - MAX 4.5',
        ],
        'rbx_api' => [
//            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37',
            '38',
            '39',
            '40',
            '41',
//            '42',
//            '43',
//            '44',
//            '45',
        ],
        'rbx_rate' => [
//            '31' => 3.1,
            '32' => 3.2,
            '33' => 3.3,
            '34' => 3.4,
            '35' => 3.5,
            '36' => 3.6,
            '37' => 3.7,
            '38' => 3.8,
            '39' => 3.9,
            '40' => 4.0,
            '41' => 4.1,
//            '42' => 4.1,
//            '43' => 4.3,
//            '44' => 4.4,
//            '45' => 4.5,
        ],
    ],

    'service-workflow' => [
        'key'=>"service-workflow",
        'title'=>"",

    ],
    'service-workname' => [
        'key'=>"service-workname",
        'title'=>"Tên công việc dịch vụ",
    ],


    'toolgame' => [
        'key'=>"toolgame",
        'title'=>"Công cụ nạp game",
        'nro'=>[
            'status_account'=>[
                0=>'Bình thường',
                -2=>'Tài khoản sai mật khẩu',
                -3=>'Không có nhân vật',
                -4=>'Không tới được map',
                -5=>'Tài khoản bị khóa',
                -6=>'Lỗi khác, game sẽ gửi kèm message cho trường hợp này',
            ],
            'status_order'=>[
                1=>'Thành công',
                2=>'Đã nhận đồ ký gửi',
                3=>'Đã ký gửi',
                -2=>'Tài khoản sai mật khẩu',
                -3=>'Không có nhân vật',
                -4=>'Không tới được map',
                -5=>'Tài khoản bị khoá',
                -6=>'Lỗi khác, game sẽ gửi kèm message cho trường hợp này',
            ],
            'server'=>[
                1=>'Vũ trụ 1',
                2=>'Vũ trụ 2',
                3=>'Vũ trụ 3',
                4=>'Vũ trụ 4',
                5=>'Vũ trụ 6',
                6=>'Vũ trụ 6',
                7=>'Vũ trụ 7',
                8=>'Vũ trụ 8',
                9=>'Vũ trụ 9',
                10=>'Vũ trụ 10',
            ],
            'keydecrypt'=>'136G2d35ccha'
        ],
        'huge-psx' =>[
            'idkey'=>[
                'key_0'=>'Bot 01',
                'key_1'=>'Bot 02',
                'key_2'=>'Bot 03',
                'key_3'=>'Bot 04',
            ],
        ]
    ],

    'status-bot' => [
        '1' => 'Hoạt động',
        '0' => 'Ngừng hoạt động',
        '2' => 'Die cookie',
        '3' => 'Hết số dư'
    ],


    //-------------------- sticky --------------------//
    'transfer-bank' => [
        'title'=>"Ngân hàng chuyển khoản",
        'key' => 'transfer-bank',
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],
    'transfer' => [
        'title'=>"Chuyển khoản Ví - ATM tự động",
        'key' => 'transfer',
        'status' => [
            '0' => 'Thất bại',
            '1' => 'Thành công (Số tiền đúng)',
            '2' => 'Đang chờ thanh toán',
            '3' => 'Thành công (Số tiền sai)',
        ],
        'partner_id' => env('HUB_PARTNER_ID'),
        'partner_key' => env('HUB_PARTNER_KEY'),
        'channel_id_telegram' => env('HUB_CHANNEL_ID_TELEGRAM')
    ],

    //-------------------- page --------------------//

    'page' => [
        'title'=>"Trang nội dung",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],


    //-------------------- adv --------------------//
    'advertise-category' => [
        'key'=>"advertise-category",
        'title'=>"Danh mục quảng cáo",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'advertise-group' => [
        'key'=>"advertise-group",
        'title'=>"Nhóm quảng cáo",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'advertise' => [
        'key'=>"advertise",
        'title'=>"Tất cả quảng cáo",
        'position' => [
            'SLIDE' => 'Slide',
            'GAME_BANNER' => 'Dịch vụ nổi bật',
            'RECOMMEND' => 'Gợi ý',
            'MINIGAME_BANNER' => 'Minigame',
            'SERVICE_BANNER' => 'Service',
            'ACCOUNT_BANNER' => 'Shop Acc',
            'ARTICLE_BANNER' => 'Bài viết',
            'CHANGE_BANNER' => 'Nạp thẻ',
            'ADS_BANNER' => 'Quảng cáo home (Bên phải banner)',
            'BOTTOM_BANNER' => 'Quảng cáo home (Dưới banner)',
            'USER_BANNER' => 'Banner danh sách idol',
            'TWO_SIDE_BANNER' => 'Banner danh sách idol 2',
            'KEYWORDS' => 'Từ khóa nổi bật',
            'REASONABLE_PRICE' => 'Giá phù hợp',
            'STREAMER_SUGGEST' => 'Streamer/Idol khuyên dùng',
            'ABOUT_US' => 'Về chúng tôi',
        ],
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',

        ],
    ],
    'advertise-ads' => [
        'key' => [
            '0' => 'Bài viết',
            '1' => 'Nick',
            '2' => 'Nạp thẻ',
            '3' => 'Mua thẻ',
            '4' => 'Minigame',
            '5' => 'Dịch vụ',
        ],
        'title'=>"Quảng cáo ads",
        'position' => [
            '0' => 'Danh sách module',
            '1' => 'Danh sách danh mục module',
            '3' => 'Chi tiết module',
        ],
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],
    //-------------------- telecom --------------------//

    'telecom' => [
        'key'=>"telecom",
        'title'=>"Cài đặt nạp thẻ tự động",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],

        'gate_id' => [
            '1' => 'NTN (with Callback)',
            '2' => 'CCC (with Callback)',
            '3' => 'PPP (with Callback)',
        ],
    ],
    //-------------------- attribute --------------------//

    'attribute' => [
        'key'=>"attribute",
        'title'=>"Quản lý thuộc tính",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
        'datatypes' => [
            '1' => 'Selection',
            '2' => 'Multi-selection',
            '3' => 'Text',
            '4' => 'Number'
        ],
        'is_search' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
        'is_compare' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
        'is_filter' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
        'is_required' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
        'is_add_value' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
    ],
    'attribute-value' => [
        'key'=>"attribute-value",
        'title'=>"Quản lý giá trị thuộc tính",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],
    'attribute-set' => [
        'key'=>"attribute-set",
        'title'=>"Quản lý bộ thuộc tính",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],
    'variation' =>[
        'key' => 'type_variation',
        'title' => 'Quản lý biến thể',
        'type' => [
            '1' => 'Text',
            '2' => 'Color',
            '3' => 'Image'
        ]
    ],
    'category' => [
        'key'=>"category",
        'title'=>"Quản lý danh mục",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
        'is_serial' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
        'is_inventory' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
        'is_return_goods' => [
            '1' => 'Có',
            '0' => 'Không',
        ],
    ],
    'category-identification' => [
        'key'=>"category-identification",
        'title'=>"Định danh danh mục",
        'type' => [
            '1' => 'Bán nick',
            '2' => 'Dịch vụ',
            '3' => 'Nạp game',
            '4' => 'Bán thẻ',
        ],
    ],
    'uom' => [
        'key'=>"uom",
        'title'=>"Quản lý đơn vị tính",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],
    'product' => [
        'key'=>"product",
        'title'=>"Quản lý sản phẩm",
        'status' => [
            '1' => 'On',
            '0' => 'Off',
        ],
        'status_edit' => [
            '1' => 'Hiệu lực',
            '2' => 'Đang nhập liệu',
        ],
        'status_display' => [
            '1' => 'On',
            '0' => 'Off',
        ],
    ],



    //-------------------- telecom --------------------//

    'store-telecom' => [
        'key'=>"store-telecom",
        'title'=>"Cài đặt mua thẻ",
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],

        'gate_id' => [
            '1' => 'NTN (with Callback)',
            // '2' => 'HQPAY',
        ],
        'params_field'=>[
            [
                'label' => 'Mã màu', // you know what label it is
                'name' => 'params[color]', // unique name for field
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => '' // default value if you want
            ],
            [
                'label' => 'Loại thẻ', // you know what label it is
                'name' => 'params[teltecom_type]', // unique name for field
                'type' => 'select', // input fields type
                'data' => '', // data type, string, int, boolean
                'rules' => '', // validation rule of laravel
                'div_parent_class' => 'col-12 col-md-12', // div parent class for input
                'class' => '', // any class for input
                'value' => 'demo', // default value if you want
                'height' => '', // default height if you want ckfinder
                'options' => [
                    1 => "Thẻ điện thoại",
                    2 => "Thẻ game"
                ]
            ],
        ],

    ],

    'store-card' => [
        'key'=>"store-card",
        'title'=>"Thống kê mua thẻ",
        'status' => [
            '0' => 'Thất bại',
            '1' => 'Thành công',
            '2' => 'Đang chờ',
            '3' => 'Đã hủy', // trường hợp này sau sẽ dùng cho thanh toán cổng thẻ
            '4' => 'Lỗi gọi nhà cung cấp',
            '5' => 'Lỗi hệ thống'
        ],

        'gate_id' => [
            // '1' => 'HQPAY',
            '1' => 'NTN',
            '2' => 'CCC',
        ]

    ],

    //-------------------- gifcode --------------------//
    'gift-code' => [
        'key' => 'gift-code',
        'title' => 'Quản lý mã nhận thưởng',
        'status' => [
            1 => 'Hoạt động',
            0 => 'Ngừng hoạt động'
        ],
        'type' => [
            '1' => 'Nhận tiền theo cấu hình tỷ lệ trúng thưởng (bằng đào)',
            '2' => 'Voucher cho dịch vụ booking',
        ]
    ],
    'gift-code-report' => [
        'key' => 'gift-code-report',
        'title' => 'Thống kê nhận thưởng',
        'status' => [
            1 => 'Hoạt động',
            0 => 'Ngừng hoạt động'
        ],
        'type' => [
            1 => 'Nhận tiền theo cấu hình tỷ lệ trúng thưởng (bằng đào)',
        ]
    ],
    'cloudflare' => [
        'key' => 'cloudflare',
        'title' => 'Quản lý cloudflare',
        'status' => [
            1 => 'Hoạt động',
            0 => 'Ngừng hoạt động'
        ],
    ],
    'charge_bank' => [
        'key' => 'charge_bank',
        'title' => 'Nạp tiền qua bank tự động',
        'status' => [
            '0' => 'Thất bại',
            '1' => 'Thành công',
            '2' => 'Đang chờ thanh toán',
            '3' => 'Đã hủy',
        ],
    ],
    // rút tiền
    'withdraw-bank' => [
        'key' => 'withdraw-bank',
        'title' => 'Quản lý ngân hàng rút tiền',
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
        'fee_type' => [
            '1' => 'Chiết khấu % theo từng lần rút'
        ]
    ],
    'withdraw' => [
        'key' => 'withdraw',
        'title' => 'Duyệt lệnh rút tiền',
        'status' => [
            '0' => 'Từ chối',
            '1' => 'Đã duyệt',
            '2' => 'Chờ duyệt',
        ],
    ],

    //-------------------- charge --------------------//

    'charge' => [
        'key'=>"charge",
        'title'=>"Nạp thẻ tự động",
        'status' => [
            '1' => 'Thẻ đúng',
            '0' => 'Thẻ sai',
            '2' => 'Chờ xử lý',
            '3' => 'Sai mệnh giá',
            '998' => 'Lỗi gọi nhà cung cấp',
            '999' => 'Lỗi nạp thẻ',
            '-999' => 'Lỗi nạp thẻ',
            '-1' => 'Phát sinh lỗi nạp thẻ',
        ],

        'status-callback' => [
            '0' => 'Thẻ sai',
            '10000' => '10,000đ',
            '20000' => '20,000đ',
            '30000' => '30,000đ',
            '50000' => '50,000đ',
            '100000' => '100,000đ',
            '200000' => '200,000đ',
            '300000' => '300,000đ',
            '500000' => '500,000đ',
            '1000000' => '1,000,000đ',
            '2000000' => '2,000,000đ',
            '5000000' => '5,000,000đ',
        ],

        'key_encrypt' => env('ENCRYPT_CHARGING'),
        'gate_id' => [
            '1' => 'NTN (with Callback)',
            '2' => 'CCC (with Callback)',
            '3' => 'PPP (with Callback)',
        ],
        'key_sign' => env('SIGN_CCC_NTN')


    ],

    'plus_money' => [
        'is_add' => [
            '1' => 'Cộng tiền',
            '0' => 'Trừ tiền',
        ],
    ],


    'txnsvp' => [
        'trade_type' => [
            'refund' => 'Hoàn vật phẩm',
            'refund_service' => 'Hoàn vật phẩm do hủy dịch vụ',
            'withdraw_item' => 'Rút vật phẩm',
            'plus_item' => 'Cộng vật phẩm',
            'minus_item' => 'Trừ vật phẩm',
            'rubywheel' => 'Mingame vòng quay',
            'flip' => 'Mingame lật hình',
            'slotmachine' => 'Mingame quay xèng',
            'slotmachine5' => 'Mingame quay xèng 5 giải',
            'squarewheel' => 'Mingame quay vòng vòng',
            'smashwheel' => 'Mingame đập lu đồng',
            'rungcay' => 'Mingame rung cây',
            'gieoque' => 'Mingame gieo quẻ',
        ],

        'is_add' => [
            '1' => 'Cộng vật phẩm',
            '0' => 'Trừ vật phẩm'
        ],
    ],

    'txns' => [
        'trade_type' => [
            'refund' => 'Hoàn tiền',
            'charge' => 'Nạp thẻ tự động',
            'transfer_money' => 'Chuyển tiền',
            'charge_bank' => 'Chuyển khoản tự động',
            'charge_bank_hand' => 'Chuyển khoản thủ công',
            'receive_money' => 'Nhận tiền',
            'withdraw_money' => 'Rút tiền',
            'plus_money' => 'Cộng tiền',
            'minus_money' => 'Trừ tiền',
            'booking' => 'Booking',
            'donate' => 'Donate',
            'gift_code' => 'Nhận thưởng',
            'withdraw' => 'Rút tiền',
            'service_purchase' => 'Thanh toán dịch vụ',
            'service_completed' => 'Hoàn thành dịch vụ',
            'service_destroy' => 'Hủy dịch vụ',
            'transfer' => 'Nạp Ví - ATM tự động',
            'rubywheel' => 'Mingame vòng quay',
            'flip' => 'Mingame lật hình',
            'slotmachine' => 'Mingame quay xèng',
            'slotmachine5' => 'Mingame quay xèng 5 giải',
            'squarewheel' => 'Mingame quay vòng vòng',
            'smashwheel' => 'Mingame đập lu đồng',
            'rungcay' => 'Mingame rung cây',
            'gieoque' => 'Mingame gieo quẻ',
            'buy_acc' => 'Mua tài khoản',
        ],
        'trade_type_api' => [
            'charge' => 'Nạp thẻ tự động',
            'service_purchase' => 'Thanh toán dịch vụ',
            'transfer' => 'Nạp Ví - ATM tự động',
            'rubywheel' => 'Mingame vòng quay',
            'flip' => 'Mingame lật hình',
            'plus_money' => 'Cộng tiền',
            'minus_money' => 'Trừ tiền',
            'slotmachine' => 'Mingame quay xèng',
            'slotmachine5' => 'Mingame quay xèng 5 giải',
            'squarewheel' => 'Mingame quay vòng vòng',
            'smashwheel' => 'Mingame đập lu đồng',
            'rungcay' => 'Mingame rung cây',
            'gieoque' => 'Mingame gieo quẻ',
            'buy_acc' => 'Mua tài khoản',
        ],
        'status' => [
            '0' => 'Không thành công',
            '1' => 'Thành công',
            '2' => 'Chờ xử lý',

        ],
        'source_type' => [
            '1' => 'ATM',
            '2' => 'Điện tử',
            '3' => 'MOMO',
            '4' => 'Tiền PR',
            '5' => 'Tiền test',
            '6' => 'Tiền thẻ lỗi',
            '7' => 'Khác',
        ],
        'source_bank' => [
            '1' => 'Vietcombank',
            '2' => 'Viettinbank',
            '3' => 'Agribank',
            '4' => 'Techcombank',
            '5' => 'Mbbank',
            '6' => 'BIDV',
            'TCSR' => 'TCSR (Thecaosieure.com)',
            'TSR' => 'Tsr(thesieure.com)',
            'TKCR' => 'Tkcr(tkcr.vn)',
            'AZPRO' => 'AZPRO',
            'MOMO2869' => 'MOMO2869',
            'MOMO2442' => 'MOMO2442',
            'MOMO3323' => 'MOMO3323',
            'MOMO2928' => 'MOMO2928',
            'MOMO4666' => 'MOMO4666',
            'MOMO0556' => 'MOMO0556',
            'MOMO9872' => 'MOMO9872',
            'MOMO4555' => 'MOMO4555',
        ],
        'notification' => [
            'type' => [
                // nạp đào thành công
                'success_recharge' => [
                    'title' => 'Bạn đã nạp thành công @>money đào.',
                    'href' => false,
                ],
                // nạp đào thất bại
                'failed_recharge' => [
                    'title' => 'Giao dịch nạp đào thất bại.',
                    'href' => false,
                ],
                // idol được nhận donate
                'take_donate' => [
                    'title' => 'Bạn đã nhận donate @>money đào từ @>user',
                    'href' => true,
                ],
                // donate cho idol thành công
                'success_donate' => [
                    'title' =>  'Bạn đã donate thành công @>money đào cho P-Star @>user. Cảm ơn bạn đã sử dụng dịch vụ.',
                    'href' => true,
                ],
                // hoàn tiền donate
                'refund_donate' => [
                    'title' =>  'Bạn đã được hoàn @>money cho giao dịch donate P-Star @>user.',
                    'href' => true,
                ],
                // donate thất bại
                'failed_donate' => [
                    'title' => 'Giao dịch donate @>money cho P-Star @>user thất bại.',
                    'href' => true,
                ],
                // có người theo dõi mới
                'follow' => [
                    'title' => '@>user đã theo dõi bạn.',
                    'href' => false,
                ],
                // user đặt booking thành công
                'new_booking_user' => [
                    'title' => 'Bạn đã đặt lịch thuê thành công vào khung giờ @>thoigian với P-Star @>user.Vui lòng đợi P-Star xác nhận lịch.',
                    'href' => true,
                ],
                // idol nhận booking thành công
                'new_booking_idol' => [
                    'title' => 'Bạn có lịch thuê vào khung giờ @>thoigian Từ @>user.Vui lòng xác nhận lịch.',
                    'href' => true,
                ],
                // hủy do bank
                'new_booking_bank_delete' => [
                    'title' => 'Rất tiếc bạn đặt Lịch thuê vào khung giờ @>thoigian với P-Star @>user thất bại vui lòng thử lại.',
                    'href' => false,
                ],
                // user gia hạn thành công
                'giahan_booking_user' => [
                    'title' => 'Bạn đã đặt lịch gia hạn thành công vào khung giờ @>thoigian với P-Star @>user.Vui lòng đợi P-Star xác nhận lịch.',
                    'href' => true,
                ],
                // idol gia hạn thành công
                'giahan_booking_idol' => [
                    'title' => 'Bạn có lịch gia hạn vào khung giờ @>thoigian Từ @>user.Vui lòng xác nhận lịch.',
                    'href' => true,
                ],
                //hủy gia hạn do bank
                'giahan_booking_bank_delete' => [
                    'title' => 'Rất tiếc bạn đặt lịch gia hạn vào khung giờ @>thoigian với P-Star @>user thất bại Vui lòng đặt lại.',
                    'href' => false,
                ],
                // idol từ chối đơn
                'canle_booking_idol' => [
                    'title' => 'Bạn đã từ chối thành công lịch thuê vào khung giờ @>thoigian với @>user.',
                    'href' => false,
                ],
                //user từ chối đơn
                'canle_booking_user' => [
                    'title' => 'Rất tiếc lịch thuê vào khung giờ @>thoigian với P-Star @>user đã bị từ chối. Vui lòng đặt lại lịch mới.',
                    'href' => false,
                ],
                // user  user hủy đơn
                'delete_user_booking_user' => [
                    'title' => 'Bạn đã huỷ thành công lịch thuê vào khung giờ @>thoigian với P-Star @>user.',
                    'href' => false,
                ],
                // idol  user hủy đơn
                'delete_user_booking_idol' => [
                    'title' => 'Rất tiếc lịch thuê vào khung giờ @>thoigian với khách hàng @>user đã bị hủy.',
                    'href' => false,
                ],
                // user  idol hủy đơn
                'delete_idol_booking_idol' => [
                    'title' => 'Bạn đã huỷ thành công lịch thuê vào khung giờ @>thoigian với khách hàng @>user.',
                    'href' => false,
                ],
                // idol  idol hủy đơn
                'delete_idol_booking_user' => [
                    'title' => 'Rất tiếc lịch thuê vào khung giờ @>thoigian với P-Star @>user đã bị hủy,bạn vui lòng đặt lại lịch khác.',
                    'href' => false,
                ],
                // user  thành công đơn
                'user_compelete_booking_user' => [
                    'title' => 'Bạn đã hoàn thành lịch thuê vào khung giờ @>thoigian với P-Star @>user.Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // idol  thành công đơn
                'user_compelete_booking_idol' => [
                    'title' => '@>user đã kết thúc sớm lịch thuê vào khung giờ @>thoigian,Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                'user_compelete_booking_idol_bonus' => [
                    'title' => '@>user đã kết thúc sớm lịch thuê vào khung giờ @>thoigian,Bạn được cộng thêm @>bonus đ do hoàn thành đơn hàng thứ @>count,Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // user  thành công đơn
                'idol_compelete_booking_user' => [
                    'title' => 'P-Star @>user đã hoàn thành lịch thuê vào khung giờ @>thoigian.Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // idol  thành công đơn
                'idol_compelete_booking_idol' => [
                    'title' => 'Bạn đã kết thúc lịch thuê vào khung giờ @>thoigian với @>user,Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                'idol_compelete_booking_idol_bonus' => [
                    'title' => 'Bạn đã kết thúc lịch thuê vào khung giờ @>thoigian với @>user,Bạn được cộng thêm @>bonus đ do hoàn thành đơn hàng thứ @>count,Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // user  chấp nhận đơn
                'accept_booking_user' => [
                    'title' => 'Lịch thuê vào khung giờ @>thoigian với P-Star @>user đã xác nhận.Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // idol  chấp nhận đơn
                'accept_booking_idol' => [
                    'title' => 'Bạn đã xác nhận lịch thuê vào khung giờ @>thoigian với khách hàng @>user Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // user  chấp nhận giahan đơn
                'accept_booking_giahan_user' => [
                    'title' => 'Lịch gia hạn vào khung giờ @>thoigian với P-Star @>user đã xác nhận.Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // idol  chấp nhận gia hạn đơn
                'accept_booking_giahan_idol' => [
                    'title' => 'Bạn đã xác nhận lịch gia hạn vào khung giờ @>thoigian với khách hàng @>user Chúc bạn chơi game vui vẻ.',
                    'href' => false,
                ],
                // user hệ thống hủy đơn đơn
                'system_delete_booking_user' => [
                    'title' => 'Rất tiếc lịch thuê vào khung giờ @>thoigian với P-Star @>user đã hủy,vui lòng đặt lại lịch mới.',
                    'href' => false,
                ],
                // idol  hệ thống hủy đơn
                'system_delete_booking_idol' => [
                    'title' => 'Hệ thống đã hủy lịch thuê vào khung giờ @>thoigian với khách hàng @>user do quá thời gian xác nhận đơn.',
                    'href' => false,
                ],
                // user 5 phut  hệ thống báo hủy đơn
                'five_delete_booking_user' => [
                    'title' => 'Xin lỗi, hiện tại @>user đang bận chưa thể trả lời tin nhắn của bạn, bạn có muốn Hủy yêu cầu và nhận lại tiền không?',
                    'href' => false,
                ],
                // user 15 phut  hệ thống báo hủy đơn
                'five_giahan_booking_user' => [
                    'title' => 'Thời gian thuê @>user sắp hết, bạn có muốn gia hạn thêm không?',
                    'href' => false,
                ],
                'success_pstar' => [
                    'title' => 'Đăng ký trở thành P-Star đã xét duyệt thành công. Chúc bạn có những trải nghiệm tuyệt vời trên Passion Zone',
                    'href' => true,
                ],
            ]
        ],
        'is_add' => [
            '1' => 'Cộng tiền',
            '0' => 'Trừ tiền'
        ],
    ],


    //-------------------- bank --------------------//
    'bank' => [
        'key'=>"bank",
        'title'=>"Ngân hàng ",

        'bank_type' => [
            '0' => 'ATM',
            '1' => 'Ví điện tử',
            '2' => 'Ví momo',
        ],
        'fee_type' => [
            '0' => 'Theo VNĐ',
            '1' => 'Theo %',
        ],
        'status' => [
            '0' => 'Ngừng hoạt động',
            '1' => 'Hoạt động',

        ],

    ],

    'product' => [
        'key'=>"product",
        'title'=>"Quản lý sản phẩm",
        'status' => [
            '1' => 'On',
            '0' => 'Off',
        ],
        'status_edit' => [
            '1' => 'Hiệu lực',
            '2' => 'Đang nhập liệu',
        ],
        'status_display' => [
            '1' => 'On',
            '0' => 'Off',
        ],
    ],
    'supplier' => [
        'key'=>"supplier",
        'title'=>"Quản lý nhà cung cấp",
        'gate_id' => [
            '0' => 'Thủ công',
            '1' => 'Napthenhanh.com',
            '2' => 'Tichhop.pro',
            '3' => 'Daily.dichvu.me',
        ],
        'type' => [
            '1' => 'Bán thẻ',
            '2' => 'Check nick tự động',
            '3' => 'Nạp UC  Pubg Mobile  auto',
            '4' => 'Nạp FC  FiFa Online 4  auto',
            '5' => 'Nạp RP  Liên Minh  auto',
            '6' => 'Nạp QH  Liên Quân  auto',
            '7' => 'Nạp KC  Free Fire  auto',
            '8' => 'Bán Vàng  Ngọc Rồng  auto',
            '9' => 'Bán Xu  Ninja school  auto',
            '10' => 'Bán Ngọc  Ngọc Rồng  auto',
            '11' => 'Bán Robux  Roblox  auto',
            '12' => 'Nạp topup  Teamobi  auto',
            '13' => 'Nạp Robux Chính Hãng  Roblox  thủ công',
            '14' => 'Săn Đệ Tử Thuê  Ngọc Rồng  thủ công',
            '15' => 'Up Bí Kíp Yardrat  ngọc Rồng  thủ công',
            '16' => 'Up Sức Mạnh Sư Phụ  ngọc Rồng  thủ công',
            '17' => 'Up Sức Mạnh Đệ Tử  ngọc Rồng  thủ công',
            '18' => 'Làm Nhiệm Vụ  ngọc Rồng  thủ công',
            '19' => 'Làm Nhiệm Vụ  Ninja school  thủ công',
            '20' => 'Treo Thuê 24/7  Ninja school  thủ công',
            '21' => 'Cày Thuê Liên Quân  Liên Quân  thủ công',
            '22' => 'Cày Thuê Liên Minh  Liên Minh  thủ công',
            '23' => 'Bán GamePass  Roblox  thủ công',
            '24' => 'Cày Thuê Roblox  Roblox  thủ công',
            '25' => 'Bán Robux Chính Hãng  Roblox  thủ công',
        ],
        'status' => [
            '1' => 'Hoạt động',
            '0' => 'Ngừng hoạt động',
        ],
    ],

//-------------------- Api document --------------------//
    'api-document' => [
        'key'=>"api-document",
        'url' => [
            'Setting' => 'swagger/setting.yaml',
            'Article' => 'swagger/article.yaml',
            'Service' => 'swagger/service.yaml',
        ],
    ],

    //-------------------- Auto link --------------------//

    'auto-link' => [
        'key'=>"auto-link",
        'category' => [
            '0' => 'Dịch vụ',
            '1' => 'minigame',
            '2' => 'Bài viết',
            '3' => 'Nick',
        ],
    ],
    'server-image' => [
        'server' => [
            'https://cdn.upanh.info' => 'cdn',
            'https://cdn.imagetip.net' => 'imagetip',
            'https://backend.dev.tichhop.pro' => 'backend',
        ],
    ],
    'server-api' => [
        'api' => [
            'https://backend.tichhop.pro/api/v1' => 'https://backend.tichhop.pro/api/v1',
            'https://backend.tichhop.pro/api/v2' => 'https://backend.tichhop.pro/api/v2',
            'https://backend.dev.tichhop.pro/api/v1' => 'https://backend.dev.tichhop.pro/api/v1',
            'https://backend.dev.tichhop.pro/api/v2' => 'https://backend.dev.tichhop.pro/api/v2',
            'https://v2.dev.tichhop.pro/api/v1' => 'https://v2.dev.tichhop.pro/api/v1',
            'https://v2.dev.tichhop.pro/api/v2' => 'https://v2.dev.tichhop.pro/api/v2',
            'https://qltt.truongdang.online/api/v1' => 'https://qltt.truongdang.online/api/v1',
            'https://qltt.truongdang.online/api/v2' => 'https://qltt.truongdang.online/api/v2',
            'http://127.0.0.1:9000/api/v1' => 'http://127.0.0.1:9000/api/v1',
            'http://127.0.0.1:9000/api/v2' => 'http://127.0.0.1:9000/api/v2',

        ],
    ],

    'telegram'=>[
        'key'=>'telegram',
        'title'=>'Báo cáo hệ thống',
        'report'=>[
            'total_output'=> [
                'title'=>'TỔNG SẢN LƯỢNG',
                'module'=>[
                    /* default = 1 (true) , default = 0 (false)*/
                    [
                        'title'=>'Nạp tiền qua thẻ cào',
                        'key'=>'charge',
                        'indexs'=>[
                            [
                                'title'=>'Số lượng giao dịch',
                                'key'=>'total_txns',
                                'default'=>1,
                            ],[
                                'title'=>'Số người giao dịch',
                                'key'=>'total_user',
                                'default'=>1,
                            ],[
                                'title'=>'Số giao dịch thành công',
                                'key'=>'total_success',
                                'default'=>1,
                            ],[
                                'title'=>'Số giao dịch đang chờ hệ thống xử lý',
                                'key'=>'total_pending',
                                'default'=>0,
                            ],[
                                'title'=>'Số giao dịch thất bại',
                                'key'=>'total_error',
                                'default'=>0,
                            ],[
                                'title'=>'Giá trị giao dịch trung bình (chỉ tính giao dịch thành công)',
                                'key'=>'total_success_avg',
                                'default'=>0,
                            ],[
                                'title'=>'Tổng số tiền nạp theo mệnh giá',
                                'key'=>'total_amount_value',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số tiền nạp thành công thực nhận',
                                'key'=>'total_real_received_price',
                                'default'=>1,
                            ],[
                                'title'=>'Chi phí nạp tiền bằng thẻ cào',
                                'key'=>'cost_charge',
                                'default'=>1,
                            ],[
                                'title'=>'Tỷ trọng / Tổng số tiền nạp',
                                'key'=>'density_success',
                                'default'=>1,
                            ],
                        ]
                    ],
                    [
                        'title'=>'Nạp tiền qua ATM',
                        'key'=>'transfer',
                        'indexs'=>[
                            [
                                'title'=>'Số lượng giao dịch',
                                'key'=>'total_txns',
                                'default'=>1,
                            ],[
                                'title'=>'Số người giao dịch',
                                'key'=>'total_user',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số tiền nạp',
                                'key'=>'total_money',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số tiền KH thực nhận trong hệ thống',
                                'key'=>'total_real_received_price',
                                'default'=>1,
                            ],[
                                'title'=>'Tỷ lê giao dịch thành công',
                                'key'=>'ratio_success',
                                'default'=>0,
                            ],[
                                'title'=>'Giá trị giao dịch trung bình (chỉ tính giao dịch thành công)',
                                'key'=>'total_success_avg',
                                'default'=>0,
                            ],[
                                'title'=>'Phí nạp ATM',
                                'key'=>'recharge_fee_money',
                                'default'=>1,
                            ],
                        ]
                    ],
                    [
                        'title'=>'Cộng trừ tiền thủ công trong hệ thống quản trị',
                        'key'=>'plus_money',
                        'indexs'=>[
                            [
                                'title'=>'Tổng số lệnh cộng tiền',
                                'key'=>'total_cmd_add',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số tài khoản người dùng được cộng tiền',
                                'key'=>'total_user_was_add',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số tiền được cộng cho người dùng',
                                'key'=>'total_money_user_was_add',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số lệnh trừ tiền',
                                'key'=>'total_cmd_minus',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số người dùng bị trừ tiền',
                                'key'=>'total_user_was_minus',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số tiền bị trừ cho người dùng',
                                'key'=>'total_money_user_was_minus',
                                'default'=>1,
                            ],
                        ]
                    ],
                    [
                        'title'=>'Bán Account',
                        'key'=>'account',
                        'indexs'=>[
                            [
                                'title'=>'Tổng số giao dịch',
                                'key'=>'total_txsns',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số đơn hàng thành công (%)',
                                'key'=>'total_success',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số đơn hàng đang check thông tin',
                                'key'=>'total_check_info',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số đơn hàng sai mật khẩu',
                                'key'=>'total_wrong_password',
                                'default'=>1,
                            ],[
                                'title'=>'Doanh thu account',
                                'key'=>'total_turnover',
                                'default'=>1,
                            ],[
                                'title'=>'Giá vốn account',
                                'key'=>'capital_expend',
                                'default'=>1,
                            ],[
                                'title'=>'Lợi nhuận account',
                                'key'=>'total_profit',
                                'default'=>1,
                            ],
                        ]
                    ],
                    [
                        'title'=>'Bán thẻ',
                        'key'=>'store_card',
                        'indexs'=>[
                            [
                                'title'=>'Tổng số giao dịch',
                                'key'=>'total_txns',
                                'default'=>1
                            ],[
                                'title'=>'Tổng số đơn hàng thành công',
                                'key'=>'total_success',
                                'default'=>1
                            ],[
                                'title'=>'Tổng số đơn hàng đang chờ',
                                'key'=>'total_pending',
                                'default'=>0
                            ],[
                                'title'=>'Tổng số đơn hàng thất bại',
                                'key'=>'total_error',
                                'default'=>0
                            ],[
                                'title'=>'Doanh thu mua thẻ',
                                'key'=>'total_turnover',
                                'default'=>1
                            ],[
                                'title'=>'Giá vốn thẻ',
                                'key'=>'capital_expend',
                                'default'=>1
                            ],[
                                'title'=>'Lợi nhuận mua thẻ',
                                'key'=>'total_profit',
                                'default'=>1
                            ],
                        ]
                    ],
                    [
                        'title'=>'Mini game & Vật phẩm rút',
                        'key'=>'minigame_withdraw_item',
                        'indexs'=>[
                            [
                                'title'=>'Số người giao dịch trong ngày',
                                'key'=>'total_user',
                                'default'=>0,
                            ],[
                                'title'=>'Số lượng giao dịch trong ngày',
                                'key'=>'total_txns',
                                'default'=>1,
                            ],[
                                'title'=>'Doanh thu trong ngày',
                                'key'=>'total_turnover',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số lệnh rút vật phẩm thành công trong ngày',
                                'key'=>'total_cmd_withdraw_success',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số lệnh rút vật phẩm đang chờ trong ngày',
                                'key'=>'total_cmd_withdraw_pending',
                                'default'=>0,
                            ],[
                                'title'=>'Tổng số lệnh rút vật phẩm thanh toán lỗi trong ngày',
                                'key'=>'total_cmd_withdraw_error',
                                'default'=>0,
                            ],[
                                'title'=>'Tổng số lệnh rút vật phẩm giao dịch lỗi trong ngày',
                                'key'=>'total_cmd_withdraw_txns_error',
                                'default'=>0,
                            ],[
                                'title'=>'Tổng số vật phẩm rút thành công trong ngày',
                                'key'=>'total_withdraw_item_success',
                                'default'=>0,
                            ],[
                                'title'=>'Chi phí rút vật phẩm',
                                'key'=>'cost_withdraw',
                                'default'=>1,
                            ],[
                                'title'=>'Lợi nhuận tạm tính',
                                'key'=>'turnover_temp',
                                'default'=>1,
                            ],
                        ]
                    ],
                    [
                        'title'=>'Dịch vụ thủ công',
                        'key'=>'service',
                        'indexs'=>[
                            [
                                'title'=>'Số giao dịch phát sinh trong ngày',
                                'key'=>'total_txns',
                                'default'=>1
                            ],[
                                'title'=>'Số giao dịch KH thanh toán thành công',
                                'key'=>'total_success',
                                'default'=>1
                            ],[
                                'title'=>'Tổng số giao dịch đang chờ trên website',
                                'key'=>'total_pending',
                                'default'=>0
                            ],[
                                'title'=>'Số giao dịch CTV hoàn tất',
                                'key'=>'total_ctv_success',
                                'default'=>0
                            ],[
                                'title'=>'Số giao dịch CTV hủy',
                                'key'=>'total_ctv_cancel',
                                'default'=>1
                            ],[
                                'title'=>'Doanh thu thành công',
                                'key'=>'turnover_success',
                                'default'=>0
                            ],[
                                'title'=>'Doanh thu dịch vụ thủ công CTV hoàn tất',
                                'key'=>'turnover_ctv_success',
                                'default'=>1
                            ],[
                                'title'=>'Giá vốn đơn hoàn tất',
                                'key'=>'cost_price_success',
                                'default'=>0
                            ],[
                                'title'=>'Lợi nhuận đơn hoàn tất',
                                'key'=>'turnover_txns_success',
                                'default'=>1
                            ],
                        ]
                    ],
                    [
                        'title'=>'Dịch vụ tự động',
                        'key'=>'service_auto',
                        'indexs'=>[
                            [
                              'title'=>'Tổng số giao dịch trong ngày',
                                'key'=>'total_txns',
                                'default'=>1
                            ],[
                              'title'=>'Số giao dịch hoàn tất trong ngày',
                                'key'=>'total_success',
                                'default'=>1
                            ],[
                              'title'=>'Số giao dịch thất bại trong ngày',
                                'key'=>'total_error',
                                'default'=>0
                            ],[
                              'title'=>'Tổng số giao dịch mất item',
                                'key'=>'total_lost_item',
                                'default'=>0
                            ],[
                              'title'=>'Tổng số giao dịch đang chờ',
                                'key'=>'total_pending',
                                'default'=>0
                            ],[
                              'title'=>'Doanh thu đơn hàng thanh toán thành công',
                                'key'=>'turnover_payment_success',
                                'default'=>0
                            ],[
                              'title'=>'Doanh thu đơn hàng CTV hoàn tất',
                                'key'=>'turnover_ctv_success',
                                'default'=>0,
                            ],
                        ]
                    ],
                ]
            ],
            'user'=> [
                'title'=>'NGƯỜI DÙNG',
                'module'=>[
                    [
                        'title'=>'Hình thức đăng kí',
                        'key'=>'type_register',
                        'indexs'=>[
                            [
                                'title'=>'Số người dùng đăng kí mới',
                                'key'=>'total_new_user',
                                'default'=>1
                            ],[
                                'title'=>'Số người dùng đăng kí trực tiếp',
                                'key'=>'total_register_live',
                                'default'=>0
                            ],[
                                'title'=>'Số người dùng đăng kí mới qua facebook',
                                'key'=>'total_register_facebook',
                                'default'=>0
                            ],[
                                'title'=>'Số người dùng đăng kí mới qua google',
                                'key'=>'total_register_google',
                                'default'=>0
                            ],
                        ]
                    ],
                    [
                        'title'=>'Tỷ lệ giao dịch',
                        'key'=>'ratio_txns',
                        'indexs'=>[
                            [
                                'title'=>'Tổng số người dùng đăng kí mới có giao dịch trong ngày',
                                'key'=>'new_user_has_txns',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số người dùng có giao dịch trong ngày',
                                'key'=>'user_has_txns',
                                'default'=>1,
                            ],[
                                'title'=>'Tổng số dư thành viên',
                                'key'=>'total_balance_user',
                                'default'=>1,
                            ],[
                                'title'=>'Thành viên có số dư lớn nhất',
                                'key'=>'balance_user_biggest',
                                'default'=>0,
                            ],[
                                'title'=>'Giao dịch phát sinh lớn nhất trong ngày',
                                'key'=>'txns_biggest',
                                'default'=>0,
                            ],
                        ]
                    ]
                ]
            ],
        ]
    ],

];





//demo params fields
//
//'params_field'=>[
//
//    [
//        'label' => 'Tiêu đề trang', // you know what label it is
//        'name' => 'params[text]', // unique name for field
//        'type' => 'text', // input fields type
//        'data' => 'string', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => 'demo' // default value if you want
//    ],
//
//    [
//        'label' => 'Demo Checkbox', // you know what label it is
//        'name' => 'params[checkbox]', // unique name for field
//        'type' => 'text', // input fields type
//        'data' => 'string', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => '' // default value if you want
//    ],
//    [
//        'label' => 'Demo ckeditor', // you know what label it is
//        'name' => 'params[ckeditor]', // unique name for field
//        'type' => 'ckeditor', // input fields type
//        'data' => '', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => '', // default value if you want
//        'height' => '400' // default height if you want
//
//    ],
//
//    [
//        'label' => 'Demo ckeditor-source', // you know what label it is
//        'name' => 'params[ckeditor-source]', // unique name for field
//        'type' => 'ckeditor-source', // input fields type
//        'data' => '', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => '', // default value if you want
//        'height' => '400' // default height if you want ckfinder
//
//    ],
//
//    [
//        'label' => 'Demo image', // you know what label it is
//        'name' => 'params[image]', // unique name for field
//        'type' => 'image', // input fields type
//        'data' => '', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => '', // default value if you want
//        'height' => '' // default height if you want ckfinder
//
//    ],
//    [
//        'label' => 'Demo image', // you know what label it is
//        'name' => 'params[image]', // unique name for field
//        'type' => 'image', // input fields type
//        'data' => '', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => '', // default value if you want
//        'height' => '' // default height if you want ckfinder
//
//    ],
//
//    [
//        'label' => 'Demo number ', // you know what label it is
//        'name' => 'params[number]', // unique name for field
//        'type' => 'number', // input fields type
//        'data' => '', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => 'demo', // default value if you want
//        'height' => '' // default height if you want ckfinder
//
//    ],
//
//    [
//        'label' => 'Demo select', // you know what label it is
//        'name' => 'params[select]', // unique name for field
//        'type' => 'select', // input fields type
//        'data' => '', // data type, string, int, boolean
//        'rules' => '', // validation rule of laravel
//        'div_parent_class' => 'col-12 col-md-12', // div parent class for input
//        'class' => '', // any class for input
//        'value' => 'demo', // default value if you want
//        'height' => '', // default height if you want ckfinder
//        'options' => [
//            0 => "Có",
//            1 => "Không"
//        ] // default height if you want ckfinder
//
//
//    ],
//
//
//    [
//          Chia cột theo gird
//        [
//            'label' => 'Demo Checkbox', // you know what label it is
//            'name' => 'params[checkbox-group]', // unique name for field
//            'type' => 'checkbox', // input fields type
//            'data' => 'string', // data type, string, int, boolean
//            'rules' => '', // validation rule of laravel
//            'div_parent_class' => 'col-12 col-md-4', // div parent class for input
//            'class' => '', // any class for input
//            'value' => 'demo' // default value if you want
//        ],
//        [
//            'label' => 'Demo checkbox', // you know what label it is
//            'name' => 'params[checkbox-group]', // unique name for field
//            'type' => 'checkbox', // input fields type
//            'data' => 'string', // data type, string, int, boolean
//            'rules' => '', // validation rule of laravel
//            'div_parent_class' => 'col-12 col-md-4', // div parent class for input
//            'class' => '', // any class for input
//            'value' => 'demo' // default value if you want
//        ],
//        [
//            'label' => 'Demo ', // you know what label it is
//            'name' => 'params[checkbox-group]', // unique name for field
//            'type' => 'checkbox', // input fields type
//            'data' => 'string', // data type, string, int, boolean
//            'rules' => '', // validation rule of laravel
//            'div_parent_class' => 'col-12 col-md-4', // div parent class for input
//            'class' => '', // any class for input
//            'value' => 'demo' // default value if you want
//        ]
//    ]
//
//],
//
