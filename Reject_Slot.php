<?php session_start(); 
if(isset($_SESSION['username']))
{
include("mysql_connect.inc.php");
?>
<html>
	<head>
		<style>
			
			input{visibility:hidden;}
		</style>
		<?php
			// posted data.
			
			$oriSchID = $_POST['hd_SchId'];
			$newSchID = $_POST['hd_NewSchId'];
			$requestType = $_POST['hd_RequestType'];
			$AirlineIATA=$_SESSION['AIRLINE_IATA_CD'];
			// k+
			$userType = $_SESSION['user_type'];
			
	
		?>
	</head>
	<body>
		<div>
			<h2>
				Reject Departure Slot
			</h2>
			
			Updating...
		</div>
		
		<?php
		if($requestType == 'EARLIER'){
		
			?>
		
		<form action="Request_Earlier_Slot.php" id="fm_RequestExchangeEarlier" method="post" name="fm_RequestExchangeEarlier">
            <input type="hidden" name="hd_SchId" value="<?php echo $oriSchID ?>">
            <input type="submit" value="Request Earlier Slot" name="nm_Request">
        </form>
        <?php
		}else{
			?>
					<form action="Request_Later_Slot.php" id="fm_RequestExchangeLater" method="post" name="fm_RequestExchangeLater">
            <input type="hidden" name="hd_SchId" value="<?php echo $oriSchID ?>">
            <input type="submit" value="Request Later Slot" name="nm_Request" >
        </form>
			<?php
		}
             ?>           
		<?php
		
				// check data exist 
				$sql = "SELECT * FROM $schema.tb_exchange_slot_log where ORI_SCH_ID = ".$oriSchID;
				// echo $sql;
				$sqlResult = $conn->query($sql);
			if ($sqlResult->num_rows > 0) {
				// update log data
					$sql = "UPDATE $schema.tb_exchange_slot_log set CUR_ASSIGN_SCH_ID = ".$newSchID." WHERE ORI_SCH_ID = ".$oriSchID;
						// echo $sql;
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
			}else{
				// insert log data
				$logStatus = '';
				if($requestType == 'EARLIER'){
					$logStatus = 'Exchanging Earlier Slot';
				}else{
					$logStatus = 'Exchanging Later Slot';
				}
				
				$sql = "INSERT INTO $schema.tb_exchange_slot_log( `LOG_ID`, `ORI_SCH_ID`, `CUR_ASSIGN_SCH_ID`, `REQ_DATE`, `STATUS`, `IS_APPROVED` ) 
				VALUES (0, ".$oriSchID.", ".$newSchID.",CURDATE(), '".$logStatus."', FALSE)";
				
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
			}
			
				if($isSuccess){
					echo '<script type="text/javascript">';
					if($requestType == 'EARLIER'){
						echo "document.getElementById('fm_RequestExchangeEarlier').submit();";
					}else{
						echo "document.getElementById('fm_RequestExchangeLater').submit();";
					}
					
		    		echo "</script>";
				}
		?>
	
		
	</body>
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
