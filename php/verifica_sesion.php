<?php
	if (isset($_SESSION["qap_userId"])) {
		include_once "sql_connection.php";
		
		$current = $_SERVER['PHP_SELF'];
		
		$qry = "SELECT id_sesion FROM $tbl_sesion WHERE token_sesion = '".$_SESSION['qap_token']."'";
		
		$qryRows = mysql_num_rows(mysql_query($qry));
			
		if ($qryRows > 0) {
			//
		} else {
			
			if (strpos($current,'/php/') !== false) {
				header("location:../login.php");
			} else {
				header("location:login.php");
			}
			
			session_destroy();
			mysql_close($con);
			exit;
			
		}
		
	} else {
		
		if (strpos($current,'/php/') !== false) {
			header("location:../login.php");
		} else {
			header("location:login.php");
		}
		
		session_destroy();
		mysql_close($con);
		exit;
		
	}