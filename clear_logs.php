<?php
$f = @fopen("logs/logs.txt", "r+");
if ($f !== false) {
    ftruncate($f, 0);
    fclose($f);
}
echo json_encode(array('success' => 'true'));
exit;