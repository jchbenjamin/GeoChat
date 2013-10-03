
<html>
<head>
<title>Log in</title>
</head>
<body style="font-family:verdana;background-color:LightBlue">
        <form action='index.php' method='POST'><table>
                <tr><td><label for='username'>Username: </label></td>
                <td><input type='text' name='username' /> <br /></td></tr>
                <tr><td><label for='password'>Password: </label></td>
                <td><input type='password' name='password' /> <br /></td></tr></table>
                <input type='submit' value='Submit' name='submit'>
        </form>
<a href='reg.php'>Click Here</a> to register.
<?php
session_start();
$conn = pg_connect("host=dbhost-pgsql.cs.missouri.edu dbname=cs3380sp13grp8 user=cs3380sp13grp8 password=Group8!") or die("Could not connect:".pg_last_error());
$_SESSION['loggedin'];
if(!$conn)
        die('unable to connect to database');


        if(isset($_POST['submit']))
        {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                if(!$user || !$pass)
                        die('<br/>Invalid Data');

                //protect from injection
                $q = 'SELECT * FROM GeoChat.Authentication 
                        WHERE username = $1';
                //prepare
                $result = pg_prepare($conn, 'query', $q);
                //check for error
                if(!$result)
                        die('<br/>Error pg_prepare');
                //execute
                $result = pg_execute($conn, 'query', array($user));
                //check for error
                if(!$result)
                        die('<br/>Error pg_execute');

                $info = pg_fetch_assoc($result);
                if($info == NULL)
                        die('<br/>1Incorrect username/password');
		
		$dbpasshash = $info['passwordhash'];
		echo '<br/>MAD'.$dbpasshash;
                //salt and hash
                $salty = trim($info['salt']);
                $passHash = sha1($salty.$pass);
		echo '<br/>MAD'.$salty.'<br/>MAD'.$passHash;
                //make sure password is correct
                if($passHash == $dbpasshash)
                {
                        //define session variables
                        $_SESSION['user'] = $user;
                        $_SESSION['loggedin'] = true;
                        //go to homepage
			//NEED TO UPDATE LOCATION
                        header('location: home.php');
                }
                else
                        die('<br/>2Incorrect username/password');

        }

        //if already logged in go to home page
        if($_SESSION['loggedin'] == true)
                header('location: home.php');


?>
</body>
</html>

