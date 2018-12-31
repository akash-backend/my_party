
<head>
<title>Myparty</title>
<link rel="stylesheet" type="text/css" href="assets/css/scam.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
.bg-white {background: #fff;}
</style>
<link rel="stylesheet" type="text/css" href="assets/css/scam.css">
</head>
<body style="background: #ccc; display: table; margin:0 auto; height: 100%;">
<?php 
  include_once("Mobile_Detect.php");
  $id = $_REQUEST['id'];
  $user_id = $_REQUEST['user_id'];
 
  $detect = new Mobile_Detect();
  if($detect->isAndroid()) {

     echo "The app is not available in Android at this time. ";
  }
elseif($detect->isIphone()) {  
?>  


 

<div class="container text-center" style="display: table-cell; 
  vertical-align:middle;">
    <div class="row">
      <div class=" col-md-12 bg-white" style="padding: 0px 0px 50px 0px; color: #fff;">
        <header style="background: #0cc; padding: 15px;">
          <h2 style="margin:0px;">My Party for Iphone</h2>
        </header>
        <img src="../assets/logo.png">
        <div>
          
          <a href="myparty://com.ctinfotech.hamro.nepali.music.activity/<?php echo $id;?>/<?php echo $user_id; ?>" class="btn btn-info">Myparty Event Detail</a> 

          <a  href="https://itunes.apple.com/us/app/sporto-pick-up-sport-nearby/id1418489329?ls=1&mt=8" class="btn btn-info">Download My Party App</a>
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
