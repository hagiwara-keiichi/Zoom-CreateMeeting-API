(function(){

	console.log('checkSystemRequirements');
	console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));

    ZoomMtg.preLoadWasm();
    ZoomMtg.prepareJssdk();
    
    var API_KEY = document.getElementById('apikey').value;
    var USER_EMAIL = document.getElementById('email').value;
    var MEETING_NUMBER = document.getElementById('mtgnum').value;
    var SIGNATURE = document.getElementById('sig').value;
/*
    var SIGNATURE = ZoomMtg.generateSignature({
        meetingNumber: document.getElementById('mtgnum').value,
        apiKey: document.getElementById('apikey').value,
        apiSecret: "H3rSbrEu2pxdmnztT8P6vFPZrhZbfjEER5en",
        role: 1,
        success: function(res){
            console.log(res.result);
        }
    });
*/
    ZoomMtg.init({
            leaveUrl: './blank.html',
            isSupportAV: true,
            showMeetingHeader: true,       // optional
            disableInvite: false,           // optional
            disableCallOut: false,          // optional
            disableRecord: false,           // optional
            disableJoinAudio: false,        // optional
            audioPanelAlwaysOpen: true,     // optional
            showPureSharingContent: true,  // optional
            isSupportChat: true,            // optional
            screenShare: true,              // optional
            rwcBackup: '',                  // optional
            videoDrag: true,                // optional
            sharingMode: 'both',            // optional
            videoHeader: true,              // optional
            isLockBottom: true,             // optional
            isSupportNonverbal: true,       // optional
            success: function () {
                ZoomMtg.join(
                    {
                        meetingNumber: MEETING_NUMBER,
                        //userName: meetConfig.userName,
                        userName: USER_EMAIL,
                        //signature: signature,
                        signature: SIGNATURE,
                        //apiKey: meetConfig.apiKey,
                        apiKey: API_KEY,
                        userEmail: USER_EMAIL,
                        passWord: "",
                        success: function(res){
                            console.log('join meeting success');
                        },
                        error: function(res) {
                            console.log(res);
                        }
                    }
                );
            },
            error: function(res) {
                console.log(res);
            }
        });

})();