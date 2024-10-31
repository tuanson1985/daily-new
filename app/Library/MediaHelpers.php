<?php

namespace App\Library;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\File;
use Request;

class MediaHelpers
{
	static function media($path){
	    if(empty($path)){
	        return "";
        }
	    //nếu là link http://
		  if (strpos($path, 'http://') === 0 ||strpos($path, 'https://') === 0 ||strpos($path, '//') === 0) {
            return url($path);
		  }else{
            $path = ltrim($path, '/');
            if (\Str::endsWith(Storage::url('abc'), '/storage/abc') && \Str::startsWith($path, 'storage/')) {
                $path = ltrim($path, 'storage/');
            }
            return Storage::url($path);
		  }
	}

    static function mediaNick($path){

        if(empty($path)){
            return "";
        }

        if (strpos($path, 'http://') > -1 ||strpos($path, 'https://') > -1 || strpos($path, '//') > -1) {
            if (strpos($path, 'http://') > -1 ||strpos($path, 'https://') > -1){
                if (strpos($path, 'http://') > -1){
                    $path = str_replace('http:','',$path);
                }elseif (strpos($path, 'https://') > -1){
                    $path = str_replace('https:','',$path);
                }

                return url($path);
            }else{
                return 'https://cdn.upanh.info/'. ltrim($path, '/');
            }
        }
        else{
            if (strpos($path, 'storage') > -1){
                return 'https://cdn.upanh.info/'. ltrim($path, '/');
            }else{
                return 'https://cdn.upanh.info/storage/'. ltrim($path, '/');
            }

        }
    }


    public static function upload_file($files = false,$dir, $filename, $width = false, $height = false){

        if($files===null){
            return "";
        }
        $result= "";
        $allowedExtensions = array('mp3','mp4');
        try{
            if($files){
                $extension = strtolower($files->getClientOriginalExtension());
                if(in_array($extension,$allowedExtensions)){
                    $filename=$filename!=""?$filename:self::rand_string(10) . '_' . time();
                    $filename .= ".{$extension}";
                    $path = "{$dir}/";
                    $result = Storage::putFileAs($path, $files, $filename);
                }
            }
        }
        catch (\Exception $e) {
            \Log::error($e);
            return "";
        }
        return $result;

    }

    public static function saveBase64ImageToS3($base64Image, $fileName) {
        $url = '';
        // Tách phần base64 của ảnh ra
        try{
            list($type, $data) = explode(';', $base64Image);
            list(, $data)      = explode(',', $data);

            // Giải mã base64
            $data = base64_decode($data);

            // Lưu ảnh lên S3
            Storage::disk('s3')->put($fileName, $data);
            $url = Storage::disk('s3')->url($fileName);
        }
        catch (\Exception $e) {
            \Log::error($e);
            return "";
        }

        // Trả về URL của ảnh
        return $url;
    }


    static function delete_image($path){
        try {
            if($path==""){
                return false;
            }

            if (strpos($path, '//') > -1) {
                return false;
            }else{
                return Storage::delete(ltrim($path, '/'));
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    static function rand_string($length)
    {
        $str = '';
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {

            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

    static function allowedImage(){
        return array('jpeg', 'jpg', 'png', 'bmp', 'gif', 'ico');
    }

    public static function imageBase64($base64Code,$dir = '/storage/upload/images/service/service-', $filename="", $width = true, $height = true){
        {
            try {
                // $dir = "storage/{$dir}";
                $binary = base64_decode(explode(',', $base64Code)[1]);
                $data = getimagesizefromstring($binary);
            }
            catch (\Exception $e) {
                return false;
            }
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];

            if (!$data) {
                return false;
            }
            if (!empty($data[0]) && !empty($data[0]) && !empty($data['mime'])) {
                if (in_array($data['mime'], $allowed)) {
                    $filename=$filename!=""?$filename:self::rand_string(10) . '_' . time();
                    $filename .= ".jpg";
                    $today = Carbon::today()->format('d-m-y');
                    $dir = $dir.$today.'/'.$filename;
                    $result = Storage::put($dir, $binary);
                    if ($result){
                        return $dir;
                    }
                    return false;
                }
            }
            return false;
        }
    }

    public static function upload_image($files = false, $dir = 'images', $filename="", $width = true, $height = true,$keepOrginExtention=false){
        if($files===null){
            return "";
        }
        $result= "";
        $allowedExtensions = self::allowedImage();
        if($files){
            $extension = strtolower($files->getClientOriginalExtension());
            if(in_array($extension,$allowedExtensions)){
                $filename=$filename!=""?$filename:self::rand_string(10) . '_' . time();
                if($extension == 'gif'){
                    $result = MediaHelpers::UploadGif($files, $dir, $filename, $width, $height,$keepOrginExtention);
                }
                else{
                    $result = MediaHelpers::HandleImage($files, $dir, $filename, $width, $height,$keepOrginExtention);
                }
            }
        }
        return $result;

    }

    static function HandleImage($file = false, $dir, $filename, $width = false, $height = false,$keepOrginExtention = false){
        $result = "";
        try {
            // $dir = "storage/{$dir}";
            if($keepOrginExtention==true && !is_string($file)){
                $extension = $file->getClientOriginalExtension();
            }else{
                $extension = 'jpg';
            }
            $filename .= ".{$extension}";
            $path = "{$dir}/{$filename}";
            $temp = "temp";
            if (!is_dir($temp)) {
                mkdir($temp, 0755);
            }
            $temp = "temp/{$filename}";
            if (is_string($file)) {
                file_put_contents($temp, $file);
                $fileSize = filesize($temp)/1024;
            }else{
                $fileSize = $file->getSize()/1024;
                $file->move('temp', $filename);
            }
            if ($fileSize > 1024) { /* > 1MB*/
                $qual = 70;
            }elseif ($fileSize > 800) {
                $qual = 80;
            }elseif ($fileSize > 400) {
                $qual = 85;
            }elseif ($fileSize > 200) {
                $qual = 95;
            }else{
                $qual = 100;
            }
            if($width || $height){
                $w = $width? $width: null;
                $h = $height? $height: null;
                // khởi tạo image
                $image  = Image::make($temp);
                // khởi tạo background images
                // $background = Image::canvas($w, $h);
                // $background->fill('rgba(0,0,0,0.0)');

                /*resize theo width*/
                if($w/$h < $image->width()/$image->height()){
                    $image->resize($w, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($temp,$qual);
                    // $background->insert($image, 'center')->save($temp,$qual);
                }
                 /*resize theo height*/
                else{
                    $image->resize(null, $h, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($temp,$qual);
                    // $background->insert($image, 'center')->save($temp,$qual);
                }

            }else{
                $image = Image::make($temp)->save($temp,$qual);
            }
            if (!empty(config('filesystems.disks.ftp.url')) && strlen(config('filesystems.disks.ftp.folder'))) { //upload vào media ftp
                $dir = config('filesystems.disks.ftp.folder')."/{$dir}";
            }
            $result = Storage::putFileAs($dir, new File($temp), $filename);

            if (file_exists($temp)) {
                @unlink($temp);
            }

        } catch (\Exception $e) {
            \Log::error($e);
            return "";
        }
        return $result;
    }


    private static function UploadGif($file = false, $dir, $filename, $width = false, $height = false,$keepOrginExtention = false){
        $result = "";
        try {
            // $dir = "storage/{$dir}";
            $extension = $file->getClientOriginalExtension();
            $filename .= ".{$extension}";
            $result = Storage::putFileAs($dir, $file, $filename);

        } catch (\Exception $e) {
            \Log::error($e);
            return "";
        }
        return $result;
    }

}
