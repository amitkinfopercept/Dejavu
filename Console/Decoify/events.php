<?php
ini_set('memory_limit', '-1');

if(!isset($_SESSION)) 
{ 
    ini_set('session.cookie_samesite', 'None');
    session_start(); 
}

include 'db.php';

function SearchQuery()
{
	$mysqli = db_connect();
	$user_id=$_SESSION['user_id'];
		
	$stmt = $mysqli->prepare("SELECT id, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp FROM Alerts where Status=1 and user_id=? ORDER BY LogInsertedTimeStamp DESC;");
	$stmt->bind_param("s", $user_id); 	
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return;
	}

	//$arr = $result->fetch_assoc();

	while($row = $result->fetch_array()) {

  	$event[] = $row;
	
	}

	$stmt->close();

	return $event;
}

function DisableAlert($alert_id)
{
	$mysqli = db_connect();

	$user_id=$_SESSION['user_id'];

	$stmt = $mysqli->prepare("Update Alerts SET status = 0 where id= ? and user_id=?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("is", $alert_id, $user_id);

	$stmt->execute();

	$stmt->close();
}


function removeAlerts()
{
	$mysqli = db_connect();

	$user_id=$_SESSION['user_id'];

	$stmt = $mysqli->prepare("Update Alerts SET status = 0 and user_id=?");

	$stmt->bind_param("s", $user_id); 	

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->execute();

	$stmt->close();
}

function getSearchFilter()
{
	$mysqli = db_connect();

	$user_id=$_SESSION['user_id'];

	$stmt = $mysqli->prepare("SELECT search_filter from SearchFilter where Status=1 and user_id=?");

	$stmt->bind_param("s", $user_id);

	$stmt->execute();

	$result = $stmt->get_result();
	
	
	while($row = $result->fetch_assoc()) {
		$event[] = $row;
	}
	
	$search_filter =  $event[0]['search_filter'];

	return $search_filter;

}

function AdvanceQuery($vals, $startDate, $endDate)
{
	$user_id=$_SESSION['user_id'];

	$filterCont = array(
		'all' => 'and',
		'any' => 'or'
		);

	$criteriaConst = array(
		'decoyName' => 'Decoy_Name',
		'decoyIP' => 'Decoy_IP',
		'attackerIP' => 'Attacker_IP',
		'eventType' => 'EventType',
		'decoyGroup' => 'Decoy_Group'
		);
	
	$query = '';

	$filter = $filterCont[$vals[0]['filter']];

	if($startDate != '' and $endDate != '')
	{
		$startDate = (string)$startDate . ' 00:00:01';

		$endDate = (string)$endDate . ' 23:59:59';
	}	

	$i = 0;

	foreach ($vals[0]['searchQuery'] as $val) {

		$criteria = $val['criteria'];
		$condition = $val['condition'];
		$searchData = $val['search_data'];

		$col = $criteriaConst[$criteria];

		//Get the condition
		if ($condition == 'eq')
		{
			$cond1 = 'or';
			$cond2 = '=';
		}
		elseif ($condition == 'not_eq')
		{
			$cond1 = 'and';
			$cond2 = '<>';
		}

		$search_data_ar = explode(',', $searchData);

		foreach ($search_data_ar as $key => $value) {
		
		if($key==0)
		{
			$query .= " ".$filter." (".$col." ".$cond2." ?";
		}
		else
		{
			$query .= " ".$cond1." ".$col." ".$cond2." ?";
		}

		$a_params[] = $value;
    	//Binding parameters. Types: s = string, i = integer
    	$a_param_type[] = "s";
		}
		$query .= ")";
		//end query

	}

    $mysqli = db_connect();

	//appending the query based on and filter
	if ($filter == 'and')
	{
		$search_query = "SELECT id, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp FROM Alerts where Status=1 and user_id=? ".$query. "ORDER BY LogInsertedTimeStamp Desc ";
		if($startDate != '' and $endDate != '')
		{
			$search_query = "SELECT id, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp FROM Alerts where Status=1 and user_id=? and (LogInsertedTimeStamp between ? and ? )".$query. "ORDER BY LogInsertedTimeStamp Desc"; 
		}
		
	}

	elseif ($filter == 'or') {
		$search_query = "SELECT id, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp FROM Alerts where Status=1 and user_id=? and (1=2".$query. ")";
		if($startDate != '' and $endDate != '')
		{
			$search_query = "SELECT id, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp FROM Alerts where Status=1 and user_id=? and (LogInsertedTimeStamp between ? and ? ) and (1=2".$query. ")";
		}
	}

	$stmt = $mysqli->prepare($search_query);
	
	if (!$stmt) {
    throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	// make $a_param_type a string
	$str_param_type = implode('', $a_param_type);

	// add this string as a first element of array
	array_unshift($a_params,$str_param_type);

	$tmp = array();
	
	foreach ($a_params as $key => $value) {
	    // each value of tmp is a reference to `$a_params` values
	    $query_params[$key] = &$a_params[$key];  
	}

	//this is form query string like -> SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM CloudLogs where (TimeStamp between ? and ? ) and (Decoy_Name = ?)Array ( [0] => sss [1] => 2018-07-01 00:00:01 [2] => 2018-07-31 23:59:59 [3] => )

	if($startDate != '' and $endDate != '')
	{
		$new_params = 'sss' . $query_params[0];

		array_unshift($query_params, $user_id, $new_params, $startDate, $endDate);

		array_splice($query_params, 4, 1);
	}

	else{
		$new_params = 's' . $query_params[0];

		array_unshift($query_params, $new_params, $user_id);

		array_splice($query_params, 2, 1);

	}

	function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
	}


	// try to call
	call_user_func_array([$stmt,'bind_param'],refValues($query_params));
	
	

	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return;
	}

	else{
		while($row = $result->fetch_array()) {

	  	$event[] = $row;
		
		}

		$stmt->close();

		return $event;
	}

}

function checkSearchFilter()
{
	$mysqli = db_connect();

	$user_id=$_SESSION['user_id'];

	$stmt = $mysqli->prepare("SELECT search_filter from SearchFilter where Status=1 and user_id=?");

	$stmt->bind_param("s", $user_id);
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {

		$row = 0;

		return $row;
	}

	else
	{
		$row = 1;

		return $row;
	}
}


if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin'){

	$user_id=$_SESSION['user_id'];

	if(isset($_POST["action"]) && $_POST["action"]=='disable')
	{
		$alert_id = $_POST["alert_id"];

		DisableAlert($alert_id);
	}

	if(isset($_POST["delete"]) && $_POST["delete"]=='delete_all')
	{
		removeAlerts();
	}

	//check if filter saved
	$row = checkSearchFilter();

	if($row == 0)
	{
		$event = SearchQuery();
	}

	elseif ($row == 1) {

		$search_filter = getSearchFilter();

		$vals = json_decode(stripslashes($search_filter), true);

		$event = AdvanceQuery($vals, $startDate, $endDate);
	}

	require 'eventsView.php';
}
else {
	header('location:loginView.php');
}

?>
