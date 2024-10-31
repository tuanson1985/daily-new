<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Item;
use App\Models\Nick;
use App\Models\Order;
use App\Models\Group;
use App\Library\CheckLogin;
use App\Library\Helpers;

class AccCheckLogin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 3600; 
    public $tries = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $item;
    public $order;
    public function __construct(Order $order) {
        if ($order->module == 'buy_acc') {
            $this->order = $order;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            $order = $this->order;
            $item = (new Nick(['table' => 'nicks_completed']))->find($order->ref_id);
            if (empty($item)) {
                $item = (new Nick(['table' => 'nicks']))->find($order->ref_id);
            }
            $username = urlencode($item->title);
            $password = urlencode(\App\Library\Helpers::Decrypt($item->slug, config('etc.encrypt_key')));
            $category = Group::find($item->parent_id);
            if ($category->is_display == 1) {
                $url = "http://nick.tichhop.pro/api/nick?action=submit_account&provider=garena&username={$username}&password=".$password."&tranid={$order->id}&callback=".urlencode(route('api.acc.callback_login'));
            }elseif ($category->is_display == 2) {
                $json = CheckLogin::teamobi(['username' => $item->title, 'password' => \App\Library\Helpers::Decrypt($item->slug, config('etc.encrypt_key'))]);
                Helpers::curl(['url' => route('api.acc.callback_login', ['tranid' => $order->id, 'status' => $json->status??2, 'message' => $json->message??'lỗi gửi check'])]);
            }elseif ($category->is_display == 3) {
                $url = "http://nick.tichhop.pro/api/nick?action=submit_account&provider=vtc&username={$username}&password=".$password."&tranid={$order->id}&callback=".urlencode(route('api.acc.callback_login'));
                // $json = CheckLogin::vtc(['username' => $item->title, 'password' => \App\Library\Helpers::Decrypt($item->slug, config('etc.encrypt_key'))]);
                // Helpers::curl(['url' => route('api.acc.callback_login', ['tranid' => $order->id, 'status' => $json->status??2, 'message' => $json->message??'lỗi gửi check'])]);
            }elseif ($category->is_display == 4) {
                $url = "http://nick.tichhop.pro/api/nick?action=submit_account&provider=nroblue&username={$username}&password=".$password."&tranid={$order->id}&callback=".urlencode(route('api.acc.callback_login'));
                // $json = CheckLogin::nroblue(['username' => $item->title, 'password' => \App\Library\Helpers::Decrypt($item->slug, config('etc.encrypt_key'))]);
                // Helpers::curl(['url' => route('api.acc.callback_login', ['tranid' => $order->id, 'status' => $json->status??2, 'message' => $json->message??'lỗi gửi check'])]);
            }elseif ($category->is_display == 5) {
                $url = "http://nick.tichhop.pro/api/nick?action=submit_account&provider=vtc&username={$username}&password=".$password."&tranid={$order->id}&callback=".urlencode(route('api.acc.callback_login'));
            }elseif ($category->is_display == 6) {
                $url = "http://nick.tichhop.pro/api/nick?action=submit_account&provider=nro&username={$username}&password=".$password."&tranid={$order->id}&callback=".urlencode(route('api.acc.callback_login'));
                // $json = CheckLogin::nro(['username' => $item->title, 'password' => \App\Library\Helpers::Decrypt($item->slug, config('etc.encrypt_key'))]);
                // Helpers::curl(['url' => route('api.acc.callback_login', ['tranid' => $order->id, 'status' => $json->status??2, 'message' => $json->message??'lỗi gửi check'])]);
            }elseif (in_array($category->is_display, [7,8,9])) {
                $providers = [7 => 'haitac', 8 => 'ninjaschool', 9 => 'knightageonline'];
                $url = "http://nick.tichhop.pro/api/nick?action=submit_account&provider={$providers[$category->is_display]}&username={$username}&password=".$password."&tranid={$order->id}&callback=".urlencode(route('api.acc.callback_login'));
            }
            if (!empty($url)) {
                $curl = curl_init();
                // if (!empty($data['params'])) {
                //     CURL_SETOPT($curl,CURLOPT_POST, True);
                //     CURL_SETOPT($curl,CURLOPT_POSTFIELDS, http_build_query($data['params']));
                // }
                CURL_SETOPT($curl,CURLOPT_URL, $url );
                CURL_SETOPT($curl,CURLOPT_RETURNTRANSFER, True);
                CURL_SETOPT($curl,CURLOPT_FOLLOWLOCATION, True);
                CURL_SETOPT($curl,CURLOPT_CONNECTTIMEOUT, 300);
                CURL_SETOPT($curl,CURLOPT_TIMEOUT, 300);
                CURL_SETOPT($curl,CURLOPT_FAILONERROR, true);
                CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                CURL_SETOPT($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                $exec = curl_exec($curl);
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $error = '';
                if (curl_errno($curl)) {
                    $error = curl_error($curl);
                }
                curl_close($curl);
                $json = json_decode($exec);
                if (!empty($json->error)) {
                    Log::error('AccCheckLogin curl error: '.$json->error.": ".($json->desc??null));
                }
                // if ($category->is_display == 2) {
                //     $json = json_decode($exec);
                //     Helpers::curl(['url' => route('api.acc.callback_login', ['tranid' => $order->id, 'status' => $json->status??2, 'message' => $json->message??'lỗi gửi check'])]);
                // }
            }
        } catch (\Exception $e) {
            Log::error('AccCheckLogin Job: '.$e->getFile().": ".$e->getLine()." – " .$e->getMessage());
        }
    }

    public function failed(Exception $exception)
    {
        echo "CurlJob lỗi";
    }
}
