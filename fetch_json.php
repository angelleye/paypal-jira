<?php

try {
    $str = file_get_contents('includes/saved_configuration.json');
    if(!empty($str)){
        $array = json_decode($str);
        $array->today_date = date('m/d/Y');
        echo json_encode(array('success' => 'true' , 'msg' => 'success', 'data' => $array));
    }
    else{
        echo json_encode(array('success' => 'false' , 'msg' => 'No settings found.'));
    }
}
catch (Exception $e) {
    echo $e->getMessage();
}