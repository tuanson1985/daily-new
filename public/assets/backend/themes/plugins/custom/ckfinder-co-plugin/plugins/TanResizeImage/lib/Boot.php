<?php

class Image
{
    /**
     * Image location including filename.
     * Example: /uploads/images/my_folder/my_image.jpg
     * @var
     */
    private $imageUrl;
    /**
     * Filename of image including file type.
     * Example: my_image.jpg
     * @var
     */
    private $fileName;
    /**
     * CKFinder folder name image is located in including slashes.
     * Example: /my_folder/
     * @var
     */
    private $folderName;
    /**
     * Store image dimensions for initial setup of window.
     * @var
     */
    private $imageDimensions = array();
    /**
     * Set base directory
     * @var
     */
    private $baseDir;
    /**
     * Initial value for $_POST error
     * @var
     */
    private $postError = true;


    public function __construct()
    {

        $this->baseDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');


        //Set up file location url
        if (isset($_GET['fileUrl']) && !empty($_GET['fileUrl'])) {
            $this->imageUrl = filter_var($_GET['fileUrl'], FILTER_SANITIZE_STRING);
        } else if (isset($_POST['fileUrl']) && !empty($_POST['fileUrl'])) {
            $this->imageUrl = filter_var($_POST['fileUrl'], FILTER_SANITIZE_STRING);
        }

        if ( is_file($this->baseDir . $this->imageUrl) ) {
            $this->imageDimensions = getimagesize($this->baseDir . $this->imageUrl);
        }

        //Set up filename
        if (isset($_GET['fileName']) && !empty($_GET['fileName'])) {
            $this->fileName = filter_var($_GET['fileName'], FILTER_SANITIZE_STRING);
        } else if (isset($_POST['fileName']) && !empty($_POST['fileName'])) {
            $this->fileName = filter_var($_POST['fileName'], FILTER_SANITIZE_STRING);
        }

        //Set up folder name
        if (isset($_GET['folderName']) && !empty($_GET['folderName'])) {
            $this->folderName = str_replace('/', '', filter_var($_GET['folderName'], FILTER_SANITIZE_STRING) );
        } else if (isset($_POST['folderName']) && !empty($_POST['folderName'])) {
            $this->folderName = str_replace('/', '', filter_var($_POST['folderName'], FILTER_SANITIZE_STRING) );
        }

    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    public function isPosted() {

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($this->imageUrl) && !empty($this->fileName) ) {
            $this->postError = false;

            return true;
        }

        return false;
    }

    public function resize()
    {

        if ($this->postError) {
            return false;
        }



//        //check có crop ảnh không
//        if ($_POST['post_crop_image']==true){
//            $targ_w = $_POST['w'];
//            $targ_h = $_POST['h'];
//            if(!$targ_w>0 && !$targ_h>0){
//                return  'Vui lòng chọn vùng cắt ảnh';
//            }
//
//            $jpeg_quality = (int) $_POST['imageQuality'];
//
//            $imageFileName=$this->baseDir . $this->imageUrl;
//            $pathInfo=pathinfo($imageFileName);
//            $fileExtension=strtolower($pathInfo['extension']);
//
//            if ($fileExtension=='png') {
//                $img_r = imagecreatefrompng($this->baseDir . $this->imageUrl);
//            } else {
//                $img_r = imagecreatefromjpeg($this->baseDir . $this->imageUrl);
//            }
//
//            $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
//
//            imagecopyresampled( $dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h'] );
//
//            $saveDir = dirname($this->baseDir . $this->imageUrl);
//            //
//            if ($_POST['post_over_write']==true) {
//                $saveFile =$this->fileName;
//            } else {
//                $saveFile = $this->createFilename($this->fileName, '_' . (int) $_POST['w'] . 'x' . (int) $_POST['h']);
//            }
//
//            $filename = $saveDir. '/' . $saveFile;
//            imagejpeg($dst_r, $filename, $jpeg_quality);
//            //nếu crop xong cắt ảnh
//            if ($_POST['post_resize']==true && is_numeric($_POST['post_resize_width']) && is_numeric($_POST['post_resize_height'])) {
//
//                $new_w_resize=$_POST['post_resize_width'];
//                $new_h_resize=$_POST['post_resize_height'];
//                //create file append thumbnail text
//                $dst_dir=$saveDir. '/'.$this->createFilename($filename,'_thumbnail_'.$new_w_resize.'x'.$new_h_resize);
//
//                //get image cropped
//                $source_file=$filename;
//                $this->resize_crop_image($new_w_resize,$new_h_resize,$source_file,$dst_dir,$jpeg_quality);
//            }
//        }
//        //nếu chỉ có resize ảnh
//        else{
//
//
//        }
        //nếu chỉ có resize ảnh


//        var_dump($_POST);
//        die('ok rrrr2222');

        $jpeg_quality = (int) $_POST['imageQuality'];
        $post_resize=$_POST['post_resize'];
        if($post_resize!=-1){
            $arrSize=explode('x',$post_resize);
            $new_w_resize=$arrSize[0];
            $new_h_resize=$arrSize[1];

        }
        else{
            $new_w_resize=$_POST['custom_width'];
            $new_h_resize=$_POST['custom_height'];
        }

        if (is_numeric($new_w_resize) && is_numeric($new_h_resize)) {


            //check file exist
            if ( !is_file($this->baseDir . $this->imageUrl) ) {
                return false;
            }

            //create file append thumbnail text
            $saveDir = dirname($this->baseDir . $this->imageUrl);
            $dst_dir=$saveDir. '/'.$this->createFilename($this->fileName,$new_w_resize.'x'.$new_h_resize);

            $saveFile =$this->fileName;
            $filename = $saveDir. '/' . $saveFile;


            //get image cropped
            $source_file=$filename;
            $this->resize_crop_image($new_w_resize,$new_h_resize,$source_file,$dst_dir,$jpeg_quality);
        }
        $dataResult=[
            'success'=>true,
            'filename'=>$this->createFilename($this->fileName,$new_w_resize.'x'.$new_h_resize)

        ];
        return json_encode($dataResult);

    }




    //resize and crop image by center
    function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch(strtolower($mime)){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = $quality;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);

        #fill nền trong suốt cho png
        imagealphablending( $dst_img, false );
        imagesavealpha( $dst_img, true );

        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);

        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);

    }

    private function createFilename($fileName,$nameType='')
    {
        $ext = substr($fileName, strrpos($fileName, "."));
        return basename($fileName, $ext) .'_'. $nameType . strtolower($ext);
    }

    public function getWidth()
    {
        return $this->imageDimensions[0];
    }

    public function getHeight()
    {
        return $this->imageDimensions[1];
    }

    public function getUrl()
    {
        return $this->imageUrl;
    }

    public function getName()
    {
        return $this->fileName;
    }

    public function getFolderName()
    {
        return $this->folderName;
    }

}
