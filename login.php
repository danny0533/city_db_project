<?php session_start();?>
<html>
<head>
<title>Airport</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="main.css" media="all" />
</head>
<body>
<?php
    if(isset($_SESSION['username'])){
    	?><p><font size="12"><?php echo 'No need to login again';?></font></p><?php
        echo '<meta http-equiv=REFRESH CONTENT=1;url=Depart_Sch.php>';
    }else{ 
?>
    <div class="container" >
        <form name="form" method="post" action="login_process.php">
            <div class="imgcontainer">
            </div>
            <div class="container">
                <label><b>User ID</b></label>
                <input type="text" placeholder="Enter User ID" name="userid" required>
                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" required>
                <button type="submit" name="nm_submit" value="Login">Login</button>
                <button type="button" class="cancelbtn"><a href="index.php">Cancel</a></button>
                
            </div>
        </form>
    </div>
<?php
}
?>
</body>
</html>