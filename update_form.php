<?php
session_start();  
?>
<!DOCTYPE html>
<html>
<head>
<title>Update</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="main.css" media="all" />
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
</head>
<body>
<?php
	include("mysql_connect.inc.php");
	$userid 	= $_SESSION['USER_ID'];
	$UserInfoSQL = "SELECT
						TEL_NO
					FROM 
						$schema.tb_user_info 
					WHERE 
						USER_ID = $userid";
	
	$SQLResult = $conn->query($UserInfoSQL);
	$row = $SQLResult->fetch_assoc();
	
?>

    
<?php
    if(isset($_SESSION['username'])){ 
?>
        <?php include("header.php");?>
        <div class="container">
            <div  class="form">
                <form name="form" method="post" action="update_acc.php" >
                    <div class="imgcontainer"></div>
                        <div class="container">
                            <label><b>User ID</b></label>
                            <input type="text" value="<?php echo $_SESSION['USER_ID']?>" name="userid" disabled>
                            <label><b>Username</b></label>
                            <input type="text" value="<?php echo $_SESSION['username']?>" name="username" disabled>
                            <label><b>Old Password</b></label>
                            <input type="password" placeholder="Enter Old Password" name="opassword" required>
                            <label><b>New Password</b></label>
                            <input type="password" placeholder="Enter New Password" name="npassword" required>
                            <label><b>Confirm Password</b></label>
                            <input type="password" placeholder="Confirm New Password" name="cpassword" required>
                            <label><b>Telephone</b></label>
                            <input type="text" value="<?php echo $row["TEL_NO"];?>" name="telephone" disabled>
                            <label><b>User Type: <?php echo $_SESSION['USER_TYPE']?></b></label><br>
                        <button type="submit" name="nm_submit" value="Submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
<?php
        }else{ 
	    echo 'Please login first.';
        echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
        }
?>

</body>
</html>



