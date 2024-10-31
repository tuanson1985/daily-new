<?php
ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(-1);

include('lib/Boot.php');
require('config.php');
$image = new Image();
?>



<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        #crop_submit{
            margin: 10px;
        }
        .form-check{
            margin-bottom: 6px;
        }
        .big-checkbox {width: 1.3rem; height: 1.3rem;}
        .form-check-label{cursor: pointer;margin-left: 10px;margin-top: 2px}
    </style>
</head>
<body>



<form id="crop_submit" action="<?php echo strtok($_SERVER["REQUEST_URI"],'?') ?>">

    <?php foreach ($options as $option) : ?>
        <div class="form-check">
            <input type="checkbox" class="form-check-input big-checkbox" name="post_resize" id="checkbox<?php echo $option['resize']??""; ?>" value="<?php echo $option['resize']??""; ?>"  <?php echo isset($option['defaultCheck'])?($option['defaultCheck']===true?"checked":""): ""; ?>>
            <label class="form-check-label" for="checkbox<?php echo $option['resize']??""; ?>"><?php echo $option['title']??""; ?></label>
        </div>
    <?php endforeach;?>

    <div class="form-check">
        <input type="checkbox" class="form-check-input big-checkbox" name="post_resize" id="checkbox5" value="-1" >
        <label class="form-check-label" for="checkbox5">Tùy chọn size</label>
    </div>


    <div class="form-row custom_size_block">
        <div class="form-group col-4">
            <label for="">Width:</label>
            <input type="text" id="resize_width" class="form-control crop_input update-api mb-2" disabled  name="custom_width"  value="">
        </div>
        <div class="form-group col-4">
            <label for="">Height:</label>
            <input type="text" id="resize_height" class="form-control crop_input update-api mb-2" disabled name="custom_height"  value="">
        </div>
        <div class="form-group col-4">
            <label for="">Quality:</label>
            <input type="text" id="imageQuality" class="form-control crop_input update-api mb-2" disabled name="imageQuality"  value="<?php echo $quality??"100"; ?>">
        </div>
    </div>

    <input type="hidden" id="image_url" name="fileUrl" value="<?php echo $image->getUrl(); ?>" />
    <input type="hidden" id="image_name" name="fileName" value="<?php echo $image->getName(); ?>" />
    <input type="hidden" id="folder_name" name="folderName" value="<?php echo $image->getFolderName(); ?>" />
    <input type="hidden" id="image_quality" name="imageQuality" value="100" />


</form>
<p id="crop_completed_value" style="text-align: center;color: #1bc5bd"></p>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

<script>



    // $("#crop_submit").bind("submit", function(event) {
    //
    //
    //
    //
    //     var values = $(this).serialize();
    //
    //
    //     event.preventDefault();
    //
    //     $.post({
    //         url: './lib/Process.php',
    //         global: false,
    //         type: "POST",
    //         dataType: "text",
    //         data: $(this).serialize(),
    //         beforeSend: function() {},
    //         success: function(response) {
    //             $("#crop_completed_value").html(response);
    //         }
    //     });
    //
    // });

    $('.form-check  input[type="checkbox"]').on('change', function() {

        $('.form-check input[type="checkbox"]').not(this).prop('checked', false);

        if(this.value==-1){
            $("#resize_width").prop('disabled', false);
            $("#resize_height").prop('disabled', false);
            $("#imageQuality").prop('disabled', false);
            $("#resize_width").focus();
        }
        else{
            $("#resize_width").prop('disabled', true);
            $("#resize_height").prop('disabled', true);
            $("#imageQuality").prop('disabled', true);
        }
    });

    // $('#crop_submit input[type=checkbox]').change(function() {
    //
    //     alert('aaaaaa');
    //     $(this).siblings('input[type="checkbox"]').not(this).prop('checked', false);
    //
    //     if(this.value==-1){
    //          $("#resize_width").prop('disabled', false);
    //          $("#resize_height").prop('disabled', false);
    //     }
    //     else{
    //         $("#resize_width").prop('disabled', true);
    //         $("#resize_height").prop('disabled', true);
    //     }
    //
    //
    // });



    // $("#btnCustomSize").click(function(){
    //     $("#resize_width").prop('disabled', false);
    //     $("#resize_height").prop('disabled', false);
    // });

</script>
</body>
</html>
