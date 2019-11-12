<?php

$requestMethod = $_SERVER['REQUEST_METHOD'];
$apiSecret = '<USE YOUR WEB API SECFRET>';

switch($requestMethod) {
    case 'POST':
    break;
    case 'GET':
    
    $token = $_GET['token'];
    $useremail = $_GET['useremail'];
    $meeting = $_GET['meeting'];

    //DECODE API_KEY FROM TOKEN
    $apikey = generate_apikey($token, $apiSecret);

    //GENERATE ACCESS SIGNATURE FROM API_KEY AND API SECRET, MEETING ID AND ROLE
    $gensig = generate_signature($apikey, $apiSecret, $meeting, '1');

    WebSDK($useremail, $meeting, $apikey, $gensig);

    break;
    default:
}

function generate_apikey($t, $s){
    require_once('jwt.php');
    try {
        $payload = JWT::decode($t, $s, array('HS256'));
        $returnArray = array('iss' => $payload->iss);
    }
    catch(Exception $e) {
        $returnArray = array('error' => $e);
    }
    return trim(json_encode($returnArray['iss'], JSON_PRETTY_PRINT), '"');
}

function generate_signature ( $api_key, $api_sercet, $meeting_number, $role){
	$time = time() * 1000; //time in milliseconds (or close enough)
	$data = base64_encode($api_key . $meeting_number . $time . $role);
	$hash = hash_hmac('sha256', $data, $api_sercet, true);
	$_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
	//return signature, url safe base64 encoded
	return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
}

function WebSDK($a, $b, $c, $d){
echo <<<EOM
<head>
    <title>Zoom JSSDK</title>
    <meta charset="utf-8" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.5.0/css/bootstrap.css"/>
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.5.0/css/react-select.css"/>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<style>
body {padding-top: 50px;}
</style>
<body>
<input type=hidden id="email" value=$a>
<input type=hidden id="mtgnum" value=$b>
<input type=hidden id="apikey" value=$c>
<input type=hidden id="sig" value=$d>

        <script src="https://source.zoom.us/1.5.0/lib/vendor/react.min.js"></script>
        <script src="https://source.zoom.us/1.5.0/lib/vendor/react-dom.min.js"></script>
        <script src="https://source.zoom.us/1.5.0/lib/vendor/redux.min.js"></script>
        <script src="https://source.zoom.us/1.5.0/lib/vendor/redux-thunk.min.js"></script>
        <script src="https://source.zoom.us/1.5.0/lib/vendor/jquery.min.js"></script>
        <script src="https://source.zoom.us/1.5.0/lib/vendor/lodash.min.js"></script>
        <script src="https://source.zoom.us/zoom-meeting-1.5.0.min.js"></script>
        <script src="js/index.js?20190815"></script>
    </body>
</html>
EOM;
}
?>
