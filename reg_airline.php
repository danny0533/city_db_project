<?php
session_start();

include("mysql_connect.inc.php");
?>
<!DOCTYPE html>
<html>
<head>
    <style>
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
		th, td {
			padding: 5px;
			text-align: left;
		}
	</style>
<title>Registration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="main.css" media="all" />
</head>
<body>
<?php
    if($_SESSION['USER_TYPE'] != 'RC' ){
	echo 'Please contact Ramp Contral!';
	echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
	?>
<?php
}
else{
?>
    <?php include("header.php");
?>
        <div class="container">
                <form name="form" method="post" action="reg_airline_finish.php" >
                    <div class="imgcontainer"></div>
                        <div class="container">
                            <label><b>Airline Name</b></label>
                            <input type="text" placeholder="Enter Airline Name" name="airline" required>
                            <label><b>Airline IATA CD</b></label>
                            <input type="text" maxlength="3" placeholder="Enter Airline IATA CD" name="iata" required>
                            <label><b>Telephone</b></label>
                            <input type="text" maxlength="8" placeholder="Telephone" name="telephone" required>
                        <button type="submit" name="nm_submit" value="Submit">Submit</button>
                    </div>
                </form>
        </div>
<?php
}
?>
</body>
</html>
