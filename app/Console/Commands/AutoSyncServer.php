<?php

namespace App\Console\Commands;
use App\Models\ActivityLog;
use App\Models\Server;
use App\Models\ServerLog;
use Carbon\Carbon;
use App\Models\Shop;
use Log;

use Illuminate\Console\Command;

class AutoSyncServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoSyncServer:crom';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update thong tin server tung phut';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $shop = Shop::all();
            foreach($shop as $shop_item){
				//$fileCookie = storage_path(\App\Library\Helpers::rand_string(15) . '.txt');
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5',
					'Cache-Control: max-age=0',
					'Connection: keep-alive',
					'Upgrade-Insecure-Requests: 1',
					'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36'

				));
				$domain = "https://".$shop_item->domain."/api/ip";
				curl_setopt($ch, CURLOPT_URL, $domain);
				curl_setopt($ch, CURLOPT_COOKIEFILE, "");
				curl_setopt($ch, CURLOPT_COOKIEJAR, "");

                $ketqua = curl_exec($ch);
				$ketqua = json_decode($ketqua);
				try{
					if(isset($ketqua->ip) && $ketqua->ip != ""){
						$ipweb = $ketqua->ip;
                        self::updateServer($shop_item->id,$shop_item->domain,$ipweb);
					}
					else{
						$domain = "http://".$shop_item->domain."/api/ip";
						curl_setopt($ch, CURLOPT_URL, $domain);
						curl_setopt($ch, CURLOPT_COOKIEFILE, "");
						curl_setopt($ch, CURLOPT_COOKIEJAR, "");
						$ketqua = curl_exec($ch);
						$ketqua = json_decode($ketqua);
                        if(isset($ketqua->ip) && $ketqua->ip != ""){
                            $ipweb = $ketqua->ip;
                            self::updateServer($shop_item->id,$shop_item->domain,$ipweb);
                        }else{
                            $ipweb = "0.0.0.0";
                            $myfile = fopen(storage_path() ."/logs/log-AutoSyncServer.txt", "a") or die("Unable to open file!");
                            $txt = Carbon::now()."__Lỗi không lấy được IP shop: ".$shop_item->domain.":".$ipweb;
                            fwrite($myfile, $txt ."\n");
                            fclose($myfile);
                        }
					}
				}
				catch(\Exception $e){
					$ipweb = "0.0.0.0";
                    $myfile = fopen(storage_path() ."/logs/log-AutoSyncServer.txt", "a") or die("Unable to open file!");
                    $txt = Carbon::now()."__Lỗi không lấy được IP shop: ".$shop_item->domain.":".$ipweb;
                    fwrite($myfile, $txt ."\n");
                    fclose($myfile);
				}
				curl_close($ch);
                continue;
            }
            //Check active or inactive server
            $listServer = Server::where("type",1)->get();
            foreach($listServer as $sv_item){
                $countsub = Shop::where("server_id",$sv_item->id)->count();
                $shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$sv_item->shop_name);
                if($countsub > 0 || (isset($shop_name) && $shop_name != null && count($shop_name) > 0)){
                    //Active Server
                    if($sv_item->status == 2)
                    {
                        $sv_item->status = 1;
                        $sv_item->save();
                        ServerLog::add($sv_item,"Cập nhật trạng thái hoạt động server #".$sv_item->id);
                    }
                }
                else{
                    if($sv_item->status == 1)
                    {
                        $sv_item->status = 2;
                        $sv_item->save();
                        ServerLog::add($sv_item,"Cập nhật trạng thái dừng hoạt động server #".$sv_item->id);
                    }
                }

            }
        }catch (\Exception $e) {
            Log::error($e);
            $myfile = fopen(storage_path() ."/logs/log-AutoSyncServer.txt", "a") or die("Unable to open file!");
            $txt = Carbon::now().":chạy cronjob: AutoSyncServer that bai";
            fwrite($myfile, $txt ."\n");
            fclose($myfile);
        }
    }

    public function updateServer($id_shop,$web,$ip){
        $shop = Shop::where("domain",$web)->first();
        if($shop){
            $shop_id = $id_shop;
            $current_shop_server_id = 0;
            if( $shop->server_id != null && $shop->server_id > 0) {
                $current_shop_server_id = $shop->server_id;
            }
            //Lấy thông tin server
            $server = Server::where("ipaddress",$ip)->first();
            if($server){
                if($current_shop_server_id > 0) {
                    if ($current_shop_server_id != $server->id) {//Trường hợp 2 ID server khác nhau-Ghi log thay đổi server
                        //Cập nhật ID server vào Shop
                        $shop->server_id = $server->id;
                        $shop->save();
                        //Ghi log thay đổi
                        $cur_server = Server::where("id",$current_shop_server_id)->first();
                        if($cur_server) {
                            ServerLog::add($cur_server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$server);
                            ServerLog::add($server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$cur_server);
                        }
                    }
                }
                else{
                    $shop->server_id = $server->id;
                    $shop->save();
                    ServerLog::add($server,"Thêm mới Shop #".$shop->domain."");
                }
            }
            else{//Không có server, tạo mới server
                //Tạo mới server
                $new_server = Server::create([
                    'ipaddress' => $ip,
                    'type' => 1,
                    'status' => 1
                ]);
                ServerLog::add($new_server,"Tạo mới server id #".$new_server->id."");
                //Cập nhật Server mới cho shop
                $shop->server_id = $new_server->id;
                $shop->save();
                //Ghi log thay đổi
                if($current_shop_server_id >0) {
                    $cur_server = Server::where("id",$current_shop_server_id)->first();
                    if($cur_server) {
                        ServerLog::add($cur_server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$new_server);
                        ServerLog::add($new_server,"Dời Shop #" . $shop->domain . " từ server #" . $current_shop_server_id . " sang server #" . $server->id,$cur_server);
                    }
                }
                else{
                    ServerLog::add($new_server,"Thêm mới Shop #".$shop->domain."");
                }
            }
        }
    }
}
