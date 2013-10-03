<html>
<head>
<title>Update description</title>
</head>
<body style="font-family:verdana;background-color:LightBlue">
Create Message

        <form method='POST' action='createMessage.php'>
                <textarea name='description' rows='20' cols='60' style="font-family:verdana"><?php echo $cur_desc;?></textarea><br />
		Radius:<input type='text' name='rad'/><br/>
                <input type='submit' name='submit' value='Submit' />
        </form>
        <a href='home.php'>Click here</a> to return home.

<?php
$conn = pg_connect("host=dbhost-pgsql.cs.missouri.edu dbname=cs3380sp13grp8 user=cs3380sp13grp8 password=Group8!") or die("Could not connect: ".pg_last_error());

session_start();

if($_SESSION['loggedin'] == true)
{
                //if submit button is pressed
                if(isset($_POST['submit']))
                {
                        $words = $_POST['description'];
			$rad = $_POST['rad'];
			if($rad > 100 || $rad < 1)
			{
				die( "<br/>Please enter a valid radius.");
				
			}
			//ADD LOCATION	
			$query = "INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ($1, $2, (SELECT location FROM GeoChat.Users WHERE username=$3), $4, $5)";
                        $result = pg_prepare($conn, "update", $query);
                        if(!$result) die("Error prepare".pg_last_error());
                        $result = pg_execute($conn, "update", array($_SESSION['userId'], $words, $_SESSION['user'], $rad, date(DATE_RSS)));
                        if(!$result) die("Error execute");
			
                        header("location: home.php");
          }
}
else
{
         //if not logged in go to index page
         header("location: index.php");
}

?>
</body>
</html>

