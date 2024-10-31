<?php

namespace App\Library;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Request;

class Files
{

	static function media($path){


	    if($path==""){
	        return "";
        }

	    //nếu là link http://
		if (strpos($path, 'http://') > -1 ||strpos($path, 'https://') > -1 ||strpos($path, '//') > -1) {

            return $path;
		}else{


		    //nếu cấu hình url ftp
            if (!empty(config('filesystems.disks.ftp.url'))){

                return config('filesystems.disks.ftp.url').$path;
            }
            else{
                return config('filesystems.disks.public.folder').$path;
                return url(config('filesystems.disks.public.folder').$path);
            }

		}
	}




    static function upload_image_extension($files = false, $dir = 'images', $filename="", $width = true, $height = true,$keepOrginExtention=false){

        $allFilename = "";
        $allowedExtensions = array('jpeg', 'jpg', 'png', 'bmp', 'gif', 'ico');
        if(is_array($files)){

            foreach ($files as $index => $aFile) {


                if($aFile){

                    $extension = strtolower($aFile->getClientOriginalExtension());

                    if(in_array($extension, $allowedExtensions)){

                        if($filename!=""){
                            $tempFileName=$filename.self::rand_string(10) . '_' . time();
                        }
                        else{
                            $tempFileName=self::rand_string(10) . '_' . time();
                        }
                        $allFilename.= Files::uploadEachFile_extension($aFile, $dir, $tempFileName, $width, $height,$keepOrginExtention);

                        if ($index < count($files) - 1) {
                            $allFilename .= "|";
                        }

                    }
                }
            }

        }
        else{
            if($files){

                $extension = strtolower($files->getClientOriginalExtension());
                if(in_array($extension, $allowedExtensions)){
                    $filename=$filename!=""?$filename:self::rand_string(10) . '_' . time();
                    $allFilename= Files::uploadEachFile_extension($files, $dir, $filename, $width, $height,$keepOrginExtention);
                }
            }
        }
        // dd($allFilename);
        return $allFilename;
    }

    static function upload_image($files = false, $dir = 'images', $filename="", $width = true, $height = true,$keepOrginExtention=false){

        if($files===null){
            return "";
        }

        $allFilename = "";
        $allowedExtensions = array('jpeg', 'jpg', 'png', 'bmp', 'gif', 'ico');
        if(is_array($files)){

            foreach ($files as $index => $aFile) {


                if($aFile){

                    $extension = strtolower($aFile->getClientOriginalExtension());

                    if(in_array($extension, $allowedExtensions)){

                        if($filename!=""){
                            $tempFileName=$filename.'-'.self::rand_string(10) . '_' . time();
                        }
                        else{
                            $tempFileName=self::rand_string(10) . '_' . time();
                        }

                        $allFilename.= Files::uploadEachFile($aFile, $dir, $tempFileName, $width, $height,$keepOrginExtention);

                        if ($index < count($files) - 1) {
                            $allFilename .= "|";
                        }

                    }
                }
            }

        }
        else{
            if($files){

                $extension = strtolower($files->getClientOriginalExtension());
                if(in_array($extension, $allowedExtensions)){
                    $filename=$filename!=""?$filename:self::rand_string(10) . '_' . time();
                    $allFilename= Files::uploadEachFile($files, $dir, $filename, $width, $height,$keepOrginExtention);
                }
            }
        }

        return $allFilename;
    }

    private static function uploadEachFile($file = false, $dir, $filename, $width = false, $height = false,$keepOrginExtention = false){
        $result = "";

        try {
            if($keepOrginExtention==true){
                $extension = $file->getClientOriginalExtension();
            }
            else{
                $extension = 'jpg';
            }

            $filename .= ".{$extension}";

            $path = "{$dir}/{$filename}";

            $temp = "temp";
            //tạo temp mẫu hình ảnh
            if (!is_dir($temp)) {

                mkdir($temp, 0777);
            }
            $temp = "temp/{$filename}";

            //end tạo temp mẫu hình ảnh
            if ($width || $height) {
                $img = Image::make($file->getRealPath());
                    // if($width < $img->width() && $height < $img->height()){
                    //     $img->crop($width, $height, null, null)->save($temp,100);
                    // }else{
                        $img->resize($width, $height)->save($temp,100);
                    // }
            }
            else{

                $img = Image::make($file->getRealPath())->save($temp,100);
            }


            //upload vào media ftp
            if (!empty(config('filesystems.disks.ftp.url'))) {

                $storage = Storage::disk('ftp');
                if (strlen(config('filesystems.disks.ftp.folder'))) {
                    $dir = config('filesystems.disks.ftp.folder')."/{$dir}";
                }
                if ($storage->putFileAs($dir, new File($temp), $filename)) {
                    $result = "/{$path}";
                }
            }

            //upload vào local
            else{

                $storage = Storage::disk('public');
                if ($storage->putFileAs($dir, new File($temp), $filename)) {
                    $result = "/{$path}";
                }
            }

            if (file_exists($temp)) {
                unlink($temp);
            }



        } catch (\Exception $e) {
            \Log::error($e);
            return "";
        }

        return $result;
    }


