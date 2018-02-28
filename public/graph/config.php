<?php

define("SERVICE_URL", "http://localhost:8080/api/");



function get_service($service_name){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,SERVICE_URL.$service_name);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec ($ch);
    curl_close ($ch);

    return json_decode($response);

}
