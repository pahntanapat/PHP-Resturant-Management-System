<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
$sess=new Session();
if(!$sess->load()->checkSession()){ // If no session
	if($config->isAjax()){ // Connection via AJAX & JSON
		$resp=new SKAjax();
		if($sess->isAuth(Session::AUTH_REJECT_LOGIN)) // If Log in wrong >= Reject limit
			$resp->addAction(SKAjax::REDIRECT,"login.php");//Redirect
		elseif($sess->isAuth(Session::AUTH_REJECT_PIN)) // If PIN Wrong >= Reject limit
			$resp->addShowDialog(SKAjax::LOGIN_DIALOG,@$_GET['form']);// Show Log in dialog
		elseif($sess->loadCookie($config->PDO())) //If has cookie and In Zone That use cookie
			$resp->addShowDialog(SKAjax::PIN_DIALOG,@$_GET['form']);// PIN Dialog
		exit($resp->toJSON());
	}else{
		header("Location: login.php");
		exit("Please log in");
	}
}

$sess->setAuth(Session::AUTH_NO); // Clear Auth Value After Processing

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<!-- TemplateBeginEditable name="doctitle" -->
<title>ระบบจัดการร้านอาหาร</title>
<!-- TemplateEndEditable -->
<link href="../css/skg.min.css" rel="stylesheet" type="text/css">
<link href="../css/jquery.mobile.icons.min.css" rel="stylesheet" type="text/css">
<link href="../css/jquery.mobile.structure-1.4.3.min.css" rel="stylesheet" type="text/css">
<link href="../css/resturant.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<!--<script type="text/javascript" src="../js/jquery.mobile-1.4.2.min.js"></script>-->
<script src="../js/jquery.mobile-1.4.3.min.js"></script>
<script type="text/javascript" src="../js/resturant.js"></script>
<script type="text/javascript" src="../js/mobile.js"></script>
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
</head>

<body>
<!-- TemplateBeginEditable name="page" -->
<div data-role="page" id="page1">
  <div data-role="header"><a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
    <h1>Header</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">Content</div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div><!-- TemplateEndEditable -->
<div data-role="dialog" id="loginDialog">
  <div data-role="header">
    <h1>กรุณา Log in</h1>
  </div>
  <div data-role="content">
    <form action="../login.scr.php" method="post" name="loginForm" id="loginForm" data-ajax="false">
      <div data-role="fieldcontain">
        <label for="phone">Phone:</label>
        <input type="tel" name="phone" id="phone" required />
      </div>
        <div data-role="fieldcontain">
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required />
        </div>
        <div class="center"><img src="../securimage/securimage_show.php?<?=uniqid()?>" alt="จงตอบคำถามต่อไปนี้" name="captchaIMG" height="96" id="captchaIMG"><br><input name="reload" type="button" id="reload" value="Reload คำถาม" data-icon="refresh">
</div>
    <div data-role="fieldcontain">
      <label for="captcha">คำตอบ = </label>
      <input type="text" name="captcha" id="captcha" required />
    </div>
    <div data-role="controlgroup">
      <input name="Submit" type="submit" id="Submit" value="Log in" data-icon="check" data-iconpos="left" />
      <input name="Reset" type="reset" value="Cancel" data-icon="delete" data-iconpos="left" />
    </div>
    </form>
  </div>
</div>
<div data-role="dialog" id="pinDialog">
  <div data-role="header">
    <h1>กรุณากรอก PIN</h1>
  </div>
  <div data-role="content">
    <form action="../login.scr.php" method="post" name="pinForm" id="pinForm" data-ajax="false">
      <div data-role="fieldcontain">
        <label for="pin">PIN:</label>
      <input name="pin" type="password" id="pin" size="16" maxlength="4" required /></div>
        <div data-role="controlgroup" data-type="horizontal">
          <input name="OK" type="submit" id="OK" value="OK" data-icon="check" />
          <input name="cancel" type="reset" id="cancel" value="Cancel" data-icon="delete" />
      </div>
    </form>
  </div>
</div>
</body>
</html>
