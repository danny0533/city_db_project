<?php
if($_SESSION['USER_TYPE'] != 'RC'){ ?>
<table>
    <tr>
		<td><a href="update_form.php"><?php echo $_SESSION['username'];;?></a></td>
        <td><a href="Depart_Slot.php">Depart Slot </a></td>
        <td><a href="Depart_Sch.php">Depart Schedule </a></td>
        <td><a href="logout.php">Logout </a></td>
    </tr>
</table><br>
<?php
}else{
?>
<table>
    <tr>
    	<td><a href="update_form.php"><?php echo $_SESSION['username'];;?></a></td>
        <td><a href="reg_user.php">User Register </a></td>
        <td><a href="update_alluser.php">User Admin </a></td>
        <td><a href="reg_airline.php">Airline Register </a></td>
        <td><a href="update_airline.php">Airline Admin </a></td>
        <td><a href="Depart_Slot.php">Depart Slot </a></td>
        <td><a href="Depart_Sch.php">Depart Schedule </a></td>
        <td><a href="logout.php">Logout </a></td>
    </tr>
</table><br>
<?php
	}
?>

