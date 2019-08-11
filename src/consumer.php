<?php

    require_once('../config/config.php'); // include database config data

    require_once('./autoloader.php'); // include autoloader function
    autoload();

    $input = json_decode(file_get_contents('php://input'),true);
    $input = ['forgot', 'helloprint'];
    
    // separate command from params
    $command = $input[0];
    $params = array_slice($input, 1);
    
    $requests = new consumerrequests(); // this is the class that processes the requests
    
    $result = $requests->process($command, $params);

    header('Content-type: application/json');
    print json_encode($result);
?>