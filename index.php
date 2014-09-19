<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6 lte9 lte8 lte7" lang="en"><![endif]-->
<!--[if IE 7 ]><html class="ie ie7 lte9 lte8 lte7" lang="en"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8 lte9 lte8" lang="en"><![endif]-->
<!--[if IE 9 ]><html class="ie ie9 lte9" lang="en"><![endif]-->
<!--[if gt IE 9]><!--><html class="" lang="en"><!--<![endif]-->
<head>
  <meta name='copyright' content='Copyright 2014. CASUAL-Dev Ms-RL License http://www.microsoft.com/en-us/openness/licenses.aspx'>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta property="og:title" content="CASUAL-Dev Minimal Chat Extreme" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://chat.casual-dev.com" />
  <title>Minimal Chat Extreme</title>
  <link rel="shortcut icon" href="favicon.ico">
</head>
<body id="body"  onload="runAtLoad()" itemscope itemtype="https://schema.org/CommunicateAction">
  <meta name=viewport content="width=device-width, initial-scale=1">
  <meta property="languate" content="english" />
  <meta property="recipient" content="everyone" />
  <meta property="agent" content ="user" />
  <meta property="instrument" content="CASUAL-Dev chat" />
  <meta property="participant" content="user"/>


  <!--set up cookies used for saving username-->
  <script >
    function getCookie(cname) {
     var name = cname + "=";
     var ca = document.cookie.split(';');
     for(var i=0; i<ca.length; i++) {
         var c = ca[i];
         while (c.charAt(0)==' ') c = c.substring(1);
         if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
     }
     return "";
    }
  </script>
  <div id="chat" >
    <!-- Chat submission form -->
    <div id="input" >
      <form id="inputfrm" name="input" action="submit.php" method="get">
        <img itemprop="image" hidden width="0" height="0" src="CASUALDude.png" />
        <INPUT TYPE="text" hidden name="room" size="10" maxlength="10" value="<?php print(($_GET['room'] == "") ? "anything" : $_GET['room']); ?>">
        <div id=name><input TYPE = "Text" size="15" maxlength="20"  id="username" VALUE="" NAME="user" placeholder="Who Are You?" ></span></div>
        <div id=textsubmit><INPUT id="textinput" TYPE = "Text" VALUE ="" maxlength="500" NAME = "chat" placeholder="Type Stuff Here" >
        <input ID="submit" type="submit" value="send"></div>
        <span ID="notifications" style="white-space: nowrap; id="clear"><input checked type="checkbox" name="sound" value="checked">Notifications?</span>
        <div id=clear>
          <span style="white-space: nowrap; id="clear"><a  rel="nofollow" href="clear.php?room=<?php print(($_GET['room'] == "") ? "anything" : $_GET['room']); ?>">clear the chat</a>
        </div>
      </form>
    </div>
    <hr>


    <!-- Chat output area & control scripts -->
    <div id="output" itemprop="result" itemprop="description">
      <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
      <script> 

        var xmlhttp;
        var oldFileSize=0;
        var updateRequired=false;
        var cycles=0.20;
        var curCycle=1;
        // at startup, set username and load chat document.
        function runAtLoad(){ 
         loadXMLDoc();
         inputfrm.elements['username'].value=getCookie('user');
        }

        function loadXMLDoc(){
          curCycle++;
          if (curCycle < cycles){

            return;
          } else {
            curCycle=0;
          }
          //use jquery to check if updates are required. 
          jQuery.ajax({cache: false,type: 'HEAD', url: "rooms/<?php print(($_GET['room'] == "") ? "anything" : $_GET['room']); ?>.txt", success: function(d,r,xhr){ var size=xhr.getResponseHeader('Content-Length'); if (size != oldFileSize) {updateRequired=true; oldFileSize=size; console.log("update Required"); cycles=.2; curcycle=0; } else { updateRequired=false; if (cycles<5) cycles=cycles+0.2; console.log("Will check for updates after "+cycles+" seconds");}},});
          if (updateRequired==true){
            console.log("Downloading new content");
            if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
              xmlhttp=new XMLHttpRequest();
            }else{// code for IE6, IE5
              xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function(){
              if (xmlhttp.readyState==4 && xmlhttp.status==200){
                var text=xmlhttp.responseText;
                if (text.indexOf("username") < 0){
                    notifyEmpty();
                    return;
                }
                var lines = text.split('\n');
                var text="";
                var curtime=new Date().getTime()/1000;
                for(var i = 0;i < lines.length;i++){
                 if (lines[i]=="") continue;
                 var timestamp=lines[i].split(">")[5].split("<")[0];
                 lines[i]=lines[i].replace(timestamp, " about "+ timeDifference(curtime,timestamp));
                
                 text=text+lines[i]+"\n";
                  
                }

                document.getElementById('output').innerHTML = text;
              }
            }
            xmlhttp.open("GET","rooms/<?php print(($_GET['room'] == "") ? "anything" : $_GET['room']); ?>.txt",true);
            xmlhttp.send();


            //play audio
            if (! new Audio().canPlayType('audio/mpeg;')) {
              document.getElementById("notifications").style.display = "none";
            } else if (inputfrm.elements['sound'].checked){
              new Audio("sounds/ding.mp3").play(); // buffers automatically when created

            }
          }
        }
        //once a second, call loadSXMLDoc which SHOULD pull from cache unless changed
        setInterval(function(){loadXMLDoc();},1000);

        //Override submit - this adds blank username checks and submit without page refresh
        $("#submit").click(function() {
          console.log(document.getElementById('username').value);
          document.cookie='user='+document.getElementById('username').value+';';
          if ( inputfrm.elements['username'].value  == "" ) { //verify username isn't blank
            alert("pick a username");
            return false;
          }
          var url = "submit.php"; //submit to submit.php
          $.ajax({
            type: "get",
            url: url,
            data: $("#inputfrm").serialize(), // serializes the form's elements.
          });
          inputfrm.elements['chat'].value='';
          return false; // avoid to execute the actual submit of the form.
        });

        //prints the "cleared" message if the room is cleared.
        function notifyEmpty(){
        //room was cleared notification
            xmlhttp.open("GET",'rooms/<?php print(($_GET['room'] == "") ? "anything" : $_GET['room']); ?>clear.txt',true);
            xmlhttp.send();
            xmlhttp.onreadystatechange=function(){
               if (xmlhttp.readyState==4 && xmlhttp.status==200){
                  console.log(xmlhttp.responseText)
                  document.getElementById('output').innerHTML =xmlhttp.responseText;
               }
            }
        }
        function timeDifference(current, previous) {
          var secPerMinute = 60
          var secPerHour = secPerMinute * 60;
          var secPerDay = secPerHour * 24;
          var secPerMonth = secPerDay * 30;
          var secPerYear = secPerDay * 365;
          var elapsed = current - previous;
          if (elapsed < secPerMinute) {
               return Math.round(elapsed) + ' seconds ago';   
          }
          else if (elapsed < secPerHour) {
               return Math.round(elapsed/secPerMinute) + ' minutes ago';   
          }
          else if (elapsed < secPerDay ) {
               return Math.round(elapsed/secPerHour ) + ' hours ago';   
          }
          else if (elapsed < secPerMonth) {
              return 'approximately ' + Math.round(elapsed/secPerDay) + ' days ago';   
          }
          else if (elapsed < secPerYear) {
              return 'approximately ' + Math.round(elapsed/secPerMonth) + ' months ago';   
          }
          else {
              return 'approximately ' + Math.round(elapsed/secPerYear ) + ' years ago';   
          }
      }
      </script>
    </div>
  </div>
  <script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "CommunicateAction",
    "agent": {
      "@type": "Person",
      "name": "User"
    },
    "recipient": {
      "@type": "Person",
      "name": "User"
    }
  }
  </script>
</body><link rel="stylesheet" href="css/style.css">
</html>
