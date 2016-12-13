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

			echo $_SESSION['username'];
			$AirlineIATA=$_SESSION['AIRLINE_IATA_CD'];
			// k+
			$userType = $_SESSION['user_type'];
		?>
	</head>
	<body>
		<a href="Depart_Slot.php">Departure Slot</a>
		<a href="logout.php">Logout</a>

		<div>
			    <h2>Request Later Departure slot</h2>
			    <h3>Selected Departure Slot</h3>
			   <table>
			    	<head>
				        <tr>
				            <th>Date</th>
				            <th>Time</th>
				            <th>Flight</th>
				            <th>Destination</th>
				            <th>Gate</th>
				            <th>Airline</th>
				            <th>Status</th>
				            <th>Submit By</th>
				        </tr>
			    	</head>
			    	<tbody>
	<?php 
		// neccessary to have req_info
		$sql = "SELECT  ss.SCH_DATE, ss.SCH_TIME, ri.FLIGHT_ID , ri.STATUS AS STATUS, Airport.AIRPORT_NAME, Airport.LOCATION,  
                                AirlineInfo.AIRLINE_NAME  , ss.GATE_NO,
								UserInfo.USER_NAME, ri.REQ_ID
                FROM $schema.tb_slot_sch ss 
				INNER JOIN $schema.tb_req_info ri  on  ss.req_id = ri.req_id 
				LEFT OUTER JOIN
								$schema.tb_airline_info as AirlineInfo 
							ON
								ri.AIRLINE_IATA_CD=AirlineInfo.AIRLINE_IATA_CD
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ri.IATA_CD=Airport.IATA_CD
							LEFT OUTER JOIN 
								$schema.tb_user_info as UserInfo
							ON
								UserInfo.USER_ID=ri.USER_ID
			
				WHERE ss.SCH_ID = '$schID'";
				// echo $sql;
		$sqlResult = $conn->query($sql);
		if ($sqlResult->num_rows > 0) {
			
				{	
			
				// INSERT
				$sql = "SELECT * FROM $schema.tb_exchange_slot_log WHERE ORI_SCH_ID =$schID LIMIT 1";
				// echo $sql;
				$sqlLogResult = $conn->query($sql);
				if ($sqlLogResult->num_rows <= 0) {
					$insertSQL = "INSERT INTO $schema.tb_exchange_slot_log(`LOG_ID`, `ORI_SCH_ID`, `CUR_ASSIGN_SCH_ID`, `REQ_DATE`, `STATUS`, `IS_APPROVED`) 
					VALUES (0, ".$schID.", ".$schID.", CURDATE(), 'Exchanging Later Slot', 0)";
					//echo $insertSQL;
					if ($conn->query($insertSQL) === TRUE) {
					    $isRequestingSlot = TRUE;
					}else{
					    echo "Error updating record: " . $conn->error;
					}
				}else{
					$updateSQL = "UPDATE  $schema.tb_exchange_slot_log SET STATUS = 'Exchanging Earlier Slot' WHERE ORI_SCH_ID = ".$schID;
					//echo $insertSQL;
					if ($conn->query($updateSQL) === TRUE) {
					    $isRequestingSlot = TRUE;
					}else{
					    echo "Error updating record: " . $conn->error;
					}	
				}
					
		
			}
			
			while($row = $sqlResult->fetch_assoc()) {
				// mark current date, time
				$currentDate = $row['SCH_DATE'];
				$currentTime = $row['SCH_TIME'];
				$reqID = $row['REQ_ID'];
				
				//update current slot record
				// $sql = "UPDATE $schema.tb_req_info SET STATUS ='Exchanging' WHERE req_id=".$row['REQ_ID'].";";
				// if ($conn->query($sql) === TRUE) {
				//     $isRequestingSlot = TRUE;
				// }else{
				//     echo "Error updating record: " . $conn->error;
				// }
				// $sql = "SELECT * FROM $schema.tb_exchange_slot_log WHERE ORI_SCH_ID = ".$schID." LIMIT 1";
				// $sqlResult = $conn->query($sql);
				// if ($sqlResult->num_rows == 0) {
				// 	$sql = "INSERT INTO $schema.tb_exchange_slot_log(`LOG_ID`, `ORI_SCH_ID`, `CUR_ASSIGN_SCH_ID`, `REQ_DATE`, `STATUS`, `IS_APPROVED`) 
				// 	VALUES (0, ".$schID.", ".$schID.", CURDATE(), 'Exchanging  Slot', FALSE)";
					
				// 	if ($conn->query($sql) === TRUE) {
				// 	    $isRequestingSlot = TRUE;
				// 	}else{
				// 	    echo "Error updating record: " . $conn->error;
				// 	}
				// }
			
 				echo "<tr>";
	            echo "<td>".$row['SCH_DATE']."</td>";
	            echo "<td>".$row['SCH_TIME']."</td>";
	            echo  "<td>".$row['FLIGHT_ID']."</td>";
	            echo  "<td>".$row['LOCATION']."</td>";
	            echo  "<td>".$row['GATE_NO']."</td>";
	            echo  "<td>".$row['AIRLINE_NAME']."</td>";
	            echo  "<td>".$row['STATUS']."</td>";
	            echo  "<td>".$row['USER_NAME']."</td>";
 				echo "</tr>";
    		}
		}
	?>
	
	        
		       
		</tbody>
	    </table>
	    
	    
	    <!-- select available slot -->
	    <h3>Available Departure Slot</h3>
	    <table>
	    		<head>
				        <tr>
				            <th>Date</th>
				            <th>Time</th>
				            <th>Gate</th>
				        </tr>
			    	</head>
			    	<tbody>
			    		
	    <?php 
				$sql = "SELECT * FROM $schema.tb_exchange_slot_log WHERE ORI_SCH_ID = ".$schID;
				$sqlResult = $conn->query($sql);
				if ($sqlResult->num_rows > 0) {
					while($row = $sqlResult->fetch_assoc()) {
						$currentSchID = $row['CUR_ASSIGN_SCH_ID'];
							$sql = "SELECT ss.SCH_ID, ss.SCH_DATE, ss.SCH_TIME, ss.GATE_NO
						FROM airport.tb_slot_sch ss where SCH_ID = ".$currentSchID;
						$sqlResult = $conn->query($sql);
						if ($sqlResult->num_rows > 0) {
							while($row = $sqlResult->fetch_assoc()) {
								$newDate = $row['SCH_DATE'];
								$newTime = $row['SCH_TIME'];
								
								$newDateTimeCond = " AND CONCAT(sub_ss.sch_date,' ', sub_ss.sch_time ) > '".$newDate." ".$newTime."'";
							}
						}
						
					}
				}else{
					$currentSchID = 0;
					$newDateTimeCond= "";
				}
				
			//	echo $currentSchID;
	   // 	$sql = "SELECT * FROM $schema.tb_slot_sch ss 
				// LEFT OUTER JOIN $schema.tb_req_info ri 
				// 	on  ss.req_id = ri.req_id 
				// WHERE ss.SCH_ID in (select top 1 SCH_ID FROM $schema.tb_slot_sch   sub_ss
				// LEFT OUTER JOIN $schema.tb_req_info sub_ri 
				// 	on  sub_ss.req_id = sub_ri.req_id 
				// 	where sub_ss.sch_date <= ".$currentDate." and sub_ss.sch_time <= ".$currentTime."
				// 	 order by sub_ss.sch_date desc, sub_ss.sch_time desc)";
				$sql = "SELECT ss.SCH_ID, ss.SCH_DATE, ss.SCH_TIME, ri.FLIGHT_ID , ri.STATUS AS STATUS, Airport.AIRPORT_NAME, Airport.LOCATION,  
                                AirlineInfo.AIRLINE_NAME  , ss.GATE_NO,
								UserInfo.USER_NAME, ri.REQ_ID
						FROM airport.tb_slot_sch ss
						LEFT OUTER JOIN
						  airport.tb_req_info ri
						ON
						  ss.req_id = ri.req_id
						  LEFT OUTER JOIN $schema.tb_exchange_slot_log as slotLog on 
							slotLog.ORI_SCH_ID = ".$schID."
						  LEFT OUTER JOIN
								$schema.tb_airline_info as AirlineInfo 
							ON
								ri.AIRLINE_IATA_CD=AirlineInfo.AIRLINE_IATA_CD
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ri.IATA_CD=Airport.IATA_CD
							LEFT OUTER JOIN 
								$schema.tb_user_info as UserInfo
							ON
								UserInfo.USER_ID=ri.USER_ID
						INNER JOIN
						  (
						  SELECT
						    SCH_ID
						  FROM
						    airport.tb_slot_sch sub_ss
						  LEFT OUTER JOIN
						    airport.tb_req_info sub_ri
						  ON
						    sub_ss.req_id = sub_ri.req_id
						  WHERE
						    CONCAT(sub_ss.sch_date,' ', sub_ss.sch_time )> '".$currentDate." ".$currentTime."' 
						    and sub_ss.sch_id <> ".$schID." AND sub_ss.req_id IS null AND sub_ss.sch_id <> ".$currentSchID.$newDateTimeCond."
						  ORDER BY
						    CONCAT(sub_ss.sch_date,' ', sub_ss.sch_time )
						LIMIT 1
						) SUBQ
						ON
						  SUBQ.SCH_ID = ss.SCH_ID";
						  
						  //and sub_ss.sch_id <> slotLog.ORI_SCH_ID
						  //echo $sql;
			$sqlResult = $conn->query($sql);
			if ($sqlResult->num_rows > 0) {
				while($row = $sqlResult->fetch_assoc()) {
					$newSchID = $row['SCH_ID'];
					$hasRecord = TRUE;
					
				echo "<tr>";
	            echo "<td>".$row['SCH_DATE']."</td>";
	            echo "<td>".$row['SCH_TIME']."</td>";
	            echo  "<td>".$row['GATE_NO']."</td>";
 				echo "</tr>";
				}
			}else{
				echo "<tr>";
				echo "<td colspan='8'>Not available to exchange</td>";
				echo "</tr>";
				
				if($reqID>0){
					// update status
					// $sql = "UPDATE $schema.tb_req_info SET STATUS ='' WHERE req_id=".$reqID;
					// if ($conn->query($sql) === TRUE) {
					// }else{
					//     echo "Error updating record: " . $conn->error;
					// }
				}
				
			}
		?>
		</tbody>
	    </table>
	    
	    <br/>
	    <br/>
	   	<?php 
	   		if($hasRecord){
	   			?>
	   				 <form action="Accept_Slot.php" id="fm_accept" method="post" name="fm_accept">
                <input type="hidden" name="hd_SchId" value="<?php echo $schID ?>">
                <input type="hidden" name="hd_NewSchId" value="<?php echo $newSchID ?>">
                <input type="hidden" name="hd_reqID" value="<?php echo $reqID ?>">
                <input type="hidden" name="hd_RequestType" value="<?php echo 'LATER' ?>">
                <input type="submit" value="Accept" name="btnAccept">
            </form>
             <form action="Reject_Slot.php" id="fm_reject" method="post" name="fm_reject">
                <input type="hidden" name="hd_SchId" value="<?php echo $schID ?>">
                <input type="hidden" name="hd_NewSchId" value="<?php echo $newSchID ?>">
                <input type="hidden" name="hd_RequestType" value="<?php echo 'LATER' ?>">
                <input type="submit" value="Reject" name="btnReject">
            </form>
        <?php
	   		}
	   	?>
	</div>


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
