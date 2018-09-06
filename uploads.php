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
                if(isset($_POST['from_config']) && $_POST['from_config'] == 'yes'){
                    $image_info = getimagesize($files[$i]['url']);
                    $image_width = $image_info[0];
                    $image_height = $image_info[1];                    
                    if($image_width > 250 || $image_height > 80){
                        $ext = pathinfo($_FILES["files"]["name"][$i], PATHINFO_EXTENSION);
                        $newwidth = 250;
                        $newheight = ceil(( $image_height / $image_width) * $newwidth);
                        if($ext == 'jpg'){                            
                            $src = imagecreatefromjpeg($files[$i]['url']);
                            $dst = imagecreatetruecolor($newwidth, $newheight);
                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $image_width, $image_height);
                            imagejpeg($dst, __DIR__.'/uploads/'.str_replace(' ','-',$newfilename.'_'.$_FILES["files"]["name"][$i]),100);
                        }
                        if($ext == 'png'){
                            $src = imagecreatefrompng($files[$i]['url']);
                            $dst = imagecreatetruecolor($newwidth, $newheight);
                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $image_width, $image_height);
                            imagepng($dst, __DIR__.'/uploads/'.str_replace(' ','-',$newfilename.'_'.$_FILES["files"]["name"][$i]), 100);
                        }
                    }                    
                }
            }
        }
    }
}
echo json_encode(array('files' => $files));
exit;