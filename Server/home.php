<html>
<head>
<title>Home Page</title>
</head>
<style rel='stylesheet' type='text/css' media='all'>
table, td, tr {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 3px 5px;
        background-color:White;
}
</style>
<body style="font-family:verdana;background-color:LightBlue">
<?php
SESSION_START();
$conn = pg_connect("host=dbhost-pgsql.cs.missouri.edu dbname=cs3380sp13grp8 user=cs3380sp13grp8 password=Group8!") or die("Could not connect: ".pg_last_error());

if($_SESSION['loggedin']==true)
{
	//query and related code to get the user info for the user that logged in.
                $query = "SELECT username, userid, ST_AsLatLonText(location, 'D.DDDDD degrees C') as loc FROM GeoChat.Users
                           WHERE username = $1";
                $result = pg_prepare($conn, "user", $query);
                if(!$result)
                        die("error prepare1");
                $result = pg_execute($conn, "user", array($_SESSION['user']));
                if(!$result)
                        die("error execute1");
                $row = pg_fetch_assoc($result);
		$_SESSION['userId'] = $row['userid'];
		echo "<br/>User Information<br/><table>";
                echo "<tr><td><strong>Username:</strong></td><td>".$row["username"]."</td></tr>";
                echo "<tr><td><strong>UserId:</strong></td><td>".$row["userid"]."</td></tr>";
		echo "<tr><td><strong>Location:</strong></td><td>".$row["loc"]."</td></tr>";
                echo "</table>";
/*
	$q = "SELECT * FROM GeoChat.Authentication WHERE username =$1";
	$res = pg_prepare($conn, "auth", $q);
	$res = pg_execute($conn, "auth", array($_SESSION['user']));
	$row = pg_fetch_assoc($res);
	echo "<br/>User Information<br/><table>";
                echo "<tr><td><strong>Username:</strong></td><td>".$row["username"]."</td></tr>";
                echo "<tr><td><strong>passwordHash:</strong></td><td>".$row["passwordhash"]."</td></tr>";
                echo "<tr><td><strong>salt:</strong></td><td>".$row["salt"]."</td></tr>";
                echo "</table>";


	*/

	
	echo "<br/>Your Messages<br/>";
        $query = "SELECT message, time, ST_AsLatLonText(location, 'D.DDDDD degrees C') as loc, radius FROM GeoChat.Messages 
                    WHERE senderid=$1 
                    ORDER BY time ASC";
        $result1 = pg_prepare($conn, "dets", $query);
        if(!$result1)  die("error prepare");
        $result1 = pg_execute($conn, "dets", array($_SESSION['userId']));
        if(!$result1) die("error execute");
	to_table($result1);
	/*
	if(!to_table($result1))
	{
		echo "<br/>Thanks for joinging GeoChat!";
		echo "<br/>Please <a href='loc.php'>update your location</a> before creating a message!</br>";
		echo "<br/><a href='logout.php'>Click here</a> to log out.</br>";
                exit;
	}
	else
	{
		to_table($result1);
		echo "<a href='createMessage.php'>Click here</a> to create a message.<br/><br/>";
	}
	*/
	
	echo "<br/>Messages in your Area.<br/>";
	$amQuery = "SELECT u.username, m.message, m.time, ST_AsLatLonText(m.location, 'D.DDDDD degress C') as loc, m.radius 
		    FROM GeoChat.Messages AS m
			INNER JOIN GeoChat.Users AS u
			ON (m.senderid = u.userid)
		    WHERE ((round(CAST((ST_Distance_Sphere(m.location, (SELECT location FROM GeoChat.Users WHERE username=$1))) As numeric),2))/1609.344) < m.radius";
	$result2 = pg_prepare($conn, "mess", $amQuery);
	if(!$result2) die("Error prepare".pg_last_error());
	$result2 = pg_execute($conn, "mess", array($_SESSION['user']));
	if(!$result2) die("Error execute");
	to_table($result2);
	/*
	if(to_table($result2) == FALSE)
	{
		echo "<br/>There are no messages in your area at this time.</br>
			You can update your location <a href='loc.php'>HERE</a>.<br/>
			Or create a message <a href='createMessage.php'>HERE</a>.<br/>
			<br/><a href='logout.php'>Click here</a> to log out.</br>";
		exit;
	}
	else
	{
		to_table($result2);
		echo "<a href='loc.php'>Click here</a> to update your location.<br/>";
	}
	*/
}
else
	header("location: index.php");


function to_table($r)
{
	$row = pg_fetch_assoc($r);
	if(!$row)
	{	
		echo "no results";
		return FALSE;
	}

        echo "<table><tr>";
        foreach($row as $key => $value)
		echo "<td><strong>".$key."</strong></td>";
        echo "</tr>";

        echo "<tr>";
        foreach($row as $res)
                echo "<td>".$res."</td>";
        echo "</tr>";

        while($row = pg_fetch_assoc($r))
        {
                echo "<tr>";
                foreach($row as $res)
                        echo "<td>".$res."</td>";
                echo "</tr>";
        }
        echo "</table>";


}

?>
<br/>
<a href='createMessage.php'>Click here</a> to create a message<br/>
<a href='loc.php'>Update Location</a><br/>
<a href='logout.php'>Click here</a> to log out.<br/>
</body>
</html>
