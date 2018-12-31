
<head>
<title>My Party</title>
<link rel="stylesheet" type="text/css" href="assets/css/scam.css">
</head>
<body>
<?php 
  include_once("Mobile_Detect.php");
  // $id = $_REQUEST['id'];
  // $user_id = $_REQUEST['user_id'];
 
  $detect = new Mobile_Detect();
  if($detect->isAndroid()) {

     echo "The app is not available in Android at this time. ";
  }
elseif($detect->isIphone()) {  
?>  
   
    <div class="container" style="overflow:hidden;">
  <div class="innermain" id="innermain">
    <div class="innerright">
      <div class="truemain" id="defContent"> <img src="" id="tracking_url" style="display:none" width="0px" height="0px">
        <div class="_ncontainer">
          <div class="_nheader"> <img src="assets/sporto-fav-icon.png" class="_nlogo">
            <!-- <h1> Myparty <span>for Iphone</span></h1> -->
          </div>
          <div class="_ncontent">
            <div><a href="myparty://com.ctinfotech.hamro.nepali.music.activity/paymentSuccess" class="discover pb-button playhandle" id= "linkid"></a> </div>
            <div id="div-gpt-ad-1393854463579-0" style="width: 100%;text-align: center;position: relative;z-index: 10;"> </div>
             <div class="_ngroup ">
              <a href="https://itunes.apple.com/us/app/sporto-pick-up-sport-nearby/id1418489329?ls=1&mt=8" class="dnapp-button"> </a> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  }
  elseif($detect->isGeneric()) {    
    echo "The app is not available in Windows device at this time. ";
   }
   else{
      echo '<script type="text/javascript">
    window.location.href="http://google.com"</script>';
   echo "web site";
   }  
?>


</body>


<script type="text/javascript">
  window.onload=function(){
  document.getElementById("linkid").click();
};
</script>



