<?php 
	require_once 'Config/Db.php';
	// Beni Hatırla Sistemi
	if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
		$_SESSION['username']   = $_COOKIE['username'];
		$_SESSION['password']   = $_COOKIE['password'];
	}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Üye Sistem Tasarımı</title>
	<link rel="stylesheet" href="assets/Css/Style.css" />
	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/Custom.js"></script>
</head>
<body>
<div class="form-wrap">
<?php 

if(isset($_SESSION['oturum'])) { ?>
	
	<!-- Üye Giriş Yapmışsa Gözüükecek Alan -->
	<table class="panel">
		<tr>
			<td>HoşGeldiniz</td>
			<td>:</td>
			<td><?php echo $_SESSION['username']; ?></td>
		</tr>
		<tr>
			<td><a href="Users.php?do=Profil">Kişisel Profilim</a></td>
		</tr>
		<tr>
			<td><a href="Users.php?do=Password&token=<?php echo $_SESSION['token']; ?>">Şifremi Değiştir</a></td>
		</tr>
		<tr>
			<td><a href="Users.php?do=Logout">Çıkış Yap</a></td>
		</tr>
	</table>
	<!--#Üye Giriş Yapmışsa Gözüükecek Alan -->

<?php } else{ ?>

	<!-- Üye Giriş Yapmamışsa Giriş Formu Göster -->	
	<div class="tabs">
		<h3 class="login-tab"><a class="active" href="#login-tab-content">Üye Girişi</a></h3>
		<h3 class="signup-tab"><a  href="#signup-tab-content">Kayıt Ol</a></h3>
	</div>
	<div id="sonu"></div>
	<div class="tabs-content">
		<!-- Üye Girişi -->
		<div id="login-tab-content" class="active">
			<form  id="uLogin" action="Users.php?do=Login" method="post">
				<input type="text" class="input" name="username" id="user_login"  placeholder="Kullanıcı Adınız">
				<input type="password" class="input" name="password" id="user_pass"  placeholder="Kullanıcı Şifreniz">
				<input type="checkbox" name="hatirla" value="hatirla" class="checkbox" id="remember_me">
				<label for="remember_me">Beni Hatırla</label>
				<input type="submit" id="Login" class="button" value="Giriş Yap">
			</form>
			<div class="help-text">
				<p><a href="Forget.php">Şifremi Unuttum ?</a></p>
			</div>
		</div>
		<!--#Üye Girişi -->
		<!-- Üye Formu -->
		<div id="signup-tab-content" >
			<form id="uRegister" action="Users.php?do=Register" method="post">
				<input type="text" class="input" name="username" id="username"   placeholder="Kullanıcı Adınız" />
				<input type="password" class="input" name="password" id="password"   placeholder="Kullanıcı Şifreniz" />
				<input type="text" class="input" name="user_email" id="user_email"  placeholder="E-Mail Adresiniz" />
				<input type="submit" id="Register" class="button" value="Kayıt Ol">
			</form>
		</div>
		<!--#Üye Formu -->
	</div>	
	<!--#Üye Giriş Yapmamışsa Giriş Formu Göster -->

<?php } ?>
</div>
</body>
</html>