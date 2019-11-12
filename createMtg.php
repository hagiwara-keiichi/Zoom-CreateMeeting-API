<?php

$curl = curl_init();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch($requestMethod) {
    case 'POST':
    $user = $_POST['useremail'];
    $token = $_POST['token'];
    $data = $_POST['data'];
    //$user = "yosuke.sawamura@zoomjp.hopto.org";
    //$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJIV0Y4MEpLbFRUeWk4TUJFNktzdW9nIiwiZXhwIjoxNTY1OTU2MzgyfQ.bB7_h-qzETj1DPt7OpbRoSr6DXf_zxQCy_ZeGApigxM";
        
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.zoom.us/v2/users/" . $user . "/meetings",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
          "authorization: Bearer " . $token,
          "content-type: application/json"
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
    case 'GET':
    break;
    default:
} 

