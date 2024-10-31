<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;


class CkFinder_Licsence
{


    public function handle(Request $request, Closure $next)
    {
        //add License
         $host= $request->getHost();
        if($host=="ctv.tichhop.pro"){
            config(['ckfinder.licenseName' => 'ctv.tichhop.pro']);
            config(['ckfinder.licenseKey' => 'HSUSAYS617KTT2HDXY4KEYSSYRQNX']);
        }
        elseif ($host=="backend-tt.tichhop.pro"){
            config(['ckfinder.licenseName' => 'backend-tt.tichhop.pro']);
            config(['ckfinder.licenseKey' => 'KB2BPF7L7SAES8NR95JY4N3CTMVBW']);
        }
        elseif ($host=="backend.tichhop.pro"){
            config(['ckfinder.licenseName' => 'backend.tichhop.pro']);
            config(['ckfinder.licenseKey' => 'A5D5DC2DHY5SUJ7YWVH1T2KHDYYEJ']);
        }
        elseif ($host=="backend.dev.tichhop.pro"){
            config(['ckfinder.licenseName' => 'backend.dev.tichhop.pro']);
            config(['ckfinder.licenseKey' => 'T3C3V4C14YJX35YMDBVXRE67A49CE']);
        } elseif ($host=="v2.dev.tichhop.pro"){
            config(['ckfinder.licenseName' => 'v2.dev.tichhop.pro']);
            config(['ckfinder.licenseKey' => 'YKNKXG3U2C2PX3C5N6BKE7EHT71NK']);
        }


        elseif($host=="localhost"){
            config(['ckfinder.licenseName' => 'localhost']);
            config(['ckfinder.licenseKey' => 'GVCVF784UPYULVKSDKXE4BPJEABNM']);
        }
        elseif($host=="127.0.0.1"){
            config(['ckfinder.licenseName' => '127.0.0.1']);
            config(['ckfinder.licenseKey' => '2TTTPDTG2BC9L37NXRQ55R63B77L5']);
        }
        return $next($request);
    }

    public function terminate( $request, $response) {
    }





}