    private static function uploadEachFile_extension($file = false, $dir, $filename, $width = false, $height = false,$keepOrginExtention = false){
        $result = "";

        try {
            if($keepOrginExtention==true){
                $extension = $file->getClientOriginalExtension();
            }
            else{
                $extension = 'png';
            }

            $filename .= ".{$extension}";

            $path = "{$dir}/{$filename}";

            $temp = "temp";
            //tạo temp mẫu hình ảnh
            if (!is_dir($temp)) {

                mkdir($temp, 0777);
            }
            $temp = "temp/{$filename}";

            //end tạo temp mẫu hình ảnh
            if ($width || $height) {
                $w = $width? $width: null;
                $h = $height? $height: null;
                $image  = Image::make($file->getRealPath());
                // if($w < $image->width() && $h < $image->height()){
                //     $image->crop($w, $h, null, null)->save($temp,100);
                // }else{
                    if($w/$h < $image->width()/$image->height()){ /*resize theo width*/
                        $image->resize($w, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $background = Image::canvas($width, $h);
                        $background->fill('rgba(0,0,0,0.0)');
                        $background->insert($image, 'center')->save($temp,100);
                    }else{
                        $image->resize(null, $h, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $background = Image::canvas($w, $h);
                        $background->fill('rgba(0,0,0,0.0)');
                        $background->insert($image, 'center')->save($temp,100);
                    }
                // }
            }


            //upload vào media ftp
            if (!empty(config('filesystems.disks.ftp.url'))) {

                $storage = Storage::disk('ftp');
                if (strlen(config('filesystems.disks.ftp.folder'))) {
                    $dir = config('filesystems.disks.ftp.folder')."/{$dir}";
                }
                if ($storage->putFileAs($dir, new File($temp), $filename)) {
                    $result = "/{$path}";
                }
            }

            //upload vào local
            else{

                $storage = Storage::disk('public');
                if ($storage->putFileAs($dir, new File($temp), $filename)) {
                    $result = "/{$path}";
                }
            }

            if (file_exists($temp)) {
                unlink($temp);
}



        } catch (\Exception $e) {
            \Log::error($e);
            return "";
        }

        return $result;
    }





    static function upload_url($url, $dir, $filename, $width = false, $height = false){
        $url = str_replace(' ', '%20', $url);
        try {
            $storage = Storage::disk('ftp');
            $path = "{$dir}/{$filename}.jpg";
            if ($width || $height) {
                if (!is_dir('temp')) {
                    mkdir('temp', 0777);
                }
                $temp = "temp/{$filename}.jpg";
                $w = $width? $width: null;
                $h = $height? $height: null;
                $img = Image::make($url);
                if ($img->width() > $w) {
                    $img->resize($w, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $img->save($temp);
                if (strlen(config('filesystems.disks.ftp.folder'))) {
                    $dir = config('filesystems.disks.ftp.folder')."/{$dir}";
                }
                if ($storage->putFileAs($dir, new File($temp), $filename)) {
                    $result = "/{$path}";
                }
                if (file_exists($temp)) {
                    unlink($temp);
                }
            }else{
                $contents = file_get_contents($url);
                if (strlen(config('filesystems.disks.ftp.folder'))) {
                    $dir = config('filesystems.disks.ftp.folder')."/{$path}";
                }else{
                    $dir = $path;
                }
                $storage->put($dir, $contents);
                $result = "/{$path}";
            }
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    static function delete_image($path){
        try {
            if($path==""){
                return;
            }

            if (strpos($path, '//') > -1) {
            }else{
                if (!empty(config('filesystems.disks.ftp.url'))){
                    Storage::disk('ftp')->delete(config('filesystems.disks.ftp.folder').$path);
                }
                else{
                    Storage::disk('public')->delete(config('filesystems.disks.public.folder').$path);
                }


            }
        } catch (\Exception $e) {
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


}












//=============================================Pháp demo code==================================
//
//<?php
//
//namespace App\Models;
//use Illuminate\Http\File;
//use Illuminate\Support\Facades\Storage;
//use Img;
//
//class Files
//{
//
//    static function media($path){
//        if (strpos($path, '//') > -1) {
//            return $path;
//        }else{
//            return env('FTP_URL').$path;
//        }
//    }
//
//    static function upload($file = false, $dir = '', $filename, $width = false, $height = false){
//        $result = false;
//        try {
//            $extension = $file->getClientOriginalExtension();
//            // $extension = 'jpg';
//            $filename .= ".{$extension}";
//            $path = "{$dir}/{$filename}";
//            if (!empty(config('filesystems.disks.ftp.host'))) {
//                $storage = Storage::disk('ftp');
//                if (!is_dir('temp')) {
//                    mkdir('temp', 0777);
//                }
//                $temp = "temp/{$filename}";
//                $file->move('temp', $filename);
//                if ($width || $height) {
//                    $w = $width? $width: null;
//                    $h = $height? $height: null;
//                    $img = Img::make($temp);
//                    if ($img->width() > $w) {
//                        $img->resize($w, $height, function ($constraint) {
//                            $constraint->aspectRatio();
//                        })->save($temp);
//                    }
//                }
//                if (strlen(config('filesystems.disks.ftp.folder'))) {
//                    $dir = config('filesystems.disks.ftp.folder')."/{$dir}";
//                }
//                if ($storage->putFileAs($dir, new File($temp), $filename)) {
//                    $result = "/{$path}";
//                }
//                if (file_exists($temp)) {
//                    unlink($temp);
//                }
//            }else{
//                $uploadDir = "storage/{$dir}";
//                if (!is_dir($uploadDir)) {
//                    mkdir($uploadDir, 0777, true);
//                }
//                $file->move($uploadDir, $filename);
//                $result = '/'.$uploadDir;
//            }
//        } catch (\Exception $e) {
//            $result = false;
//        }
//
//        return $result;
//    }
//
//    static function upload_url($url, $dir, $filename, $width = false, $height = false){
//        $url = str_replace(' ', '%20', $url);
//        try {
//            $storage = Storage::disk('ftp');
//            $path = "{$dir}/{$filename}.jpg";
//            if ($width || $height) {
//                if (!is_dir('temp')) {
//                    mkdir('temp', 0777);
//                }
//                $temp = "temp/{$filename}.jpg";
//                $w = $width? $width: null;
//                $h = $height? $height: null;
//                $img = Img::make($url);
//                if ($img->width() > $w) {
//                    $img->resize($w, $height, function ($constraint) {
//                        $constraint->aspectRatio();
//                    });
//                }
//                $img->save($temp);
//                if (strlen(config('filesystems.disks.ftp.folder'))) {
//                    $dir = config('filesystems.disks.ftp.folder')."/{$dir}";
//                }
//                if ($storage->putFileAs($dir, new File($temp), $filename)) {
//                    $result = "/{$path}";
//                }
//                if (file_exists($temp)) {
//                    unlink($temp);
//                }
//            }else{
//                $contents = file_get_contents($url);
//                if (strlen(config('filesystems.disks.ftp.folder'))) {
//                    $dir = config('filesystems.disks.ftp.folder')."/{$path}";
//                }else{
//                    $dir = $path;
//                }
//                $storage->put($dir, $contents);
//                $result = "/{$path}";
//            }
//            return $result;
//        } catch (\Exception $e) {
//            return false;
//        }
//    }
//
//    static function upload_image($file = false, $dir, $filename, $width = false, $height = false){
//        $extension = $file->getClientOriginalExtension();
//        $allowedExtensions = array('jpeg', 'jpg', 'png', 'bmp', 'gif', 'ico');
//        if(in_array($extension, $allowedExtensions)){
//            return Files::upload($file, $dir, $filename, $width, $height);
//        }else{
//            return false;
//        }
//    }
//}
