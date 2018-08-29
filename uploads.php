<?php
$files = array();
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".dirname($_SERVER['PHP_SELF']);
if (isset($_FILES['files']) && !empty($_FILES['files'])) {
    $no_files = count($_FILES["files"]['name']);
    for ($i = 0; $i < $no_files; $i++) {
        if ($_FILES["files"]["error"][$i] > 0) {
            echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
        } else {
            if (file_exists('uploads/' . $_FILES["files"]["name"][$i])) {
                echo 'File already exists';                
            } else {
                $newfilename = round(microtime(true));
                move_uploaded_file($_FILES["files"]["tmp_name"][$i], 'uploads/' . str_replace(' ','-',$newfilename.'_'.$_FILES["files"]["name"][$i]));                
                $files[$i]['Name']=str_replace(' ','-',$newfilename.'_'.$_FILES["files"]["name"][$i]);
                $files[$i]['url']=$actual_link.'/uploads/'.str_replace(' ','-',$newfilename.'_'.$_FILES["files"]["name"][$i]);
            }
        }
    }
}
echo json_encode(array('files' => $files));
exit;