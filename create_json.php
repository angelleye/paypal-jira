<?php

if(isset($_POST['form_data'])){
    $params = array();
    parse_str($_POST['form_data'], $params);
    $params=array_map("trim",$params);
    $fp = fopen('includes/saved_configuration.json', 'w');
    fwrite($fp, json_encode($params,JSON_PRETTY_PRINT));
    fclose($fp);
    echo json_encode(array('success'=>'true'));
    exit;
}
else{
    echo json_encode(array('success'=>'false'));
    exit;
}
