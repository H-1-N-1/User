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

/*
	Mysql Ayar Dosyası
*/
require_once 'Config/Db.php';

$do = @$_GET['do'];
Switch($do){

	/*
		Üye Kayıt İşlemi 
		Özellikleri ;
			+ Aynı Kullanıcı Adı ve E-Mail Sahip Kişileri Kaydetmez.
			+ Her Kullanıcıya Özgün Token Oluşturur.
			+ Şifreler MD5 ile şifrelenir.
	*/
	case'Register';
	if($_POST){
		// Formdaki Bilgiler
		$username 		= $_POST['username'];
		$password 		= md5($_POST['password']);
		$user_email     = $_POST['user_email'];
		$user_token     = uniqid($username,true);
		// Aynı Kullanıcı varmı Kontrolü
		$user_kontrol = $db->prepare("Select * from users where username = ? or user_email = ? ");
		$user_kontrol->execute(array($username,$user_email));
		if($user_kontrol->rowCount()){
			echo '<div style="color:green;font-weight:bold;line-height:23px;padding:5px 5px 5px 5px">
			<strong style="color:red;">Hata !</strong><br />
			Bu Bilgiler İle Daha Önce Kayıt Olmuşsunuz.Yönlendiriliyorsunuz.
			</div>';
			header("refresh:2; url=index.php"); 
		}else{
			// Kullanıcıyı Veritabanına Ekle
			$kaydet = $db->prepare("INSERT INTO users set username = ? , password = ? , user_email = ? , user_token = ? ");
			$kaydet->execute(array($username,$password,$user_email,$user_token));
			if($kaydet->rowCount()){
				echo '<div style="color:green;font-weight:bold;line-height:23px;padding:5px 5px 5px 5px">
				<strong style="color:red;">Tebrikler !</strong><br />
				Başarılı Bir Şekilde Kayıt Oldunuz. Yönlendiriliyorsunuz.
				</div>';
				header("refresh:2; url=index.php"); 
			}else{
				echo '<div style="color:green;font-weight:bold;line-height:23px;padding:5px 5px 5px 5px">
				<strong style="color:red;">Hata !</strong><br />
				Lütfen Daha Sonra Tekrar Deneyiniz.Yönlendiriliyorsunuz.
				</div>';
				header("refresh:2; url=index.php"); 
			}
		}
	}else{
		header("Location:index.php");
	}
	break;

	/*
		Üye Giriş Alanı
		Üye Bilgileri SESSİONDA tutulur.
		Beni Hatırla Sistemi
	*/
	case'Login';
	if($_POST){
		// Formdan gelen bilgiler
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		$hatirla  = @$_POST['hatirla'];
		// Üye Giriş İşlemi
		$login = $db->prepare("Select * from users where username = ? and password = ? ");
		$login->execute(array($username,$password));
		if($login->rowCount()){
			// Bilgileri Doğru ise Sesisona ata ve giriş yap
			$row = $login->fetch(PDO::FETCH_ASSOC);
			$_SESSION['oturum']		= TRUE;
			$_SESSION['username']   = $username;
			$_SESSION['password']   = $password;
			$_SESSION['email']		= $row['user_email'];
			$_SESSION['token']		= $row['user_token'];
			// Beni Hatırla Seçeneği
			if($hatirla == "hatirla"){
				setcookie("username",$_SESSION['username'],time()+(60*60*24));
				setcookie("password",$_SESSION['password'],time()+(60*60*24));
			}
			header("Location:index.php");

		}else{
			header("Location:index.php");
		}
	}else{
		header("Location:index.php");
	}
	break;

	/*
		Üye Çıkış İşlemleri
	*/
	case'Logout';
	if(isset($_SESSION['oturum'])){
		session_start();
		session_destroy();
		header("Location:index.php");
		setcookie("username",$_SESSION['username'],time()-3600);
		setcookie("password",$_SESSION['password'],time()-3600);
	}else{
		header("Location:index.php");
	}
	break;

	/*
		Üye Profil Sayfası
	*/
	case'Profil';
	if($_SESSION['oturum']){
	echo '<a href="index.php" style="padding:10px 10px 10px 10px">Anasayfa</a>';
		echo '<table class="panel">
			<tr>
				<td>Username</td>
				<td>:</td>
				<td>'.$_SESSION['username'].'</td>
			</tr>
			<tr>
				<td>E-Mail</td>
				<td>:</td>
				<td>'.$_SESSION['email'].'</td>
			</tr>
			<tr>
				<td>İP</td>
				<td>:</td>
				<td>'.$_SERVER['REMOTE_ADDR'].'</td>
			</tr>
		</table>';

	}else{
		header("Location:index.php");
	}
	break;

	/*
		Üye Şifre Değiştirme Alanı
		Özellikleri ;
			+ Token ile Şifreniz Güvenle Değiştirilir
			+ Dışarıdan Müdahale Şansı yoktur.
	*/
	case'Password';
	if(isset($_SESSION['oturum'])){
		echo '<a href="index.php" style="padding:10px 10px 10px 10px">Anasayfa</a>';
		// Gelen Token Değeri
		$token = $_GET['token'];
		echo'<form action="" method="post">
		<h3 style="color:red;padding:10px 10px 10px 10px">'.$_SESSION['username'].' - Şifre Değiştir</h3> 
		<table class="panel">
			<tr>
				<td>Yeni Şifreniz</td>
				<td>:</td>
				<td><input type="password" style="width:300px;height:25px" name="password" placeholder="Yeni Şifrenizi Giriniz..." /></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><input type="submit" value="Şifreyi Güncelle" /></td>
			</tr>
		</table>
		</form>';
		// Eşlesen Token değeri ile Şifreyi Değiştir.
		if($_POST){
			$password = md5($_POST['password']);
			$update = $db->exec("UPDATE users set password = '$password' where user_token = '$token' ");
			if($update){
				echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">
				<strong>Tebrikler !</strong> <br />
				Şifreniz Güncellendi. Yönlendiriliyorsunuz . . . 
				</div>';
				header("Refresh:2;url=index.php");
			}else{
				echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">
				Hata ! <br />
				Lütfen Daha Sonra Tekrar Deneyiniz. Yönlendiriliyorsunuz . . . 
				</div>';
				header("Refresh:2;url=index.php");
			}
		}

	}else{
		header("Location:index.php");
	}
	break;

	/*
		Şifremi Unuttum Kısmı
		Mail Adresine Link Gönderme İşlemi
	*/
	case'Forget-Mail';
	if($_POST){
		// Gelen Mail Adresi
		$user_email = $_POST['user_email'];
		// Mail Adresi ile Token Eşleştirme - Varmı Yokmu
		$bul = $db->query('SELECT * FROM users WHERE user_email="'.$user_email.'" ');
		$bul = $bul->fetch(PDO::FETCH_ASSOC);
		if($bul){
			// Token Adresi
			$token =  $bul['user_token'];
			// Şifre Sıfırlama Link Mail Gönderme ;
			include 'Mail/class.phpmailer.php';
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;
			$mail->SMTPSecure = 'tls';
			$mail->Username = 'xxx@gmail.com'; // G-Mail Adresi
			$mail->Password = 'Gmail Şifreniz';  // G-Mail Şifresi
			$mail->SetFrom($mail->Username, 'Abudikbudik.Com'); // Sitenizin Adı
			$mail->AddAddress($user_email);
			$mail->CharSet = 'UTF-8';
			$mail->Subject = 'Şifre Sıfırlama'; // Mail Başlığı
			$content = '<div style="background: #eee; padding: 10px; font-size: 14px">
			Merhabalar ! <br />
			Abudikbudik.Com sitesinin size özel şifre sıfırlama linki aşağıdadır.Aşağıda ki linke tıklayarak şifrenizi sıfırlayabilirsiniz.<br />
			<a href="http://localhost/User/Users.php?do=Forget&token='.$token.'">Şifrenizi Sıfırlayın</a>
			</div>';
			$mail->MsgHTML($content);
			if($mail->Send()) {
				// e-posta başarılı ile gönderildi
				echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">E-posta başarıyla gönderildi, lütfen kontrol edin.</div>';
				header("Refresh:2;url=index.php");
			} else {
				// bir sorun var, sorunu ekrana bastıralım
				echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">'.$mail->ErrorInfo.'</div>';
				header("Refresh:1;url=index.php");
			}

		}else{
			echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">
			Hata ! <br />
			Bu E-Mail Adresi Sisteme Kayıtlı Değildir. Yönlendiriliyorsunuz . . . 
			</div>';
			header("Refresh:1;url=index.php");
		}

	}else{
		header("Location:index.php");
	}
	break;

	/*
		Şifre Sıfırlama İşlemi
		Gelen Linkden Şifreyi Sıfırla
	*/
	case'Forget';
	if(isset($_GET['token']) == "token"){
		// Gelen Token Linki
		$token = $_GET['token'];
		echo'<form action="" method="post">
		<table class="panel">
			<tr>
				<td>Yeni Şifreniz</td>
				<td>:</td>
				<td><input type="password" style="width:300px;height:25px" name="password" placeholder="Yeni Şifrenizi Giriniz..." /></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><input type="submit" value="Şifreyi Güncelle" /></td>
			</tr>
		</table>
		</form>';
		
		// Eşlesen Token değeri ile Şifreyi Değiştir.
		if($_POST){
			$password = md5($_POST['password']);
			$update = $db->exec("UPDATE users set password = '$password' where user_token = '$token' ");
			if($update){
				echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">
				<strong>Tebrikler !</strong> <br />
				Şifreniz Güncellendi. Yönlendiriliyorsunuz . . . 
				</div>';
				header("Refresh:2;url=index.php");
			}else{
				echo '<div style="padding:10px 10px 10px 10px;border-top:1px solid #ddd;line-height:23px">
				Hata ! <br />
				Lütfen Daha Sonra Tekrar Deneyiniz. Yönlendiriliyorsunuz . . . 
				</div>';
				header("Refresh:2;url=index.php");
			}
		}
	}else{
			header("Location:index.php");
	}
	break;

}

?>
</div>
</body>
</html>