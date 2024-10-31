<?php

namespace App\Http\Controllers\Api\V1\Theme;

use App\Http\Controllers\Controller;
use App\Models\ThemeClient;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Validator;
use Carbon\Carbon;
use Cache;
use DB;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Order;

class ThemeController extends Controller
{
    public function getThemeConfig(Request $request){
        $client = $request->get('domain')??$request->get('client');
        $secret_key = $request->get('secret_key');
        if($client == null || $client == "" || $secret_key ==  null || $secret_key == ""){
            return response()->json([
                'message' => __('Client chưa được cung cấp'),
                'status' => 0
            ], 200);
        }

        $shop = Shop::where('secret_key',$secret_key)->where('domain',$client)->where('status',1)->first();

        if (!isset($shop)){
            return response()->json([
                'message' => __('Client chưa được cung cấp'),
                'status' => 0
            ], 200);
        }

        $theme_config = ThemeClient::join('theme','theme.id','=','theme_id')->where('client_id',$shop->id)->where('theme_client.status',1)->where('client_name',$client )->first();
        $data = new \stdClass();
        if($theme_config) {

            $data->client_name = $theme_config->client_name;
            $data->client_id = $theme_config->client_id;
            $data->theme_name = $theme_config->title;
            $data->theme_key = $theme_config->key;
            $data->theme_id = $theme_config->theme_id;
            $data->theme_config = json_decode($theme_config->param_attribute);
        }
        return response()->json([
            'message' => __('Success'),
            'data' => $data,
            'status' => 1
        ], 200);
    }
}
