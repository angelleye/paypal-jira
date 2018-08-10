<?php

try {
    $str = file_get_contents('includes/saved_configuration.json');
    if(!empty($str)){
        $array = json_decode($str);
        echo json_encode(array('success' => 'true' , 'msg' => 'success', 'data' => $array));
    }
    else{
        echo json_encode(array('success' => 'false' , 'msg' => 'No settings found.'));
    }
}
catch (Exception $e) {
    echo $e->getMessage();
}