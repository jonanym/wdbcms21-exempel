<?php 
// IP-API
$result = [
    'ip' => $_SERVER['REMOTE_ADDR']
]; 

header('content-type: application/json');

echo json_encode($result);

?>