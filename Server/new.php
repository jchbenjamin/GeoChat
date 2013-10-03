<?php
echo "<?xml version=\"1.0\"?>\n";
echo "<reply>\n";

/*this is where we are giong to pull variables formerly in SESION from GET request*/

$conn = pg_connect("host=dbhost-pgsql.cs.missouri.edu dbname=cs3380sp13grp8 user=cs3380sp13grp8 password=Group8!") or die("Could not connect: ".pg_last_error());

if($_GET['mode'] == 'update' or $_GET['mode'] == 'message')
{
//authenticate
	$user = htmlspecialchars($_GET['user']);
	$pass = htmlspecialchars($_GET['pass']);


                if(!$user || !$pass)
                        die('Invalid Data');

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
                //salt and hash
                $salty = trim($info['salt']);
                $passHash = sha1($salty.$pass);
                //make sure password is correct
                if($passHash != $dbpasshash)
                {
			die("wrong user/pass!");
                }
	
//update lat and lon
if(isset($_GET['lat']) and isset($_GET['lon'])) {
		        $lat = htmlspecialchars($_GET['lat']);
                        $lon = htmlspecialchars($_GET['lon']);

                        $query = "UPDATE GeoChat.Users SET location = ST_SetSRID((ST_MakePoint($2, $1)), 4326) WHERE username=$3";
                        $result = pg_prepare($conn, "update", $query);
                        if(!$result) die("Error prepare".pg_last_error());
                        $result = pg_execute($conn, "update", array($lat, $lon, $user));
                        if(!$result) die("Error execute".pg_last_error());
}

//if mode is message then send message
if($_GET['mode'] == message) {

                        $words = $_GET['mesg'];
                        $rad = $_GET['rad'];
                        if($rad > 100 || $rad < 1)
                        {
                                die( "<br/>Please enter a valid radius.");

                        }
                        
                		$query="SELECT * FROM geochat.users WHERE username=$1;";
                		$statement= pg_prepare($conn, "userid", $query);   
                        $res=pg_execute($conn, "userid", array($user));
                        if(!$res) die("SO many errors");
                        $row=pg_fetch_assoc($res);
                        $userid=$row['userid'];
                        
                        //ADD LOCATION
                        $query = "INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ($1, $2, (SELECT location FROM GeoChat.Users WHERE username=$3), $4, $5)";
                        $result = pg_prepare($conn, "message", $query);
                        if(!$result) die("Error prepare".pg_last_error());
                        $result = pg_execute($conn, "message", array($userid, $words, $user, $rad, date(DATE_RSS)));
                        if(!$result) die("Error execute");

}

//end authenticate
	//query and related code to get the user info for the user that logged in.
                $query = "SELECT username, userid, ST_AsLatLonText(location, 'D.DDDDD degrees C') as loc FROM GeoChat.Users
                           WHERE username = $1";
                $result = pg_prepare($conn, "user", $query);
                if(!$result)
                        die("error prepare1");
                $result = pg_execute($conn, "user", array($user));
                if(!$result)
                        die("error execute1");
                $row = pg_fetch_assoc($result);
				
				$userId = $row['userid'];

        //format users messages to xml	

	$amQuery = "SELECT u.username, m.message, m.time, ST_AsLatLonText(m.location, 'D.DDDDD degrees C') as loc, m.radius 
		    FROM GeoChat.Messages AS m
			INNER JOIN GeoChat.Users AS u
			ON (m.senderid = u.userid)
		    WHERE ((round(CAST((ST_Distance_Sphere(m.location, (SELECT location FROM GeoChat.Users WHERE username=$1))) As numeric),2))) < m.radius";
	$result2 = pg_prepare($conn, "mess", $amQuery);
	if(!$result2) die("Error prepare".pg_last_error());
	$result2 = pg_execute($conn, "mess", array($user));
	if(!$result2) die("Error execute");
	to_xml_message($result2);

}
//leave this here but delete later when ready for android client
else
	die("must set mode");


function to_xml_message($r)
{
	$row = pg_fetch_assoc($r);
	if(!$row)
	{	
		echo "no results";
		return FALSE;
	}

        echo "<message>";
	echo "<id>".$row['messageId']."</id>\n";
	echo "<username>".$row['username']."</username>\n";
        echo "<content>".$row['message']."</content>\n";
        echo "<time>".$row['time']."</time>\n";
        echo "<loc>".$row['loc']."</loc>\n";
        echo "<radius>".$row['radius']."</radius>\n";
        echo "</message>";

        while($row = pg_fetch_assoc($r))
        {
                echo "<message>";
		echo "<id>".$row['messageId']."</id>\n";
                echo "<username>".$row['username']."</username>\n";
                echo "<content>".$row['message']."</content>\n";
		echo "<time>".$row['time']."</time>\n";
		echo "<loc>".$row['loc']."</loc>\n";
		echo "<radius>".$row['radius']."</radius>\n";
                echo "</message>";
        }
}

?>
</reply>
