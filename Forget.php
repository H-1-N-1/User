<?php 
	require_once 'Config/Db.php';
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Şifremi Unuttum ?</title>
	<link rel="stylesheet" href="assets/Css/Style.css" />
	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/Custom.js"></script>
</head>
<body>
<div class="form-wrap">

	<div class="tabs">
		<h3 class="login-tab"><a class="active" href="">Şifremi Gönder</a></h3>
		<h2><a class="" href="index.php">Anasayfa</a></h2>
	</div>

	<div id="sonu"></div>
	<div class="tabs-content">
		<form action="Users.php?do=Forget-Mail" method="post">
			<input type="text" class="input" name="user_email" placeholder="E-Mail Adresiniz . . ." />
			<input type="submit" id="Register" class="button" value="Şifremi Gönder">
		</form>
	</div>

</div>
</body>
</html>