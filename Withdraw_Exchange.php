<?php session_start(); 
if(isset($_SESSION['username']))
{
include("mysql_connect.inc.php");
?>
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

			table > tbody tr:hover {background-color: #f5f5f5}
		</style>
		<?php
			// posted data.
			$schID = $_POST['hd_SchId'];
			$AirlineIATA=$_SESSION['AIRLINE_IATA_CD'];
			$userType = $_SESSION['user_type'];
		?>
	</head>
	<body>
		<div>Withdrawing Requested Exchange...</div>
	<?php 
		$sql = "SELECT * FROM $schema.tb_slot_sch ss 
				LEFT OUTER JOIN $schema.tb_req_info ri 
					on  ss.req_id = ri.req_id 
				WHERE ss.SCH_ID = '$schID' ";
		$sqlResult = $conn->query($sql);
		if ($sqlResult->num_rows > 0) {
			while($row = $sqlResult->fetch_assoc()) {
				// $sql = "UPDATE $schema.tb_req_info SET STATUS ='' WHERE req_id=".$row['REQ_ID'].";";
				// if ($conn->query($sql) === TRUE) {
				//     $isSuccess = TRUE;
				// }else{
				//     echo "Error updating record: " . $conn->error;
				// }
				
					// update current status in exchange log tb
				$sql = "UPDATE $schema.tb_exchange_slot_log set IS_APPROVED = FALSE , STATUS = '', CUR_ASSIGN_SCH_ID= ".$schID." WHERE ORI_SCH_ID = ".$schID;
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
    		}
		}
		
		if($isSuccess){
			echo '<script type="text/javascript">
        		window.location = "Depart_Slot.php";
    		</script>';
		}
	?>

</html>
                 
 <?php
$conn->close();
}
else
{
    echo 'Please Use Login Page';
    echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
}
?>
