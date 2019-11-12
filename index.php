<?php

$requestMethod = $_SERVER['REQUEST_METHOD'];

$msg = '';
$useremail = '';
$password = '';
$id_pw_found;
$jsonEncodedReturnArray = '';

switch($requestMethod) {
    case 'POST':
    Login();
    break;
    case 'GET':
    TopPage();
    break;
}

function Login() {
    if (isset($_POST['useremail']) && isset($_POST['password'])) {
        $useremail = $_POST['useremail'];  
        $password = $_POST['password'];
        $api_key = '<USE YOUR WEB API KEY>';
        $api_secret = '<USE YOUR WEB API SECRET>';
        $exp = strtotime("+ 90 minutes");
        $file = fopen('./users.txt', 'r');

        while(!feof($file)){
            $line = fgets($file);
            list($user, $pass) = explode(',', $line);
            if(trim($user) == $useremail && trim($pass) == $password){
                require_once('jwt.php');
            
                // create a token
                $payloadArray = array();
                $payloadArray['iss'] = $api_key;
                if (isset($exp)) {$payloadArray['exp'] = $exp;}
                $token = JWT::encode($payloadArray, $api_secret);

                SecondPage($useremail,$token);
                $id_pw_found = 1;
                break;
            }
        }
        fclose($file);
        if(!$id_pw_found){
            $msg = 'Logged in failed';
            TopPage($msg);
        }
    }
}

function TopPage($msg) {
echo <<<EOM
<html lang = "en">
<head>
 <link href="./css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table><tr><th>
<h3>Sample WebSite</h3>
    <form action="" method="POST">
        <input type="text" name="useremail" placeholder="useremail" size="40" required autofocus></br>
        <input type="password" name="password" placeholder="password" size="40" required></br>
        <input type="submit" value="Login">
    </form>
EOM;
echo $msg;
echo <<<EOM
<br>
</th></tr></table>
</body>
</html>
EOM;
}

