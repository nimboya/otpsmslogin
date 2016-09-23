<?php
session_start();
include_once("conn.php");
if(isset($_POST['login']) && $_POST['login'] == "yes") {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	$sql = "SELECT username, password FROM users WHERE username='$username' AND password='$password'";
	$runq = mysql_query($sql) or die(mysql_error());
	$status = mysql_num_rows($runq);

	if($status == 1) {
		// Generate OTP
		$otp = mt_rand(111110,999999);
		$addotp = mysql_query("UPDATE users SET otp='$otp' WHERE username='$username'");
		
		//Send OTP
		$uname = "";
        $password = "";
        $out = file("http://smsc.xwireless.net/API/WebSMS/Http/v3.1/index.php?username=".$uname."&password=".$password."&sender=".$from."&to=".$recs."&message=".urlencode($msg)."&reqid=1&format=json");
		$_SESSION['curu'] = $username;
		header("Location: index.php?w=otp");
	} else {
		header("Location: index.php?l=fail");
	}
}

if(isset($_POST['verifyotp']) && $_POST['verifyotp'] == "yes") {
	$otp = $_POST['verify'];
	$sql = "SELECT username, otp FROM users WHERE otp='$otp' AND username='$_SESSION[curu]'";
	
	$runq = mysql_query($sql) or die(mysql_error());
	$ostatus = mysql_num_rows($runq);

	if($ostatus == 1) {
		header("Location: success.html");
	} else {
		header("Location: failed.html");
	}
}
?>
<?php if(!isset($_GET['w'])) { ?>
<form action="" method="post">
<p>
<h3>Enter Login Details</h3>
<?php if(isset($_GET['l'])) { echo '<h4>Wrong Username/Password</h4>';} ?>
Username: <input type="text" id="username" name="username" /> <br />
Password: <input type="password" id="password" name="password" />
<input type="hidden" name="login" id="login" value="yes" /><br />
<input type="submit" value="Login" name="submit" id="submit" /> 
</p>
</form>
<?php } ?>

<?php if(isset($_GET['w'])) { ?>
<h3>Validate OTP Code</h3>
<form action="" method="post">
<p>
Enter OTP: <input type="text" id="verify" name="verify" />
<input type="hidden" name="verifyotp" value="yes" /><br />
<input type="submit" value="Verify Now" name="veryotp" id="veryotp" /> 
</p>
</form>
<?php	
} 
?>
