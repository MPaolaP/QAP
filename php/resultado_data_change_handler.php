<?php
	session_start();
	include_once"verifica_sesion.php";
	
	header('Content-Type: text/xml');
	echo "<?xml version='1.0' encoding='UTF-8' standalone='yes' ?>";
	
	actionRestriction_102();
	
	$header = $_POST['header'];
	
	switch ($header) {
		case 'saveTempPdf':
		
			$filename = $_POST['filename'];
			$pdfstatus = $_POST['pdfstatus'];
			$pdftoken = mysql_real_escape_string($_POST['pdftoken']);
			$html = mysql_real_escape_string($_POST['html']);
			$html = str_replace("amp;","&",$html);
			$html = str_replace("plus","+",$html);
			
			$qry = "SELECT id_temp_pdf FROM $tbl_temp_pdf WHERE pdf_token = '$pdftoken' LIMIT 0,1";
			$checkrows = mysql_num_rows(mysql_query($qry));
			mysqlException(mysql_error(),$header."_01");
			$qryData = mysql_fetch_array(mysql_query($qry));
			mysqlException(mysql_error(),$header."_02");					
			
			if ($checkrows > 0) {
				$qry = "SELECT html_content FROM $tbl_temp_pdf WHERE id_temp_pdf = ".$qryData['id_temp_pdf'];
				
				mysql_query($qry);
				mysqlException(mysql_error(),$header."_03");
				$innerQryData = mysql_fetch_array(mysql_query($qry));
				
				$html = $innerQryData['html_content']."<!-- sheet separator -->".$html;
				
				$qry = "UPDATE $tbl_temp_pdf SET html_content = '$html',pdf_status = $pdfstatus WHERE id_temp_pdf = ".$qryData['id_temp_pdf'];
				
				mysql_query($qry);
				mysqlException(mysql_error(),$header."_04");				
			} else {
				
				$qry = "INSERT INTO $tbl_temp_pdf (filename,html_content,pdf_token,pdf_status) VALUES ('$filename','$html','$pdftoken',$pdfstatus)";
				
				mysql_query($qry);
				mysqlException(mysql_error(),$header."_05");
				
				$qry = "SELECT id_temp_pdf FROM $tbl_temp_pdf WHERE pdf_token = '$pdftoken' LIMIT 0,1";
				$qryData = mysql_fetch_array(mysql_query($qry));
				mysqlException(mysql_error(),$header."_06");					
			}
			
			echo '<response code="1">';
			echo $qryData['id_temp_pdf'];
			echo '</response>';		
		
		break;
		case 'deleteTempCharts':
			
			$fileArray = explode("|",$_POST['filenamearray']);
			
			for ($x = 0; $x < sizeof($fileArray); $x++) {
				$path = "temp_chart/".$fileArray[$x].".jpg";
				if (file_exists($path)) {
					unlink($path);
				}
			}		
		
		break;
		case 'deleteTempFiles':
			
			$files = glob('temp_chart/*');
			$counter = 0;
			
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file);
					$counter++;
				}
			}		
		
			echo '<response code="1">';
			echo $counter;
			echo '</response>';			
		
		break;
		case 'saveAnalitMedia':
		
			actionRestriction_101();
		
			$postValues_1 = explode("|",clean($_POST['ids']));
			$postValues_2 = explode("|",clean($_POST['melvl1']));
			$postValues_3 = explode("|",clean($_POST['delvl1']));
			$postValues_4 = explode("|",clean($_POST['cvlvl1']));
			$postValues_5 = explode("|",clean($_POST['melvl2']));
			$postValues_6 = explode("|",clean($_POST['delvl2']));
			$postValues_7 = explode("|",clean($_POST['cvlvl2']));
			$postValues_8 = explode("|",clean($_POST['melvl3']));
			$postValues_9 = explode("|",clean($_POST['delvl3']));
			$postValues_10 =  explode("|",clean($_POST['cvlvl3']));
			$postValues_11 =  clean($_POST['sampleid']);
			$postValues_12 =  explode("|",clean($_POST['nlvl1']));
			$postValues_13 =  explode("|",clean($_POST['nlvl2']));
			$postValues_14 =  explode("|",clean($_POST['nlvl3']));
			$postValues_15 =  clean($_POST['labid']);
			$postValues_16 =  clean($_POST['programtypeid']);
			$postValues_17 =  clean($_POST['savemethod']);
			$postValues_18 =  clean($_POST['programid']);			
			
			$insertedValues = 0;
			
			for ($y = 0; $y < sizeof($postValues_1); $y++) {
			
				$tempValue_2 = str_replace(",",".",$postValues_2[$y]);
				$tempValue_3 = str_replace(",",".",$postValues_3[$y]);
				$tempValue_4 = str_replace(",",".",$postValues_4[$y]);
				$tempValue_5 = str_replace(",",".",$postValues_5[$y]);
				$tempValue_6 = str_replace(",",".",$postValues_6[$y]);
				$tempValue_7 = str_replace(",",".",$postValues_7[$y]);
				$tempValue_8 = str_replace(",",".",$postValues_8[$y]);
				$tempValue_9 = str_replace(",",".",$postValues_9[$y]);
				$tempValue_10 = str_replace(",",".",$postValues_10[$y]);
				$tempValue_11 = $postValues_11;
				$tempValue_12 = $postValues_12[$y];
				$tempValue_13 = $postValues_13[$y];
				$tempValue_14 = $postValues_14[$y];
				$tempValue_15 = $postValues_15;
				$tempValue_16 = $postValues_16;
				$tempValue_17 = $postValues_17;
				$tempValue_18 = $postValues_18;
						
				$lvl = array(1,2,3);
			
				if ($postValues_15 != "NULL") {
					$tempValue_1 = array($postValues_1[$y]);
					$tbl_media_evaluacion = $tbl_media_evaluacion_caso_especial;
					$extraQry_1 = ",id_laboratorio";
					$extraQry_2 = " AND id_laboratorio = $tempValue_15";
					$extraQry_3 = ",$tempValue_15";
				} else {
					$extraQry_1 = "";
					$extraQry_2 = "";
					$extraQry_3 = "";
					
					switch ($tempValue_17) {
						case 1:
							$tempValue_1 = array($postValues_1[$y]);
						break;					
						default:
							$tempValue_1 = array($postValues_1[$y]);
						break;
					}
				}
				
				for ($x = 0; $x < sizeof($tempValue_1); $x++) {
				
					$qry = "SELECT id_media_analito FROM $tbl_media_evaluacion WHERE id_configuracion = ".$tempValue_1[$x]." AND nivel = $lvl[0] AND id_muestra = $tempValue_11$extraQry_2";
					
					$qryArray = mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x01");			
					
					$checkrows = mysql_num_rows($qryArray);
					
					if ($checkrows == 0) {
						
						$qry = "INSERT INTO $tbl_media_evaluacion (id_configuracion,media_estandar,desviacion_estandar,coeficiente_variacion,n_evaluacion,nivel,id_muestra$extraQry_1) VALUES (".$tempValue_1[$x].",'$tempValue_2','$tempValue_3','$tempValue_4','$tempValue_12',$lvl[0],$tempValue_11$extraQry_3)";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x02");
						$logQuery['INSERT'][$iSum] = $qry;
						$iSum++;					
						
					} else {
						
						$qry = "UPDATE $tbl_media_evaluacion SET media_estandar = '$tempValue_2', desviacion_estandar = '$tempValue_3', coeficiente_variacion = '$tempValue_4', n_evaluacion = '$tempValue_12' WHERE id_configuracion = ".$tempValue_1[$x]." AND nivel = $lvl[0] AND id_muestra = $tempValue_11$extraQry_2";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x03");
						$logQuery['UPDATE'][$uSum] = $qry;
						$uSum++;	
						
					}
					
					$qry = "SELECT id_media_analito FROM $tbl_media_evaluacion WHERE id_configuracion = ".$tempValue_1[$x]." AND nivel = $lvl[1] AND id_muestra = $tempValue_11$extraQry_2";
					
					$qryArray = mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x04");			
					
					$checkrows = mysql_num_rows($qryArray);
					
					if ($checkrows == 0) {
						
						$qry = "INSERT INTO $tbl_media_evaluacion (id_configuracion,media_estandar,desviacion_estandar,coeficiente_variacion,n_evaluacion,nivel,id_muestra$extraQry_1) VALUES (".$tempValue_1[$x].",'$tempValue_5','$tempValue_6','$tempValue_7','$tempValue_13',$lvl[1],$tempValue_11$extraQry_3)";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x05");
						$logQuery['INSERT'][$iSum] = $qry;
						$iSum++;					
						
					} else {
						
						$qry = "UPDATE $tbl_media_evaluacion SET media_estandar = '$tempValue_5', desviacion_estandar = '$tempValue_6', coeficiente_variacion = '$tempValue_7', n_evaluacion = '$tempValue_13' WHERE id_configuracion = ".$tempValue_1[$x]." AND nivel = $lvl[1] AND id_muestra = $tempValue_11$extraQry_2";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x06");
						$logQuery['UPDATE'][$uSum] = $qry;
						$uSum++;	
						
					}
					
					$qry = "SELECT id_media_analito FROM $tbl_media_evaluacion WHERE id_configuracion = ".$tempValue_1[$x]." AND nivel = $lvl[2] AND id_muestra = $tempValue_11$extraQry_2";
					
					$qryArray = mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x07");			
					
					$checkrows = mysql_num_rows($qryArray);
					
					if ($checkrows == 0) {
						
						$qry = "INSERT INTO $tbl_media_evaluacion (id_configuracion,media_estandar,desviacion_estandar,coeficiente_variacion,n_evaluacion,nivel,id_muestra$extraQry_1) VALUES (".$tempValue_1[$x].",'$tempValue_8','$tempValue_9','$tempValue_10','$tempValue_14',$lvl[2],$tempValue_11$extraQry_3)";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x08");
						$logQuery['INSERT'][$iSum] = $qry;
						$iSum++;					
						
					} else {
						
						$qry = "UPDATE $tbl_media_evaluacion SET media_estandar = '$tempValue_8', desviacion_estandar = '$tempValue_9', coeficiente_variacion = '$tempValue_10', n_evaluacion = '$tempValue_14' WHERE id_configuracion = ".$tempValue_1[$x]." AND nivel = $lvl[2] AND id_muestra = $tempValue_11$extraQry_2";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x09");
						$logQuery['UPDATE'][$uSum] = $qry;
						$uSum++;	
						
					}				
					
					$insertedValues++;
					
				}
			}
		
			echo '<response code="1">';
			echo $insertedValues;
			echo '</response>';			
		
		break;
		case 'mediaValueEditor':

			actionRestriction_100();

			$which = $_POST['which'];
			$id = $_POST['id'];
			$value = $_POST['value'];
			$otherIds = explode("|",$_POST['otherids']);
			
			if ($otherIds[0] != "") {
				$labid = $otherIds[0];
			} else {
				$labid = "NULL";
			}
			
			if ($otherIds[1] != "") {
				$sampleid = $otherIds[1];
			} else {
				$sampleid = "NULL";
			}

			if ($otherIds[2] != "") {
				$saveValueForAllConfigurations = $otherIds[2];
			} else {
				$saveValueForAllConfigurations = "NULL";
			}				
			
			if ($labid == "NULL" || $saveValueForAllConfigurations == 0) {
				$tableToEdit = $tbl_media_evaluacion;
				$tableToInsert = "(id_configuracion,media_estandar,desviacion_estandar,coeficiente_variacion,n_evaluacion,id_muestra,nivel,id_analito_resultado_reporte_cualitativo) VALUES ($id,0,0,0,0,$sampleid,0,$value)";
				$wichTable = 0;
			} else {
				$tableToEdit = $tbl_media_evaluacion_caso_especial;
				$tableToInsert = "(id_configuracion,media_estandar,desviacion_estandar,coeficiente_variacion,n_evaluacion,id_muestra,nivel,id_laboratorio,id_analito_resultado_reporte_cualitativo) VALUES ($id,0,0,0,0,$sampleid,0,$labid,$value)";
				$wichTable = 1;
			}
			
			switch ($which) {	
				case 1:
					
					if ($wichTable == 1) {
						$qry = "SELECT id_media_analito FROM $tableToEdit WHERE id_configuracion = $id AND id_muestra = $sampleid AND id_laboratorio = $labid";
					} else {
						$qry = "SELECT id_media_analito FROM $tableToEdit WHERE id_configuracion = $id AND id_muestra = $sampleid";
					}
					
					$qryRows = mysql_num_rows(mysql_query($qry));
					mysqlException(mysql_error(),$header."_0x01");
					
					if ($qryRows > 0) {
						if ($wichTable == 1) {
							$qry = "UPDATE $tableToEdit SET id_analito_resultado_reporte_cualitativo = $value WHERE id_configuracion = $id AND id_muestra = $sampleid AND id_laboratorio = $labid";
						} else {
							$qry = "UPDATE $tableToEdit SET id_analito_resultado_reporte_cualitativo = $value WHERE id_configuracion = $id AND id_muestra = $sampleid";
						}
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x02");
						$logQuery['UPDATE'][$uSum] = $qry;
						$uSum++;						
					} else {
						$qry = "INSERT INTO $tableToEdit $tableToInsert";
						mysql_query($qry);
						mysqlException(mysql_error(),$header."_0x03");
						$logQuery['INSERT'][$iSum] = $qry;
						$iSum++;							
					}
				
				break;	
			}
			
			echo'<response code="1"></response>';
			
		break;
		case 'referenceValueRegistry':

			actionRestriction_101();

			$postValues_1 = $_POST['sampleid'];
			$postValues_2 = $_POST['analyzerid'];
			$postValues_3 = $_POST['methodid'];
			$postValues_4 = $_POST['referencevalue'];
			$postValues_5 = $_POST['analitid'];
			$postValues_6 = $_POST['programid'];
			$postValues_7 = $_POST['unitid'];
			$postValues_8 = $_POST['labid'];
			
			$tempValue_1 = $postValues_1;
			$tempValue_2 = $postValues_2;
			$tempValue_3 = $postValues_3;
			$tempValue_4 = clean($postValues_4);
			$tempValue_5 = $postValues_5;
			$tempValue_6 = $postValues_6;
			$tempValue_7 = $postValues_7;
			$tempValue_8 = $postValues_8;
			
			$qry = "SELECT id_valor_metodo_referencia 
				FROM $tbl_valor_metodo_referencia 
				WHERE id_laboratorio = $tempValue_8 AND id_muestra = $tempValue_1 AND id_metodologia = $tempValue_3 AND id_unidad = $tempValue_7 AND id_analito = $tempValue_5 LIMIT 0,1";
			$checkrows = mysql_num_rows(mysql_query($qry));
			mysqlException(mysql_error(),$header."_0x01");	

			if($checkrows > 0){
				echo '<response code="422">Ya existe un valor Xpt para la misma muestra y laboratorio</response>';
			} else {
				$qry = "INSERT INTO $tbl_valor_metodo_referencia (id_analito,id_metodologia,id_unidad,id_muestra,valor_metodo_referencia, id_laboratorio) VALUES ($tempValue_5,$tempValue_3,$tempValue_7,$tempValue_1,'$tempValue_4',$tempValue_8)";
				mysql_query($qry);
				mysqlException(mysql_error(),$header."_0x02");
				$logQuery['INSERT'][$iSum] = $qry;
				$iSum++;
				
				/*
				$qry = "SELECT $tbl_muestra.id_muestra 
						FROM $tbl_muestra 
						INNER JOIN $tbl_muestra_programa ON $tbl_muestra.id_muestra = $tbl_muestra_programa.id_muestra 
						WHERE $tbl_muestra.id_muestra NOT IN (
							SELECT $tbl_valor_metodo_referencia.id_muestra FROM $tbl_valor_metodo_referencia INNER JOIN $tbl_muestra_programa ON $tbl_valor_metodo_referencia.id_muestra = $tbl_muestra_programa.id_muestra WHERE $tbl_muestra_programa.id_programa = $tempValue_6 AND id_analito = $tempValue_5 AND id_metodologia = $tempValue_3 AND id_unidad = $tempValue_7) AND $tbl_muestra_programa.id_programa = $tempValue_6";
				
				$qryArray = mysql_query($qry);
				mysqlException(mysql_error(),$header."_0x03");
				
				while ($qryData = mysql_fetch_array($qryArray)) {
					$qry = "INSERT INTO $tbl_valor_metodo_referencia (id_analito,id_metodologia,id_unidad,id_muestra,valor_metodo_referencia) VALUES ($tempValue_5,$tempValue_3,$tempValue_7,".$qryData['id_muestra'].",'0')";
					mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x04");
					$logQuery['INSERT'][$iSum] = $qry;
					$iSum++;					
				}
				*/
				echo '<response code="1">1</response>';
			}
			
		break;
		case 'referenceValueValueEditor':

			actionRestriction_100();

			$which = $_POST['which'];
			$id = $_POST['id'];
			$value = $_POST['value'];
			
			switch ($which) {
				case 4:
					$qry = "UPDATE $tbl_valor_metodo_referencia SET id_metodologia = $value WHERE id_valor_metodo_referencia = $id";
					mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x01");
					$logQuery['UPDATE'][$uSum] = $qry;
					$uSum++;
				
				break;					
				case 5:
					$qry = "UPDATE $tbl_valor_metodo_referencia SET valor_metodo_referencia = '".clean($value)."' WHERE id_valor_metodo_referencia = $id";
					mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x01");
					$logQuery['UPDATE'][$uSum] = $qry;
					$uSum++;
				
				break;
				case 6:
					$qry = "UPDATE $tbl_valor_metodo_referencia SET id_unidad = $value WHERE id_valor_metodo_referencia = $id";
					mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x01");
					$logQuery['UPDATE'][$uSum] = $qry;
					$uSum++;
				
				break;					
			}
			
			echo'<response code="1"></response>';
			
		break;
		case 'referenceValueDeletion':

			actionRestriction_100();

			$ids = explode("|",$_POST['ids']);
			
			for ($x = 0; $x < sizeof($ids); $x++) {
				$qry = "SELECT id_valor_metodo_referencia FROM $tbl_valor_metodo_referencia WHERE id_valor_metodo_referencia = $ids[$x] LIMIT 0,1";
				
				$checkrows = mysql_num_rows(mysql_query($qry));
				
				if ($checkrows > 0) {
					$qry = "DELETE FROM $tbl_valor_metodo_referencia WHERE id_valor_metodo_referencia = $ids[$x]";
					mysql_query($qry);
					mysqlException(mysql_error(),$header."_0x01_".$x);
					$logQuery['DELETE'][$dSum] = $qry;
					$dSum++;
	
				}
			}

			echo'<response code="1">1</response>';
			
		break;		
		default:
			echo'<response code="0">PHP dataChangeHandler error: id "'.$header.'" not found</response>';
		break;
	}
	
	if (sizeof($logQuery['INSERT']) > 0) {
		for ($y = 0; $y < sizeof($logQuery['INSERT']); $y++) {
			$tempLogQuery = mysql_real_escape_string($logQuery['INSERT'][$y]);
			$qry = "INSERT INTO $tbl_log (id_usuario,fecha,hora,log,query) VALUES ($userId,'$logDate','$logHour','Registro de información','$tempLogQuery')";
			mysql_query($qry);
			mysqlException(mysql_error(),$header."_LGQ_0x01_".$y);
		}
	}
	
	if (sizeof($logQuery['UPDATE']) > 0) {
		for ($y = 0; $y < sizeof($logQuery['UPDATE']); $y++) {
			$tempLogQuery = mysql_real_escape_string($logQuery['UPDATE'][$y]);
			$qry = "INSERT INTO $tbl_log (id_usuario,fecha,hora,log,query) VALUES ($userId,'$logDate','$logHour','Actualización de información','$tempLogQuery')";
			mysql_query($qry);
			mysqlException(mysql_error(),$header."_LGQ_0x02_".$y);			
		}		
	}
	
	if (sizeof($logQuery['DELETE']) > 0) {
		for ($y = 0; $y < sizeof($logQuery['DELETE']); $y++) {
			$tempLogQuery = mysql_real_escape_string($logQuery['DELETE'][$y]);
			$qry = "INSERT INTO $tbl_log (id_usuario,fecha,hora,log,query) VALUES ($userId,'$logDate','$logHour','Remoción de información','$tempLogQuery')";
			mysql_query($qry);
			mysqlException(mysql_error(),$header."_LGQ_0x03_".$y);			
		}		
	}
	
	mysql_close($con);
	exit;
?>