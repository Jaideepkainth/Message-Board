<html>
<head><title>Login Page</title></head>
<?php
session_start();
if(isset($_SESSION['username']))
{
	header("Location:board.php");
	exit;
}
?>
<body>
<h3>Please Enter Username and Password</h3>
<form action="login.php" method="Get">
<label>Username</label>
<input type="text" name="username">
<label>Password</label>
<input type="password" name="password">
<input type="submit" value="Login">
</form>
</body>
</html>
<?php
if(isset($_GET['username']))
{
	$username=$_GET['username'];
	$password=$_GET['password'];
	$password=md5($password);
	error_reporting(E_ALL);
	ini_set('display_errors','On');
	try 
	{
		$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$dbh->beginTransaction();
		$dbh->commit();
		$stmt = $dbh->prepare("select password from users where username='".$username."'");
		$stmt->execute();
		print "<pre>";
		$row = $stmt->fetch();
		if($row[0]==$password)
		{
			$_SESSION['username']=$username;
			header("Location:board.php");
			exit;
		}
		else
		{
			print("Wrong Username or password");
		}
	}
	catch (PDOException $e) 
	{
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}
?>