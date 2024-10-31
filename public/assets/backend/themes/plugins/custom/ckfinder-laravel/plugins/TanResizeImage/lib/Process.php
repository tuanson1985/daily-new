<?php

include('Boot.php');

$image = new Image();

if ( $image->isPosted() ) {

    $new_file = $image->resize();
    echo $new_file;
}

return "ok";
