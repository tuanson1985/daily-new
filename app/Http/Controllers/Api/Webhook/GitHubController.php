<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Library\Helpers;
use Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class GitHubController extends Controller
{
    protected string $useragent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
    public function getCallback(Request $request){
        $path = storage_path() ."/logs/github/";
        if(!\File::exists($path)){
            \File::makeDirectory($path, $mode = "0755", true, true);
        }
        $txt = Carbon::now().":".$request->fullUrl().json_encode($request->all());
        \File::append($path.Carbon::now()->format('Y-m-d').".txt",$txt."\n");
        $message = '';
        $channel_id = config('telegram.bots.mybot.channel_noty_github');
        try{
            $time = Carbon::now()->format('d-m-y H:i');
            $action = $request->action??null; // hoạt động hoàn thành 1 lần merge commit
            if($action !== "closed"){
                return "Not closed";
            }
            $user = $request->pull_request['user']['login']??null; // người thao tác merge
            $title = $request->pull_request['title']??null; // nội dung tiêu đề của thao tác merger: người dùng nhập
            $body = $request->pull_request['body']??null; // nội dung mô tả của thao tác merge
            $base = $request->pull_request['base']['label']??null; // nhánh dev hoặc master cần merge vào
            $ref = $request->pull_request['base']['ref']??null; // nhánh dev hoặc master cần merge vào
            $project = $request->pull_request['base']['repo']['name']??null; // tên project
            $html_url = $request->pull_request['html_url']??null; // url pull request
            $branche_dev = "dev";
            $branche_master = "master";
            // $branche=":".config('github.branche');
            // if(strpos($ref, $branche_dev) === false && strpos($base, $branche_master) === false){
            //     return "Not branche";
            // }
            if($ref != $branche_dev && $ref != $branche_master){
                return "Not branche";
            }
            $commits_url = $request->pull_request['commits_url']??null; // url lấy thông tin commit của user;
            $message .= '<b>';
            $message .= "\n";
            if($ref == $branche_dev){
                $message .= "Thông báo merge code vào nhánh DEV";
            }
            elseif($ref == $branche_master){
                $message .= "Thông báo merge code vào nhánh MASTER";
            }
            $message .= "\n";
            $message .= "Thời gian: ".$time;
            $message .= "\n";
            $message .= "\n";
            $message .= "Project: ".$project;
            $message .= "\n";
            $message .= "\n";
            $message .= "Nhánh: ".$ref;
            $message .= "\n";
            $message .= "\n";
            $message .= "Người thao tác: ".$user;
            $message .= "\n";
            $message .= "\n";
            $message .= "Nội dung: ".$title;
            $message .= "\n";
            $message .= "\n";
            $message .= "Mô tả: ".$body;
            $message .= "\n";
            $message .= "\n";
            $message .= "Pull URL: ".$html_url;
            $message .= "\n";
            $message .= "\n";
            $commit = $this->getCommitUrl($commits_url);
            if($commit->status == 1){
                $message .= "Commit được xử lý: ";
                $message .= "\n";
                $message .= '-------';
                $message .= "\n";
                $message .= $commit->message;
            }
            else{
                $message .=  $commit->message;
            }
            $message .= '</b>';
            Helpers::TelegramNotify($message,$channel_id);
            return "OK";
        }
        catch(\Exception $e){
            Log::error($e);
            $message = "Có lỗi phát sinh trong quá trình xử API GITHUB. Vui lòng kiểm tra lại.";
            Helpers::TelegramNotify($message,$channel_id);
            return 'ERROR';
        }
    }
    public function getCommitUrl($url){
        try{
            $headers  = [
                'accept: application/vnd.github.v3+json',
                'Authorization: token '.config('github.token'),
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $resultRaw = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $resultChange = new \stdClass();
            if($httpcode === 200){
                $result = json_decode($resultRaw);
                $message = "";
                if(isset($result) && count($result) > 0){
                    foreach($result as $key=>$item){
                        $message .= "ID: ".$item->sha??null;
                        $message .= "\n";
                        $message .= "\n";
                        $message .= 'author: '.$item->commit->author->name??null;
                        $message .= "\n";
                        $message .= "\n";
                        $message .= 'message: '.$item->commit->message??null;
                        $message .= "\n";
                        $message .= "\n";
                        $message .= '-------';
                        $message .= "\n";
                        $message .= "\n";
                    }
                }
                $resultChange->status = 1;
                $resultChange->message = $message;
                return $resultChange;
            }
            else{
                $resultChange->status = 0;
                $resultChange->message = $resultRaw;
                return $resultChange;
            }
        }
        catch(\Exception $e){
            $resultChange->status = 999;
            $resultChange->message = "Lỗi không xử lý được thông tin commit của lần thao tác này.";
            return $resultChange;
        }
    }

    public function testCommit(){
        return true;
    }
}
