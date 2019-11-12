<?php

$curl = curl_init();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch($requestMethod) {
    case 'POST':
    break;
    case 'GET':
    $user = $_GET['useremail'];
    $token = $_GET['token'];
    //$user = "yosuke.sawamura@zoomjp.hopto.org";
    //$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJIV0Y4MEpLbFRUeWk4TUJFNktzdW9nIiwiZXhwIjoxNTY1OTUzNzgxfQ.mTF2vBM3W5QAQLxqs3zftV0eRUckNSRe0W2vPxM3uUE";
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.zoom.us/v2/users/" . $user . "/meetings?page_number=1&page_size=30&type=upcoming",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "authorization: Bearer " . $token
        ),
      ));
      
      $response = curl_exec($curl);
      $err = curl_error($curl);
      
      curl_close($curl);
      
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }

    break;
    default:
} 

