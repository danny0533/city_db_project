<?php 
	session_start(); 
	
	if(isset($_SESSION['username'])){
		include("mysql_connect.inc.php");
		include("Depart_Status.php");
				
		// Auto Genarate Schedule After 9 Month
		$MaxDateSQL = "	SELECT 
							MAX(SCH_ID) AS MAX_SCH_ID ,
							MAX(SCH_DATE) as MAX_DATE ,
							DATE_ADD(CURDATE(),INTERVAL 9 month) AS DATE_9_MTH
						FROM 
							$schema.tb_slot_sch";
							
		$MaxDateSQLResult = $conn->query($MaxDateSQL);

		if ($MaxDateSQLResult->num_rows > 0) {
			while($row = $MaxDateSQLResult->fetch_assoc()) {
				$MaxSchId=$row["MAX_SCH_ID"];
				
				if ($row["MAX_DATE"] < $row["DATE_9_MTH"]) {
					
					$DefaultTime = new DateTime;
					$DefaultTime->setTime(0,0,0);
					
					$MaxTime = new DateTime;
					$MaxTime->setTime(23,45,0);
					
					$Date9Mth = $row["DATE_9_MTH"];
					
					while ($DefaultTime <= $MaxTime ) {
						$vrDefaultTime=$DefaultTime->format('H:i:s');
						
						$NEW_SLOT_NO = 1;
						while ($NEW_SLOT_NO <= 5 ) {
							$MaxSchId++;
							
							$SCH_INSERT_SQL = "	INSERT INTO $schema.tb_slot_sch 
													(SCH_ID,GATE_NO,SCH_DATE,SCH_TIME,REQ_ID)
												VALUES 
													( '$MaxSchId' , 
													'$NEW_SLOT_NO' , 
													'$Date9Mth' , 
													'$vrDefaultTime' , 
													NULL )";
													
							if ($conn->query($SCH_INSERT_SQL) === TRUE) {
								echo "";
							} else {
								echo "Error: " . $SCH_INSERT_SQL . "<br>" . $conn->error;
							}
							
							$NEW_SLOT_NO++;
						}
						$DefaultTime->modify('+15 minutes');
					}
				}
			}
		}
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
			
			table > tbody tr:hover{
				 background-color: #f5f5f5
			}
			
			#fm_Reset{
				margin-top:15px;
			}
		</style>

	</head>
	<body>
		<?php
    	include("header.php");
    	$AirlineIATA=$_SESSION['AIRLINE_IATA_CD'];
		?>
		<!-- Table for Search Engine -->
		<table>
			<tr>
				<form action="<?php $_PHP_SELF ?>" id="fm_Gate" method="post" name="fm_Gate">
				<td> Date : </td>
				<td>
					<!-- Search By Date -->
					<select name="sl_Date">
						<option disabled selected value> -- Select an Option -- </option>
						<?php
						$SchDateSQL = "	SELECT 
											DISTINCT SCH_DATE
										FROM 
											$schema.tb_slot_sch";

						$SchDateSQLResult = $conn->query($SchDateSQL);

						if ($SchDateSQLResult->num_rows > 0) {
							// output data of each row
							while($row = $SchDateSQLResult->fetch_assoc()) {
								echo "<option name='nm_Date' value='".$row["SCH_DATE"]."'>".$row["SCH_DATE"]."</option>";
							}
						}
						?>
					</select>
				</td>
				<td> Time : </td>
				<td>
					<!-- Search By Time -->
					<select name="sl_Time">
						<option disabled selected value> -- Select an Option -- </option>
						<?php
						$SchTimeSQL = "	SELECT 
											DISTINCT SCH_TIME
										FROM 
											$schema.tb_slot_sch";

						$SchTimeSQLResult = $conn->query($SchTimeSQL);

						if ($SchTimeSQLResult->num_rows > 0) {
							// output data of each row
							while($row = $SchTimeSQLResult->fetch_assoc()) {
								echo "<option name='nm_Time' value='".$row["SCH_TIME"]."'>".$row["SCH_TIME"]."</option>";
							}
						}
						?>
					</select>
				</td>
				<td> Gate : </td>
				<td> 
					<!-- Search By Gate -->					
					<select name="sl_Gate">
						<option disabled selected value> -- Select an Option -- </option>
						<?php
						$SchGateSQL = "	SELECT 
											DISTINCT GATE_NO
										FROM 
											$schema.tb_slot_sch";

						$SchGateSQLResult = $conn->query($SchGateSQL);

						if ($SchGateSQLResult->num_rows > 0) {
							// output data of each row
							while($row = $SchGateSQLResult->fetch_assoc()) {
								echo "<option name='nm_Gate' value='".$row["GATE_NO"]."'>".$row["GATE_NO"]."</option>";
							}
						}
						?>
					</select>
				</td>
				<td>
					<input type="submit" value="Submit">
				</td>
				</form>
				<td>
					<!-- Reset Button -->
					<form action="<?php $_PHP_SELF ?>" id="fm_Reset" method="post" name="fm_Reset">
						<input type="submit" value="Reset" name="nm_Reset">
					</form>
				</td>
			</tr>
		</table>
		
		<!-- Table for Departure Schedule -->
		<table>
			<caption><h2>Departure Schedule</h2></caption>
			<tr>
				<th>Date</th>
				<th>Time</th>
				<th>Flight</th>
				<th>Destination</th>
				<th>Gate</th>
				<th>Airline</th>
				<th>Status</th>
			</tr>
			
			<?php
			// Set Sub Query for select data
			if(isset($_POST["nm_Reset"])){ 
				$SchDataSubQuery = "WHERE 1=1";
			} else {
				if(isset($_POST["sl_Gate"])){ 
					$postGate=$_POST["sl_Gate"];
					$SchDataSubQuery = "WHERE GATE_NO=$postGate";
				} elseif(isset($_POST["sl_Date"])){
					$postDate=$_POST["sl_Date"];
					$SchDataSubQuery = "WHERE SCH_DATE='$postDate'";
				} elseif(isset($_POST["sl_Time"])){
					$postTime=$_POST["sl_Time"];
					$SchDataSubQuery = "WHERE SCH_TIME='$postTime'";
				} else {
					$SchDataSubQuery = "WHERE 1=1";
				}
			}
			
			$NowDate=date("Y-m-d" , strtotime( '-1 days' ));

			$SchDataSQL = "	SELECT
								SCH_ID, GATE_NO, SCH_DATE, SCH_TIME, SlotSch.REQ_ID, ReqInfo.IATA_CD, 
                                Airport.AIRPORT_NAME, Airport.LOCATION,  
                                AirlineInfo.AIRLINE_NAME , ReqInfo.USER_ID , ReqInfo.FLIGHT_ID , ReqInfo.STATUS
							FROM 
								$schema.tb_slot_sch SlotSch
							LEFT OUTER JOIN
								$schema.tb_req_info as ReqInfo 
							ON
								SlotSch.REQ_ID=ReqInfo.REQ_ID
							LEFT OUTER JOIN
								$schema.tb_airline_info as AirlineInfo 
							ON
								ReqInfo.AIRLINE_IATA_CD=AirlineInfo.AIRLINE_IATA_CD
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ReqInfo.IATA_CD=Airport.IATA_CD
							$SchDataSubQuery 
								And SlotSch.REQ_ID IS NOT NULL
								And SlotSch.SCH_DATE >= '$NowDate' 
							ORDER BY 
								SCH_DATE , SCH_TIME , GATE_NO , SCH_ID";
								
			$SchDataSQLResult = $conn->query($SchDataSQL);

			if ($SchDataSQLResult->num_rows > 0) {
				// output data of each row
				while($row = $SchDataSQLResult->fetch_assoc()) {
					echo "<tr><td>".$row["SCH_DATE"]."</td>";
					echo "    <td>".$row["SCH_TIME"]."</td>";
					echo "    <td>".$row["FLIGHT_ID"]."</td>";
					echo "    <td>".$row["LOCATION"]."</td>";
					echo "    <td>".$row["GATE_NO"]."</td>";
					echo "    <td>".$row["AIRLINE_NAME"]."</td>";
					echo "    <td>".$row["STATUS"]."</td>";
					echo "</tr>";
				}
			}
			?>
		</table>
	</body>
</html>

<?php
$conn->close();
?>
<?php
}
else
{
    echo 'Please Use Login Page';
    echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
}
?>