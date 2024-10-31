<?php

namespace App\Library;
use App\Models\Setting;
use Google_Client;
use Google_Service_Indexing;
use Google_Service_Indexing_UrlNotification;


class GoogleIndexing
{
    /** @var Google_Client */
    private $googleClient;

    /** @var Google_Service_Indexing */
    private $indexingService;


    public function __construct()
    {
        $auth_config = Setting::get('sys_google_analytics');
        $auth_config =json_decode($auth_config,true);

        $this->googleClient = new Google_Client();

        $this->googleClient->setAuthConfig($auth_config);

        foreach (config('laravel-google-indexing.google.scopes', []) as $scope) {
            $this->googleClient->addScope($scope);
        }

        $this->indexingService = new Google_Service_Indexing($this->googleClient);

    }

//    public function setURL(Request $request,$url)
//    {
//        try {
//
//            $url = 'https://frontend.dev.tichhop.pro/tin-tuc/game-son-sung-khong-giat-lai-con-cho-phep-thi-dau-bang-pc-nhung-du-co-3080-thi-max-settting-cung-the-thoi-5';
//
//            $result = GoogleIndexingController::create()->update($url);
//
//            return response()->json([
//                'status'=>1,
//                'result'=>$result,
//            ]);
//
//        } catch (\Exception $e){
//            return response()->json([
//                'status'=>0,
//                'message'=>$e->getMessage(),
//            ]);
//        }
//    }

    public static function create(): self
    {
        return new static();
    }

    public function status(string $url)
    {
        return $this->indexingService
            ->urlNotifications
            ->getMetadata([
                'url' => urlencode($url),
            ]);
    }

    public function update(string $url)
    {
        return $this->publish($url, 'URL_UPDATED');
    }

    public function delete(string $url)
    {
        return $this->publish($url, 'URL_DELETED');
    }

    private function publish(string $url, string $action)
    {
        $urlNotification = new Google_Service_Indexing_UrlNotification();

        $urlNotification->setUrl($url);
        $urlNotification->setType($action);

        return $this->indexingService
            ->urlNotifications
            ->publish($urlNotification);
    }


}
