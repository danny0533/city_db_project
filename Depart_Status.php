<?php
session_start(); 
include("mysql_connect.inc.php");
	date_default_timezone_set('Asia/Hong_Kong');
	$NowDate=date("Y-m-d");
	$NowTime=date("H:i:s");
	$OneHrTime=date("H:i:s",strtotime($SchTime.'+1 hours'));
	$MinTime=date("H:i:s",strtotime($SchTime.'+30 minutes'));

	$UpdateStatusSQL = "SELECT
							req.REQ_ID , TAKEOFF_TIME ,
							CASE WHEN '$NowTime' >= TAKEOFF_TIME THEN CONCAT('Dep :' , DATE_FORMAT(TAKEOFF_TIME,'%H:%i'))
								 WHEN '$MinTime' >= TAKEOFF_TIME And '$NowTime' < TAKEOFF_TIME THEN 'Gate Closed'
								 WHEN '$OneHrTime' >= TAKEOFF_TIME And '$NowTime' < TAKEOFF_TIME And '$MinTime' < TAKEOFF_TIME THEN 'Broading'
								 ELSE ''
							END AS UPDATE_STATUS
						FROM
							$schema.tb_req_info req
						WHERE 
							req.STATUS <> 'Canceled' And
							req.REQ_ID IN (SELECT 
												REQ_ID
											FROM 
												$schema.tb_slot_sch
											WHERE
												REQ_ID IS NOT NULL
											And SCH_DATE = '$NowDate')";
								
	$UpdateStatusSQLResult = $conn->query($UpdateStatusSQL);
	
	if ($UpdateStatusSQLResult->num_rows > 0) {
		while($row = $UpdateStatusSQLResult->fetch_assoc()) {

			if(!empty($row["UPDATE_STATUS"])){
				$UpdateStatus=$row["UPDATE_STATUS"];
				$UpdateReqId=$row["REQ_ID"];				
				
				$UpdateSQL = "UPDATE
								$schema.tb_req_info req
							SET
								STATUS = '$UpdateStatus'
							WHERE
								REQ_ID = $UpdateReqId";
				
				$conn->query($UpdateSQL);
			}
		}
	}
?>