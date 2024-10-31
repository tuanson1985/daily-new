<?php

namespace App\Http\Controllers\Admin\Telegram;

use App\Http\Controllers\Controller;
use App\Library\ReportShop;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

class ConfigController extends Controller
{
    public function updateConfig(Request $request)
    {
        $shop = Shop::query()->where('id',$request->get('shop_id'))->first();
        if ($shop){
            try {
               $old_data = json_decode($shop->telegram_config,true);
               if (!$old_data){
                   session()->flash('notify', 'Shop chưa có group');
                   return response()->json([
                       'status'=>0,
                       'message'=>'Shop chưa có group',
                   ]);
               }

               foreach ($old_data as $key => $group){
                   if ($group['order'] == $request->get('order')) {
                       $old_data[$key]['config'] = json_encode($request->get('config'));
                   }
               }

               $shop->telegram_config = json_encode($old_data);
               $shop->save();

                session()->flash('notify', 'Cấu hình thông báo thành công !');

                return  response()->json([
                    'status'=>1,
                    'message'=>'Cấu hình thông báo thành công !',
                ]);

            } catch (\Exception $e) {
                session()->flash('notify', 'Có lỗi xảy ra');

                return response()->json([
                    'status'=>0,
                    'message'=>$e->getMessage()
                ]);
            }
        }else {
            session()->flash('notify', 'Không tìm thấy Shop');

            return response()->json([
                'status'=>0,
                'message'=>'Không tìm được điểm bán.'
            ],404);
        }
    }

    public function store(Request $request)
    {
        $data_group = $request->get('data');
        if (!$data_group['group_id'] || !$data_group['group_name']) {
            return response()->json([
                'status'=>2,
                'message'=>'Cần điền đầy đủ các trường !',
            ]);
        }
        $shop = Shop::query()->where('id',$request->shop_id)->first();
        if ($shop){

            $config_default = [];
            $default_quantity_config = [
                'report'=>"total-quantity-config",
                'modules'=>[],
            ];
            $default_user_config = [
                'report'=>"user-config",
                'modules'=>[],
            ];

            foreach (config('module.telegram.report.total_output.module') as $key => $module ) {
                $data_module = [];
                $data_module['module'] = $module['key'];
                $data_module['index'] = [];

                foreach ($module['indexs'] as $index) {
                    $data_index_default = [$index['key']=>$index['default']];
                    array_push($data_module['index'],$data_index_default);
                }

                array_push($default_quantity_config['modules'],$data_module);
            }
            foreach (config('module.telegram.report.user.module') as $key => $module ) {
                $data_module = [];
                $data_module['module'] = $module['key'];
                $data_module['index'] = [];

                foreach ($module['indexs'] as $index) {
                    $data_index_default = [$index['key']=>$index['default']];
                    array_push($data_module['index'],$data_index_default);
                }

                array_push($default_user_config['modules'],$data_module);
            }

            array_push($config_default,$default_quantity_config);
            array_push($config_default,$default_user_config);

            $data_group['config'] = json_encode($config_default);

            try {
                $old_data = json_decode($shop->telegram_config,true);
                if (!$old_data) {
                    $old_data = [];
                }
                array_push($old_data,$data_group);
                $new_data = json_encode($old_data);
                $shop->telegram_config = $new_data;
                $shop->save();

                session()->flash('notify', 'Thêm group thành công');

                return  response()->json([
                    'status'=>1,
                    'message'=>'Thêm group thành công',
                ]);
            } catch (\Exception $e) {
                session()->flash('notify', 'Có lỗi xảy ra');

                return response()->json([
                    'status'=>0,
                    'message'=>$e->getMessage()
                ]);
            }
        }else {
            session()->flash('notify', 'Không tìm thấy Shop');

            return response()->json([
                'status'=>0,
                'message'=>'Không tìm được điểm bán.'
            ],404);
        }
    }

    public function update(Request $request)
    {
        $shop = Shop::query()->where('id',$request->get('shop_id'))->first();
        if ($shop){
            try {
                $old_data = json_decode($shop->telegram_config,true);
                if (!$old_data) {
                    session()->flash('notify', 'Shop chưa có group');
                    return response()->json([
                        'status'=>0,
                        'message'=>'Shop chưa có group',
                    ]);
                }
                if (!$request->filled('group_id') || !$request->filled('group_name')) {
                    return response()->json([
                        'status'=>2,
                        'message'=>'Cần điền đầy đủ các trường !',
                    ]);
                }
                foreach ($old_data as $key => $group) {
                    if($group['order'] == $request->get('order')) {
                       $old_data[$key]['group_id'] = $request->get('group_id');
                       $old_data[$key]['group_name'] = $request->get('group_name');
                       $old_data[$key]['status'] = $request->get('group_status');
                    }
                }
                $shop->telegram_config = json_encode($old_data);
                $shop->save();


                session()->flash('notify', 'Đã cập nhật thay đổi');
                return response()->json([
                    'status'=>1,
                    'message'=>'Đã cập nhật thay đổi',
                ]);
            } catch (\Exception $e) {
                session()->flash('notify', 'Có lỗi xảy ra');
                return response()->json([
                    'status'=>0,
                    'message'=>'Có lỗi xảy ra',
                ]);
            }
        }else {
            session()->flash('notify', 'Không tìm thấy shop');
            return response()->json([
                'status'=>0,
                'message'=>'Không tìm thấy shop',
            ]);
        }
    }


    public function destroy(Request $request)
    {
        $shop = Shop::query()->where('id',$request->get('shop_id'))->first();
        if ($shop){
            try {
                $old_data = json_decode($shop->telegram_config,true);
                if (!$old_data) {
                    session()->flash('notify', 'Shop chưa có group');

                    return response()->json([
                        'status'=>0,
                        'message'=>'Điểm bán không có Group nào',
                    ]);
                }
                foreach ($old_data as $key => $group){
                    if ($group['order'] == $request->get('order')) {
                        unset($old_data[$key]);
                    }
                }
                $shop->telegram_config = json_encode($old_data);
                $shop->save();
                session()->flash('notify', 'Xoá group thành công');
                return response([
                    'status'=>1,
                    'message'=>'Xoá group thành công',
                ]);

            } catch (\Exception $e) {
                session()->flash('notify', 'Có lỗi xảy ra');

                return response()->json([
                    'status'=>0,
                    'message'=>'Có lỗi xảy ra',
                ]);
            }
        }else {
            session()->flash('notify', 'Không tìm thấy shop');

            return response()->json([
                'status'=>0,
                'message'=>'Không tìm thấy shop',
            ]);
        }
    }

    public function sendMessageDemo($shop_id,$order_group)
    {
        try {
            $time = Carbon::today();
            new ReportShop($time, $shop_id,$order_group);

            return response()->json([
                'status'=>1,
                'message'=> 'Đã tạo yêu cầu gửi tin nhắn'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'=>0,
                'message'=> 'Có lỗi xảy ra trong quá trình gửi tin nhắn.'
            ]);
        }
    }
}
