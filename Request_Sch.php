<?php 
	session_start();  
	
	// Database Connection
	include("mysql_connect.inc.php");

	if(isset($_POST["nm_Request"])){ 
		$PostSchId	= $_POST["hd_SchId"];
?>
<html>
	<body>
		<?php
		$AirlineIATA = $_SESSION['AIRLINE_IATA_CD'];
		
		$SchReqInfoSQL = "	SELECT 
								SCH_ID, GATE_NO, SCH_DATE, SCH_TIME, REQ_ID
							FROM 
								$schema.tb_slot_sch
							WHERE
								SCH_ID=$PostSchId";

		$SchReqInfoSQLResult = $conn->query($SchReqInfoSQL);

		if ($SchReqInfoSQLResult->num_rows > 0) {
			// output data of each row
			while($row = $SchReqInfoSQLResult->fetch_assoc()) {
				$FlightSchDate=$row["SCH_DATE"];
		?>
		<form action="<?php $_PHP_SELF ?>" id="fm_ConfReq" method="POST" name="fm_ConfReq">
			Schedule ID : 	<input type="text" name="tx_SchId" value="<?php echo $row["SCH_ID"]; ?>" readonly><br>
			Schedule Date: 	<input type="text" name="tx_SchDate" value="<?php echo $row["SCH_DATE"]; ?>" disabled><br>
			Schedule Time: 	<input type="text" name="tx_SchTime" value="<?php echo $row["SCH_TIME"]; ?>" disabled><br>
			Gate No: 		<input type="text" name="tx_GateNo" value="<?php echo $row["GATE_NO"]; ?>" disabled><br>
			Location : 		<select name="sl_IATA">
								<?php
								$LocationSQL = "SELECT 
													IATA_CD, AIRPORT_NAME
												FROM 
													$schema.tb_lkp_airport";

								$LocationSQLResult = $conn->query($LocationSQL);

								if ($LocationSQLResult->num_rows > 0) {
									// output data of each row
									while($row = $LocationSQLResult->fetch_assoc()) {
										echo "<option name='nm_IATA' value='".$row["IATA_CD"]."'>".$row["IATA_CD"].",".$row["AIRPORT_NAME"]."</option>";
									}
								}
								?>
							</select>
			Flight : 		<select name="sl_Flight">
								<?php
								$FlightSQL = "	SELECT 
													AIRLINE_IATA_CD , FLIGHT_ID
												FROM 
													$schema.tb_flight_info
												WHERE
													AIRLINE_IATA_CD='$AirlineIATA'
												And FLIGHT_ID NOT IN (	SELECT
																			DISTINCT FLIGHT_ID
																		FROM
																			$schema.tb_req_info
																		WHERE 
																			REQ_ID IN (SELECT 
																							REQ_ID 
																						FROM 
																							$schema.tb_slot_sch
																						WHERE
																							SCH_DATE = '$FlightSchDate'
																						)
																	)";

								$FlightSQLResult = $conn->query($FlightSQL);

								if ($FlightSQLResult->num_rows > 0) {
									// output data of each row
									while($row = $FlightSQLResult->fetch_assoc()) {
										echo "<option name='nm_Fligth' value='".$row["FLIGHT_ID"]."'>".$row["FLIGHT_ID"]."</option>";
									}
								}
								?>
							</select><br>
			<input type="submit" value="Confirm" name="nm_ConfirmReq">
		</form>
		<?php 
			}
		}
		?>
	</body>
</html>
<?php
} elseif(isset($_POST["nm_ConfirmReq"])) {
	// Get Max Request ID
	$MaxReqNoSQL = "SELECT 
						MAX(REQ_ID) as MAX_REQ_ID
					FROM 
						$schema.tb_req_info";
						
	$MaxReqNoSQLResult = $conn->query($MaxReqNoSQL);
	
	if ($MaxReqNoSQLResult->num_rows > 0) {
		while($row = $MaxReqNoSQLResult->fetch_assoc()) {
			$ReqID=$row["MAX_REQ_ID"]+1;
		}
	} else {
		$ReqID=1;
	}
	
	// Insert Rquest Information
	$NowDate=date('Y-m-d');
	$IATACode = $_POST["sl_IATA"];
	$AirlineIATACode = $_SESSION['AIRLINE_IATA_CD'];
	$FlightId = $_POST["sl_Flight"];
	$UserId = $_SESSION['USER_ID'];
	
	$InsertReqSQL = "INSERT INTO $schema.tb_req_info 
						(REQ_ID, USER_ID, AIRLINE_IATA_CD, REQ_DATE, IATA_CD, FLIGHT_ID , TAKEOFF_TIME) 
					VALUES 
						($ReqID, $UserId, '$AirlineIATACode', '$NowDate' ,'$IATACode' ,'$FlightId', NULL)";
						
	if ($conn->query($InsertReqSQL) === TRUE) {
		// Update Schedule Request ID
		$SchIdforUpdate = $_POST["tx_SchId"];

		$UpdateReqSQL = "UPDATE 
							$schema.tb_slot_sch 
						SET 
							REQ_ID=$ReqID 
						WHERE 
							SCH_ID=$SchIdforUpdate";
							
		if ($conn->query($UpdateReqSQL) === TRUE) {
			echo "Request Approval <br>";
?>
			<a href="Depart_Sch.php">Back To Schedule Page</a>
<?php
		} else {
			echo "Error: " . $UpdateReqSQL . "<br>" . $conn->error;
		}
	} else {
		echo "Error: " . $InsertReqSQL . "<br>" . $conn->error;
	}
} else {
	echo "b";
}
?>
<?php
$conn->close();
?>