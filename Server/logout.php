<html>
<head>
<title>Logout</title>
</head>
<body style="font-family:verdana;background-color:LightBlue">
<?php

session_start();

if(isset($_POST['logout']))
{
        session_unset();
        session_destroy();
}

if($_SESSION['loggedin'] == true)
{
        echo "";
?>
<form action="logout.php" method="POST">
<input type="submit" value="Logout" name="logout"/>
</form>

<?php
}

else
{
        header("location: index.php");
}
?>
</body>
</html>