function SecondPage($a,$b) {
echo <<<EOM
<html lang = "en">
<head>
 <link href="./css/style.css" rel="stylesheet" type="text/css">
</head>
 <body>
  <input type="button" id="BtnGetMtg" value="Get Meeting List">
  <input type="button" id="BtnCreateMtg" value="Create New Meeting">
  <input type="button" value="Logout" onclick="window.location='./';">
 <hr></hr>

 <p id="results">
  <table><tr><th>
   <img src="img/sample.png" style="width:500px;height:400px;">
  </th></tr></table>
 </p>
 
  <script>
   var useremail = '$a';
   var token = '$b';
   
   console.log("Logged in");
   console.log("useremail : " + useremail);
   console.log("token : " + token);
   //localStorage.setItem('token', token);

   document.getElementById("BtnGetMtg").onclick = function(){
    console.log("BtnGetMtg");
    console.log("BtnGetMtg --> Request");
    var data = null;
    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function () {
       if (this.readyState === this.DONE) {
         console.log("BtnGetMtg <-- Response");
         console.log(this.responseText);
         meetingList(this.responseText);
       }
     });

    xhr.open("GET", "./getMtg.php?useremail=" + useremail + "&token=" + token);
    xhr.send(data);
   };

   function meetingList(v){
    var obj, i, x = "";
    obj = JSON.parse(v);
    
    for (i in obj.meetings) {
        var date = new Date(obj.meetings[i].start_time);
        x += date.toLocaleDateString() +
        " " + date.toLocaleTimeString() +
        " " + obj.meetings[i].topic +
        " " + "<input type='button' value=" + obj.meetings[i].id + " onclick='joinMtg("+obj.meetings[i].id+")'><br>";
    }
    document.getElementById("results").innerHTML = x;
   };

   var toDoubleDigits = function(num) {
    num += "";
     if (num.length === 1) {
       num = "0" + num;
     }
    return num;
   };

   function joinMtg(v){
     var mtgid = v;
     var x = '<iframe src="websdk.php?token=' + token + '&useremail=' + useremail + '&meeting=' + mtgid + '"></iframe>';
     console.log(x);
     document.getElementById("results").innerHTML = x;
   };

   document.getElementById("BtnCreateMtg").onclick = function(){
    console.log("BtnCreateMtg");
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth()+1).padStart(2, '0'); //January is 0!
    var hh = String(toDoubleDigits(today.getHours()));
    var min = String(toDoubleDigits(today.getMinutes()));
    var yyyy = today.getFullYear();
    today = yyyy+'-'+mm+'-'+dd+'T'+hh+':'+min+':00';
    //console.log(today);
    var x = '\
    <form>\
    <h3>General</h3>\
       topic: <input type="text" id="topic" autofocus><br>\
       type: <select id="type">\
        <option value="2">Scheduled Meeting</option>\
        <option value="1">Instant Meeting</option>\
       </select><br>\
       start_time: <input type="datetime-local" id="start_time" value="'+ today +'"><br>\
       duration: <select id="duration">\
        <option value="60">60</option>\
        <option value="120">120</option>\
        <option value="180">180</option>\
       </select><br>\
       timezone: <select id="timezone">\
        <option value="Asia/Tokyo">(GMT+09:00) Asia/Tokyo</option>\
        <option value="Australia/Sydney">(GMT+10:00) Australia/Sydney</option>\
      </select><br>\
      password: <input type="text" id="password" maxlength="10"><br>\
      agenda: <input type="text" id="agenda"><br>\
      <h3>Settings</h3>\
      alternative_hosts: <input type="text" id="alternative_hosts"><br>\
      duration: <select id="approval_type">\
        <option value="2">No registration required</option>\
        <option value="0">Automatically approve</option>\
        <option value="1">Manually approve</option>\
       </select><br>\
       audio: <select id="audio">\
        <option value="both">Both telephony and computer</option>\
        <option value="telephony">Telephony audio only</option>\
        <option value="voip">Computer audio only</option>\
       </select><br>\
       auto_recording: <select id="auto_recording">\
        <option value="none">Disable</option>\
        <option value="local">Record on local</option>\
        <option value="cloud">Record on cloud</option>\
       </select><br>\
       close_registration: <select id="close_registration">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       cn_meeting: <select id="cn_meeting">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       enforce_login: <select id="enforce_login">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       enforce_login_domains: <input type="text" id="enforce_login_domains"><br>\
       host_video: <select id="host_video">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       in_meeting: <select id="in_meeting">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       join_before_host: <select id="join_before_host">\
        <option value="true">Enable</option>\
        <option value="false">Disable</option>\
       </select><br>\
       mute_upon_entry: <select id="mute_upon_entry">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       participant_video: <select id="participant_video">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       registrants_confirmation_email: <select id="registrants_confirmation_email">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       waiting_room: <select id="waiting_room">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       watermark: <select  id="watermark">\
        <option value="false">Disable</option>\
        <option value="true">Enable</option>\
       </select><br>\
       </form>\
       <p><input type="button" value="Create" onclick="createMtg();" ></p>\
    ';
    document.getElementById("results").innerHTML = x;
   };

   function createMtg(){
    console.log("createMtg");
    var s = document.getElementById("start_time").value;
    console.log(s);
    var data = JSON.stringify({
        "topic": document.getElementById("topic").value,
        "type": document.getElementById("type").value,
        "start_time": s + ":00",
        "duration": document.getElementById("duration").value,
        "timezone": document.getElementById("timezone").value,
        "password": document.getElementById("password").value,
        "agenda": document.getElementById("agenda").value,
        "settings": {
          "alternative_hosts": document.getElementById("alternative_hosts").value,
          "approval_type": document.getElementById("approval_type").value,
          "audio": document.getElementById("audio").value,
          "auto_recording": document.getElementById("auto_recording").value,
          "close_registration": document.getElementById("close_registration").value,
          "cn_meeting": document.getElementById("cn_meeting").value,
          "enforce_login": document.getElementById("enforce_login").value,
          "enforce_login_domains": document.getElementById("enforce_login_domains").value,
          "host_video": document.getElementById("host_video").value,
          "in_meeting": document.getElementById("in_meeting").value,
          "join_before_host": document.getElementById("join_before_host").value,
          "mute_upon_entry": document.getElementById("mute_upon_entry").value,
          "participant_video": document.getElementById("participant_video").value,
          "registrants_confirmation_email": document.getElementById("registrants_confirmation_email").value,
          "waiting_room": document.getElementById("waiting_room").value,
          "watermark": document.getElementById("watermark").value
        }
      });
      console.log("createMtg --> Request");
      console.log(data);
      
      var xhr = new XMLHttpRequest();
      xhr.withCredentials = true;
      
      xhr.addEventListener("readystatechange", function () {
        if (this.readyState === this.DONE) {
          console.log("createMtg <-- Response");
          console.log(this.responseText);
        }
      });
      
      xhr.open("POST", "./createMtg.php");
      console.log(token);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send("useremail=" + useremail + "&token=" + token + "&data=" + data);
    };

  </script>
  </body>
</html>
EOM;
}
?>
