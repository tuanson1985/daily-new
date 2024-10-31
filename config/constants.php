<?php

//define('SETTING_SYSTEM_TITLE111', 'SETTING_SYSTEM_TITLE11');
//define('SETTING_SYSTEM_DESCRIPTION', 'SETTING_SYSTEM_DESCRIPTION');

//



return [
	'module' => [
		'smashwheel' => [
			'title' => 'Đập lu đồng',
			'key_app' => 'smashwheel',
			'key_app_nick' => 'smashwheel_nick',
			'key_app_money' => 'smashwheel_money',
			'key_cat' => 'smashwheel_category',
			'key_bonus_nick' => 'smashwheel_bonus_nick',
			'key_bonus_money' => 'smashwheel_bonus_money',
			'key_bonus_ruby' => 'smashwheel_bonus_ruby',
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động',
				'5' => 'Đã xóa'
			],
			'log-status' => [
				'1' => 'Chưa trao thưởng',
				'0' => 'Đã trao thưởng',
				'5' => 'Đã xóa'
			],
			'type' => [
				'nick' => 'Thưởng nick',
				'tien'	=> 'Thưởng tiền'
			]
		],
        'gieoque' => [
            'title' => 'Gieo quẻ',
            'key_app' => 'gieoque',
            'key_app_nick' => 'gieoque_nick',
            'key_app_money' => 'gieoque_money',
            'key_cat' => 'gieoque_category',
            'key_bonus_nick' => 'gieoque_bonus_nick',
            'key_bonus_money' => 'gieoque_bonus_money',
            'key_bonus_ruby' => 'gieoque_bonus_ruby',
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '5' => 'Đã xóa'
            ],
            'log-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng',
                '5' => 'Đã xóa'
            ],
            'type' => [
                'nick' => 'Thưởng nick',
                'tien'  => 'Thưởng tiền'
            ]
        ],
        'rungcay' => [
            'title' => 'Rung cây hái lộc',
            'key_app' => 'rungcay',
            'key_app_nick' => 'rungcay_nick',
            'key_app_money' => 'rungcay_money',
            'key_cat' => 'rungcay_category',
            'key_bonus_nick' => 'rungcay_bonus_nick',
            'key_bonus_money' => 'rungcay_bonus_money',
            'key_bonus_ruby' => 'rungcay_bonus_ruby',
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '5' => 'Đã xóa'
            ],
            'log-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng',
                '5' => 'Đã xóa'
            ],
            'type' => [
                'nick' => 'Thưởng nick',
                'tien'  => 'Thưởng tiền'
            ]
        ],
		'dicewheel' => [
			'title' => 'Xúc sắc may mắn',
			'key_app' => 'dicewheel',
			'key_app_nick' => 'dicewheel_nick',
			'key_cat' => 'dicewheel_category',
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động',
				'5' => 'Đã xóa'
			],
			'log-status' => [
				'1' => 'Chưa trao thưởng',
				'0' => 'Đã trao thưởng',
				'5' => 'Đã xóa'
			],
			'type' => [
				'nick' => 'Thưởng nick',
				'money'	=> 'Thưởng tiền'
			],
		],
		'slotmachine5' => [
			'title' => 'Quay xèng may mắn',
			'key_app' => 'slotmachine5',
			'key_app_nick' => 'slotmachine5_nick',
			'key_cat' => 'slotmachine5_category',
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động',
				'5' => 'Đã xóa'
			],
			'log-status' => [
				'1' => 'Chưa trao thưởng',
				'0' => 'Đã trao thưởng',
				'5' => 'Đã xóa'
			],
			'type' => [
				'0' => 'Thưởng vật phẩm',
				'1' => 'Thưởng nick',
				'2'	=> 'Thưởng tiền'
			]
		],
        'slotmachine' => [
            'title' => 'Quay xèng may mắn',
            'key_app' => 'slotmachine',
            'key_app_nick' => 'slotmachine_nick',
            'key_cat' => 'slotmachine_category',
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '5' => 'Đã xóa'
            ],
            'log-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng',
                '5' => 'Đã xóa'
            ],
            'type' => [
                '0' => 'Thưởng vật phẩm',
                '1' => 'Thưởng nick',
                '2' => 'Thưởng tiền'
            ]
        ],

        'squarewheel' => [
            'title' => 'Quay vòng may mắn',
            'key_app' => 'squarewheel',
            'key_app_nick' => 'squarewheel_nick',
            'key_cat' => 'squarewheel_category',
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '5' => 'Đã xóa'
            ],
            'log-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng',
                '5' => 'Đã xóa'
            ],
            'type' => [
                '0' => 'Thưởng vật phẩm',
                '1' => 'Thưởng nick',
                '2' => 'Thưởng tiền'
            ]
        ],
        'rubywheel' => [
            'title' => 'vòng quay kim cương',
            'key_app' => 'rubywheel',
            'key_cat' => 'rubywheel_category',
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '5' => 'Đã xóa'
            ],
            'type' => [
                '0' => 'Thưởng vật phẩm',
                '1' => 'Thưởng nick',
                '2' => 'Thưởng tiền'
            ]
        ],
		'rubyrandomwheel' => [
			'title' => 'vòng quay random kim cương',
			'key_app' => 'rubyrandomwheel',
			'key_cat' => 'rubyrandomwheel_category',
			'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '5' => 'Đã xóa'
            ],
            'type' => [
                '0' => 'Thưởng vật phẩm',
                '1' => 'Thưởng nick',
                '2' => 'Thưởng tiền'
            ]
		],
        'rubyflip' => [
            'title' => 'lật hình kim cương',
            'key_app' => 'rubyflip',
            'key_app_nick' => 'rubyflip_nick',
            'key_cat' => 'rubyflip_category',
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
            'log-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng',
                '5' => 'Đã xóa'
            ],
            'type' => [
                '0' => 'Lật hình vật phẩm',
                '1' => 'Lật hình trúng nick'
            ],
            'withdraw_type' => [
                '0' =>  'Lật hình vật phẩm',
                '1' =>  'Vòng quay vật phẩm',
                '2' =>  'Mua gói vật phẩm',
                '3' =>  'Quay vòng vòng',
                '4' =>  'Quay Xèng'
            ],
            'type' => [
                '0' => 'Thưởng vật phẩm',
                '1' => 'Thưởng nick',
                '2' => 'Thưởng tiền'
            ]
        ],

        'luckywheel' => [
            'key_app' => 'luckywheel',
            'key_cat' => 'luckywheel_category',
            'key_app1' => 'luckywheel1',
            'key_cat1' => 'luckywheel_category1',
            'key_app2' => 'luckywheel2',
            'key_cat2' => 'luckywheel_category2',
            'key_app3' => 'luckywheel3',
            'key_cat3' => 'luckywheel_category3',
            'key_app4' => 'luckywheel4',
            'key_cat4' => 'luckywheel_category4',
            'key_app5' => 'luckywheel5',
            'key_cat5' => 'luckywheel_category5',
            'key_app6' => 'luckywheel6',
            'key_cat6' => 'luckywheel_category6',
            'key_app7' => 'luckywheel7',
            'key_cat7' => 'luckywheel_category7',
            'key_app8' => 'luckywheel8',
            'key_cat8' => 'luckywheel_category8',
            'key_app9' => 'luckywheel9',
            'key_cat9' => 'luckywheel_category9',
            'gift-status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
            'log-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng'
            ],
            'item-status' => [
                '1' => 'Chưa trao thưởng',
                '0' => 'Đã trao thưởng'
            ],
        ],



		'menumain' => [
			'title' => 'Menu chính',
			'key_cat' => 'menumain_category',
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động'
			],
            'target' => [
                '0' => 'Không chọn',
                '1' => 'Mở tab mới',
                '2' => 'Mở Popup'
            ]
		],

		'product' => [
			'title' => 'Sản phẩm',
			'key_app' => 'product',
			'key_cat' => 'product_category',
			'key_group' => 'product_group',
			'key_filter' => 'product_filter',
			'key_order' => 'order',
			'position' => [
				'1' => 'Nhóm sản phẩm bên trái',
				'2' => 'Nhóm sản phẩm chân trang',
				'3' => 'Nhóm sản phẩm bán chạy',
				'4' => 'Nhóm sản phẩm nổi bật'
			]

		],
		'article' => [
			'title' => 'Bài viết',
			'key_app' => 'article',
			'key_cat' => 'article_category',
			'key_group' => 'article_group',
			'key_single' => 'article_single',
			'position' => [
				'1' => 'Nhóm tin nổi bật',
			],
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động'
			]
		],
		'partner' => [
			'title' => 'Bài viết',
			'key_app' => 'partner',
			'key_cat' => 'partner_category',
			'key_group' => 'partner_group',
			'key_single' => 'partner_single',
			'position' => [
				'1' => 'Nhóm tin nổi bật',
			],
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động'
			]
		],

		'game' => [
			'title' => 'Bài viết',
			'key_app' => 'game',
			'key_cat' => 'game_category',
			'key_group' => 'game_group',
			'key_provider' => 'game_provider',
			'key_attribute' => 'game_attribute',
			'key_attribute_value' => 'game_attribute_value',
			'key_single' => 'game_single',
            'key_withdrawruby' => 'game_withdrawruby',
            'key_ruby_package' => 'game_ruby_package',

			'position' => [
				'1' => 'Nhóm tin nổi bật',
			],
			'type_input' => [
				'1' => 'Ô văn bản',
				'2' => 'Ô checkbox',
				'3' => 'Mutiselect',
				'4' => 'Select',
				'5' => 'Radio',
				'6' => 'File hình ảnh',
				'7' => 'Khung văn bản',

			],
			'type_require' => [
				'0' => 'Không',
				'1' => 'Có',


			],

            'require_checkpass' => [
                '1' => 'Có',
                '0' => 'Không'
            ],

            'is_random' => [
                '0' => 'Không',
                '1' => 'Có'

            ],

            'status-bot' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động',
                '2' => 'Die cookie',
                '3' => 'Hết số dư'
            ],

			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động'
			],
			'item-status' => [
				'1' => 'Chưa bán',
				'0' => 'Đã bán',
                '2' => 'Chờ check thông tin tài khoản',
				'3' => 'Đã đặt cọc (Trả góp)',
				'4' => 'Sai mk',
				'5' => 'Đã xóa',
                '6' => 'Đã hoàn lại',
                '7' =>  'Đang check thông tin'

			],

            'is_ruby' => [
                '1' => 'Vật phẩm cộng dồn 1',
                '2' => 'Vật phẩm cộng dồn 2',
                '3' => 'Vật phẩm cộng dồn 3',
                '4' => 'Vật phẩm cộng dồn 4',
                '5' => 'Vật phẩm cộng dồn 5',
                '6' => 'Vật phẩm cộng dồn 6',
                '7' => 'Vật phẩm cộng dồn 7',
                '8' => 'Vật phẩm cộng dồn 8',
                '9' => 'Vật phẩm cộng dồn 9',
                '10' => 'Vật phẩm cộng dồn 10',
            ],

            'ruby-status' => [
                '0'=>'Chờ xử lý',
                '1'=>'Hoàn thành',
                '2'=>'Thanh toán lỗi',
                '3'=>'Giao dịch lỗi (xem tiến độ)'
            ],
            'game_type' => [
                'lienquan' => 'Liên quân mobile (Quân huy)',
                'freefire' => 'Freefire (kim cương)',
                'lienminh' => 'Liên minh',
                'bns'      => 'Blade an Soul',
                'ads'      => 'Âm dương sư',
                'fo4'      => 'Fifa online 4',
                'fo4m'     => 'Fifa online 4 mobile',
                'pubgm'    => 'Pubg mobile',
                'aumobile' => 'Audition mobile'
            ],

		],
        'service' => [
            'title' => 'Dịch vụ',
            'key_app' => 'service',
            'key_cat' => 'service_category',
            'key_group' => 'service_group',
            'key_purchase' => 'service_purchase',
            'key_workflow' => 'service_workflow',
            'key_workname' => 'service_workname',
            'key_provider' => 'service_provider',
            'key_attribute' => 'service_attribute',
            'key_attribute_value' => 'service_attribute_value',
            'key_single' => 'service_single',

            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
            'purchase_status' => [
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
            ],
            'mistake_by' => [
                '1' => 'Khách',
                '0' => 'QTV',
                '2' => 'Game',
            ],


        ],

        'accessories' => [
            'title' => 'Bài viết',
            'key_app' => 'accessories',
            'key_cat' => 'accessories_category',
            'key_group' => 'accessories_group',
            'key_provider' => 'accessories_provider',
            'key_attribute' => 'accessories_attribute',
            'key_attribute_value' => 'pkaccessories_attribute_value',
            'key_single' => 'accessories_single',
            'key_order'=>'accessories_order',
            'key_workflow' => 'accessories_order',
            'position' => [
                '1' => 'Nhóm tin nổi bật',
            ],
            'type_input' => [
                '1' => 'Ô văn bản',
                '2' => 'Ô checkbox',
                '3' => 'Mutiselect',
                '4' => 'Select',
                '5' => 'Radio',
                '6' => 'File hình ảnh',
                '7' => 'Khung văn bản',

            ],
            'type_require' => [
                '0' => 'Không',
                '1' => 'Có',


            ],

            'require_checkpass' => [
                '1' => 'Có',
                '0' => 'Không'
            ],

            'is_random' => [
                '0' => 'Không',
                '1' => 'Có'

            ],
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],

            'order-status' => [
                '0' => 'Đã hủy',
                '1' => 'Chờ xử lý',
                '2' => 'Đang giao hàng',
                '3' => 'Từ chối',
                '4' => 'Hoàn tất',
                '5' => 'Thất bại'

            ],
            'item-status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
        ],

        'bank' => [
            'title' => 'Bank',
            'key_app' => 'bank',
            'key_cat' => 'bank_category',
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

        'withdraw' => [
            'status' => [
                '0' => 'Đã hủy',
                '1' => 'Thành công',
                '2' => 'Chờ xử lý',

            ],
        ],

		'user-qtv' => [
			'status' => [
				'1' => 'Hoạt động',
				'2' => 'Chờ kích hoạt',
				'0' => 'Khóa'
			],
			'role_id' => [
				'1' => 'Admin',
				'2' => 'Quản trị viên',
				'3' => 'Thành viên'
			]
		],
		'user' => [
			'status' => [
				'1' => 'Hoạt động',
				'2' => 'Chờ kích hoạt',
				'0' => 'Khóa'
			],
			'role_id' => [
				'1' => 'Admin',
				'2' => 'Quản trị viên',
				'3' => 'Thành viên'
			]
		],

        'telecom' => [

            'slow-status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
            'auto-status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
            'gate_id' => [
                //'1' => 'Tichhop.vn',
               // '2' => 'Tichhop247.com',
               // '3' => 'Doicard.vn',
                //'4' => 'Scoin.vn',
                //'5' => 'Napthenhanh.com (updon.napthenhanh.com)',
                //'6' => 'Napthenhanh.com1 (napthenhanh.com)',
                '7' => 'NTN (with Callback)',
				'8' => 'CCC (with Callback)',
                //'99' => 'Cổng khác'
            ]
        ],
        'charge' => [
            'status' => [
                '1' => 'Chờ xử lý',
                '2' => 'Thẻ sai',
                '3' => 'Thẻ đúng',
                '4' => 'Thẻ trễ',
                '5' => 'Thẻ sai mệnh giá',
                '10000' => '10,000đ',
                '20000' => '20,000đ',
                '30000' => '30,000đ',
                '50000' => '50,000đ',
                '100000' => '100,000đ',
                '200000' => '200,000đ',
                '300000' => '300,000đ',
                '500000' => '500,000đ',
                '1000000' => '1,000,000đ',
                '5000000' => '5,000,000đ',
            ],

            'status-auto' => [
                '1' => 'Thẻ đúng',
                '0' => 'Thẻ sai',
                '2' => 'Chờ xử lý',
                '3' => 'Sai mệnh giá',
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
                '5000000' => '5,000,000đ',
            ],

            'type_charge' => [
                '0' => 'Nạp thẻ',
                '1' => 'Nạp thẻ chậm',

            ],
            'api_type' => [
                '0' => 'Nạp Auto',
                '1' => 'Nạp auto và Callback',

            ],

        ],

		'txns' => [
			'trade_type' => [
				'1' => 'Nạp thẻ tự động',
				'2' => 'Nạp thẻ chậm',
				'3' => 'Chuyển tiền',
				'4' => 'Nhận tiền',
				'5' => 'Rút tiền',
				'6' => 'Cộng tiền',
				'7' => 'Trừ tiền',
				'8' => 'Tiền thưởng',
				'9' => 'Thanh toán bán nick',
				'10' => 'Đặt cọc (Trả góp)',
				'11' => 'Hoàn tiền',
				'12' => 'Thanh toán dịch vụ',
				'13' => 'Hoàn tất dịch vụ',
				'14' => 'Thanh toán mua thẻ',
				'15' => 'Thanh toán mua phụ kiện',
				'101' => 'Tăng số dư',
				'102' => 'Giảm số dư',
				'103' => 'Chuyển nhận tiền',
				'104' => 'Cộng trừ tiền',
				'105' => 'Mua tài khoản game',
                '106' => 'Mua kim cương',
                '107' => 'Vòng quay may mắn',
                '108' => 'Vòng quay vật phẩm',
                '109' => 'Lật hình vật phẩm',
                '110' => 'Lật hình trúng nick',
                '111' => 'Quay hình trúng vp',
                '112' => 'Quay hình trúng nick',
                '113' => 'Quay hình trúng tiền',
                '114' => 'Vòng quay trúng tiền',
                '115' => 'Lật hình trúng tiền',
                '116' => 'Quay xèng trúng vp',
                '117' => 'Quay xèng trúng nick',
                '118' => 'Quay xèng trúng tiền',
                '119' => 'Vòng quay trúng nick',
                '120' => 'Rung cây trúng vp',
                '121' => 'Rung cây trúng nick',
                '122' => 'Rung cây trúng tiền',
                '123' => 'Gieo quẻ trúng vp',
                '124' => 'Gieo quẻ trúng nick',
                '125' => 'Gieo quẻ trúng tiền',
                '126' => 'Đập lu trúng vp',
                '127' => 'Đập lu trúng nick',
                '128' => 'Đập lu trúng tiền',
			],
            'status' => [
                '0' => 'Không thành công',
                '1' => 'Thành công',
                '2' => 'Chờ xử lý',

            ],
		],


        'hire_purchase' => [
            'status' => [
                '1' => 'Đang trả góp',
                '2' => 'Hoàn tất',
                '3' => 'Hủy bỏ',
            ],
        ],

        'notify' => [
            'type' => [
                '1' => 'Nạp thẻ chậm',

            ],
        ],


		'video' => [
			'title' => 'Videos',
			'key_app' => 'video',
			'key_cat' => 'video_category',
			'key_group' => 'video_group',
			'position' => [
				'1' => 'Nhóm video nổi bật',
			]

		],
		'support' => [
			'title' => 'Hỗ trợ',
			'key_app' => 'support',
			'key_cat' => 'support_category',
			'key_group' => 'support_group',
			'position' => [
				'1' => 'Nhóm hỗ trợ trang chủ',
			],
		],
		'advertisement' => [
			'title' => 'Quảng cáo',
			'key_app' => 'advertisement',
			'key_cat' => 'advertisement_category',
			'key_group' => 'advertisement_group',
			'position' => [
				'1' => 'Slide',
				'2' => 'Dịch vụ nổi bật',
			],
			'status' => [
				'1' => 'Hoạt động',
				'0' => 'Ngừng hoạt động'
			]
		],

		'mainmenu' => [
			'title' => 'Menu chính',
			'key_app' => 'mainmenu',
			'key_cat' => 'mainmenu_category',
			'key_group' => 'mainmenu_group',
            'target' => [
                '0' => 'Không chọn',
                '1' => 'Mở tab mới',
                '2' => 'Mở Popup'
            ]
		],
		'contact' => [
			'title' => 'Liên hệ',
			'key_app' => 'contact',
			'key_cat' => 'contact_category',
			'key_group' => 'contact_group',
			'key_filter' => 'contact_filter',
			'key_contentpage' => 'contact_contentpage',
		],
		'agency' => [
			'title' => 'Đại lý',
			'key_app' => 'agency',
			'key_cat' => 'agency_category',
			'key_group' => 'agency_group',
			'position' => [
				'1' => 'Nhóm đại lý trang chủ',
			]

		],

        'store-telecom' => [

            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
        ],

        'store-card' => [
            'key_app' => 'contact',
            'key_app' => 'store_card',
            'key_purchase' => 'store_card_purchase',
            'status' => [
                '0' => 'Chưa bán',
                '1' => 'Đã bán',

            ],
        ],



		'about' => [
			'title' => 'Tải xuống',
			'key_app' => 'about',
			'key_cat' => 'about_category',
			'key_group' => 'about_group',
			'position' => [
				'1' => 'Slide',
				'2' => 'Quảng cáo cạnh slide',
				'3' => 'Quảng cáo cột trái',
				'4' => 'Popup vào trang chủ',
			],
			'display_type' => [
				'1' => 'Hiện thị dạng bài viết',
				'2' => 'Lấy dữ liệu con dưới dạng List',
				'3' => 'Lấy dữ liệu con dưới dạng Grid',

			],

		],

        'client' => [
            'status' => [
                '1' => 'Hoạt động',
                '0' => 'Ngừng hoạt động'
            ],
        ],

    ],
	//status
	'status' => [
		'1' => 'Active',
		'2' => 'Pending',
		'0' => 'Inactive'

	],
	//limit
	'limit_default' => '50',
	'limit' => [
		'50' => '50',
		'100' => '100',
		'200' => '200',
		'1000' => '1000'


	],
	//product filter type
	'target' => [
		'1' => 'Mở trang tại cửa sổ mới',
		'2' => 'Mở trang tại cửa sổ hiện tại',
	],
	'target_menu' => [
		'2' => 'Mở trang tại cửa sổ hiện tại',
		'1' => 'Mở trang tại cửa sổ mới',
	],

	//product filter type
	'product_filter_type' => [
		'1' => 'Chọn nhiều thuộc tính/sản phẩm',
		'2' => 'Chọn 1 thuộc tính/sản phẩm',
	]


];

