<?php
require_once 'config.inc.php';

?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mobile.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>ระบบจัดการร้านอาหาร</title>
<!-- InstanceEndEditable -->
<link href="css/skg.min.css" rel="stylesheet" type="text/css">
<link href="css/jquery.mobile.icons.min.css" rel="stylesheet" type="text/css">
<link href="css/jquery.mobile.structure-1.4.3.min.css" rel="stylesheet" type="text/css">
<link href="css/resturant.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<!--<script type="text/javascript" src="../js/jquery.mobile-1.4.2.min.js"></script>-->
<script src="js/jquery.mobile-1.4.3.min.js"></script>
<script type="text/javascript" src="js/resturant.js"></script>
<script type="text/javascript" src="js/mobile.js"></script>
<!-- InstanceBeginEditable name="head" -->
<script>
$(document).ready(function(e) {
    $("#ajax").click(function(e) {
        $.mobile.changePage("test.php#page2");
    });
});
</script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<div data-role="page" id="page">
  <div data-role="header">
    <h1>Header</h1>
  </div>
  <div data-role="content">
    <p><a href="test.php#page2" data-rel="dialog">Content</a></p>
    <p><a href="#" id="ajax">content Ajax</a></p>
    <p>&nbsp;  <?=Printing::ORDER_CONFIRM|Printing::ORDER_PRINT?>
    <div data-role="fieldcontain">
      <fieldset data-role="controlgroup">
        <legend>Option</legend>
        <label for="checkbox1_0">Option0</label>
        <label for="checkbox1_1">Option1</label>
        <label for="checkbox1_2">Option2</label>
        <input type="checkbox" name="checkbox1" id="checkbox1_0" class="custom" value="" />
        <input type="checkbox" name="checkbox1" id="checkbox1_1" class="custom" value="" />
        <input type="checkbox" name="checkbox1" id="checkbox1_2" class="custom" value="" />
      </fieldset>
    </div>
    <div data-role="fieldcontain">
      <fieldset data-role="controlgroup">
        <legend>Option</legend>
        <input type="radio" name="radio1" id="radio1_0" value="" />
        <input type="radio" name="radio1" id="radio1_1" value="" />
        <input type="radio" name="radio1" id="radio1_2" value="" />
        <label for="radio1_2">Option2</label>
        <label for="radio1_1">Option1</label>
        <label for="radio1_0">Option0</label>
      </fieldset>
    </div>
    </p>
  </div>
  <div data-role="footer">
    <h4>Footer</h4>
  </div>
</div>
<!-- InstanceEndEditable -->
<div data-role="dialog" id="loginDialog">
  <div data-role="header">
    <h1>กรุณา Log in</h1>
  </div>
  <div data-role="content">
    <form action="login.scr.php" method="post" name="loginForm" id="loginForm" data-ajax="false">
      <div data-role="fieldcontain">
        <label for="phone">Phone:</label>
        <input type="tel" name="phone" id="phone" required />
      </div>
        <div data-role="fieldcontain">
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required />
        </div>
        <div class="center"><img src="securimage/securimage_show.php?<?=uniqid()?>" alt="จงตอบคำถามต่อไปนี้" name="captchaIMG" height="96" id="captchaIMG"><br><input name="reload" type="button" id="reload" value="Reload คำถาม" data-icon="refresh">
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
    <form action="login.scr.php" method="post" name="pinForm" id="pinForm" data-ajax="false">
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
<!-- InstanceEnd --></html>
