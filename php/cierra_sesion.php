<?php
	session_start();
	include_once"verifica_sesion.php";

	$qry = "DELETE FROM $tbl_sesion WHERE token_sesion = '".$_SESSION['qap_token']."'";
	
	mysql_query($qry);
	mysqlException(mysql_error(),"001");
	
	session_destroy();
	header("location:../login.php");
	
	mysql_close($con);
	exit;
?>