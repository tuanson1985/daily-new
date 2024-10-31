<?php
return [
	'acc' => [
		/*
			target: lấy thông tin tự động
			seo_description: mess lấy thông tin tự động,
			seo_title: keyword
		*/
		'status' => [
			1 => 'Chưa bán', 0 => 'Đã bán', 2 => 'Chờ xử lý', 3 => 'Đang check thông tin', 4 => 'Sai mật khẩu', 5 => 'Đã xoá', 6 => 'Check lỗi',
			7 => 'Chờ thông tin auto', 8 => 'Đang lấy thông tin', 9 => 'Đang điền thông tin', 10 => 'Đang up', 11 => 'Cũ thông tin',
            12 => 'Đã bán chờ xác nhận', 13 => 'Y/c hoàn tiền', 14 => 'Đã hoàn tiền'
		],
		'order_status' => [0 => 'Hoàn tiền', 1 => 'Thành công', 2 => 'Đang xử lý'],
		'lienminh_auto_prop' => [
			'champions' => 'Tướng', 'icons' => 'Biểu tượng', 'wards' => 'Mẫu mắt', 'emotes' => 'Biểu cảm', 'tftcompanions' => 'Linh thú TFT',
			'tftdamageskins' => 'Dame đòn đánh TFT', 'tftmapskins' => 'Sân đấu TFT', 'skins' => 'Trang phục', 'chromas' => 'Đa sắc'
		],
		'lienminh_rank' => [
			'iron' => 'Sắt', 'bronze' => 'Đồng', 'silver' => 'Bạc', 'gold' => 'Vàng', 'platinum' => 'Bạch kim', 'diamond' => 'Kim cương',
			'master' => 'Cao thủ', 'grandmaster' => 'Đại cao thủ', 'challenger' => 'Thách đấu',
		],
		'off_servie' => false
	],
	'acc_property' => [
		/*
			is_display: Có check login,
			is_slug_override: Thông tin private chỉ hiển thị cho người sở hữu,
		*/
		'module' => ['acc_provider' => 'Nhà phát hành', 'acc_category' => 'Danh mục', 'acc_skill' => 'Thông tin', 'acc_label' => 'Tuỳ chọn'],
		'status' => [1 => 'Hiển thị', 2 => 'Tạm ẩn', 0 => 'Ẩn'],
		'type' => [1 => 'Nick thường', 2 => 'Nick random'], /* table groups.display_type */
		'position' => ['select' => 'Chọn danh sách', 'radio' => 'Stick chọn 1', 'checkbox' => 'Stick chọn nhiều', 'text' => 'Text'], /* kiểu input */
		'check_login' => [
			1 => ['name' => 'Garena', 'url' => env('URL_CHECK_LOGIN_GARENA', 'http://nick.tichhop.pro')],
			// 2 => ['name' => 'Teamobi', 'url' => null],
			3 => ['name' => 'VTCmobile', 'url' => null],
			4 => ['name' => 'nroblue.com', 'url' => null],
			5 => ['name' => 'Goplay', 'url' => env('URL_CHECK_LOGIN_GOPLAY', 'http://node.tichhop.net')],
			6 => ['name' => 'NRO', 'url' => env('URL_CHECK_LOGIN_NRO', 'http://nick.tichhop.pro')],
			7 => ['name' => 'Hải Tặc Tí Hon', 'url' => env('URL_CHECK_LOGIN_NRO', 'http://nick.tichhop.pro')],
			8 => ['name' => 'Ninja School', 'url' => env('URL_CHECK_LOGIN_NRO', 'http://nick.tichhop.pro')],
			9 => ['name' => 'Hiệp sỹ Online', 'url' => env('URL_CHECK_LOGIN_NRO', 'http://nick.tichhop.pro')],
		],
		'auto' => ['lienminh' => 'Liên minh', 'lienquan' => 'Liên quân', 'ninjaschool' => 'Ninja School', 'nro' => 'Ngọc Rồng Online'], /*position module acc_category*/
		'semi_auto' => ['lienquan'],
		'up_by_excel' => ['lienquan']
	],
	'discount_step' => [1000000, 2000000, 5000000, 10000000],
	'encrypt_key' => '2314ku',
	'encrypt_upnick' => env('UPNICK_ENSCRYPT_KEY'),
    'minute_order_cron'=>[
        'complete'=>72,
    ],
];
?>
