<!--FINISHED BUT NOT BEING USED. reg.php IS BEING USED.-->
<?php
	session_start();
	ERROR_REPORTING(E_ALL);
	ini_set("display_errors", 1);
	
	
		if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
		$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: $redirect");
	}
?>

<html>
<head>
<title>Registration page</title>
</head>
<body>
<form method = 'POST' action ='registration.php'>
Username: <input type = 'text' name = 'username' /><br/>
Password: <input type = 'password' name = 'password' /><br/>
Confirm password: <input type = 'password' name = 'confirm-password' /><br/>
<input type = 'submit' name = 'submit' value = 'Register' />
</form>

<?php
	if(isset($_POST['submit']))
	{		
		$password_string = $_POST['password'];
		$confirm_password_string = $_POST['confirm-password'];
		 
		$connection = pg_connect("host = dbhost-pgsql.cs.missouri.edu user=cs3380sp13grp8 password = Group8! dbname = cs3380sp13grp8");
		if(!$connection)
		{
			die("Failed to connect to database.");
		}
		
		else
		{
			if($password_string != $confirm_password_string)
			{
				echo "Provided password values do not match. Please re-enter.";
				unset($_POST['password']);
				unset($_POST['confirm-password']);
				unset($password_string);
				unset($confirm_password_string);
			}
		
			else
			{
				$salt = rand();
				$pass_sha1 = sha1($salt.$password_string);
				
				$username = $_POST['username'];
				//$remote_ip = $_SERVER['REMOTE_ADDR'];
			
								
				$query = "INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES ($1, $2, $3)";
                                $result = pg_prepare($connection, "authentication_insert", $query);
                                $result = pg_execute($connection, "authentication_insert", array($username, $pass_sha1, $salt));
	
				if(!$result)
				{
					echo "\nError: invalid user name. Try a different one.\n";
					exit;
				}
				$query = "INSERT INTO GeoChat.Users (userName) VALUES($1)";
 	                        $result = pg_prepare($connection, "username_insert", $query);
                                $result = pg_execute($connection, "username_insert", array($username));
				
				$query = "INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES ($1, $2, $3)";
				$result = pg_prepare($connection, "authentication_insert", $query);
				$result = pg_execute($connection, "authentication_insert", array($username, $pass_sha1, $salt));
				
				/*
				$query = "INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, $3)";
				$result = pg_prepare($connection, "log_query", $query);
				$result = pg_execute($connection, "log_query", array($username, $remote_ip, "Registered to website"));
				
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = $username;*/
				header("Location: http://babbage.cs.missouri.edu/~cs3380sp13grp8/home.php");
			}	
		}	
	}
?>
</body>
</html>

