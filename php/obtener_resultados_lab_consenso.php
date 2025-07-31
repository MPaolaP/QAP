<?php

session_start();
require_once __DIR__ . "/repositorys/ResultadosRepository.php";

header('Content-Type: application/json; charset=utf-8');
$response_data = array();

// 1. Recibir todos los parámetros
$id_config_consenso = isset($_POST['id_config_consenso']) ? $_POST['id_config_consenso'] : null;
$fecha_corte = isset($_POST['fecha_corte']) ? $_POST['fecha_corte'] : null;
$id_muestra = isset($_POST['id_muestra']) ? $_POST['id_muestra'] : null;

error_log("PRESELECCION_DEBUG: Iniciando. ID Config: {$id_config_consenso}, Fecha Corte: {$fecha_corte}, ID Muestra: {$id_muestra}");

if (empty($id_config_consenso) || empty($fecha_corte) || empty($id_muestra)) {
    echo json_encode(array('error' => 'Parámetros incompletos.'));
    exit;
}

$id_config_consenso_escaped = mysql_real_escape_string($id_config_consenso);
$id_muestra_escaped = mysql_real_escape_string($id_muestra);

$nombre_analito = null;
$nombre_unidad = null;
$nombre_lote = null;

// Lógica para obtener los nombres 
$sql_get_analito_unidad = "SELECT a.nombre_analito, u.nombre_unidad FROM configuracion_laboratorio_analito cla JOIN analito a ON cla.id_analito = a.id_analito JOIN unidad u ON cla.id_unidad = u.id_unidad WHERE cla.id_configuracion = '" . $id_config_consenso . "' LIMIT 1";
error_log("PRESELECCION_DEBUG: SQL analito/unidad: " . $sql_get_analito_unidad);
$query_result_au = mysql_query($sql_get_analito_unidad);
if ($query_result_au && mysql_num_rows($query_result_au) > 0) {
    $data_au = mysql_fetch_assoc($query_result_au);
    $nombre_analito = $data_au['nombre_analito'];
    $nombre_unidad = $data_au['nombre_unidad'];
    error_log("PRESELECCION_DEBUG: Analito: {$nombre_analito}, Unidad: {$nombre_unidad}");
} else {
    error_log("PRESELECCION_ERROR: No se pudo obtener analito/unidad. Error: " . mysql_error());
}


$sql_get_lote = "SELECT l.nombre_lote, p.nombre_programa FROM lote l INNER JOIN muestra_programa mp ON l.id_lote = mp.id_lote JOIN programa p ON p.id_programa = mp.id_programa WHERE mp.id_muestra = '" . $id_muestra . "' LIMIT 1";
error_log("PRESELECCION_DEBUG: SQL lote: " . $sql_get_lote);
$query_result_lote = mysql_query($sql_get_lote);
if ($query_result_lote && mysql_num_rows($query_result_lote) > 0) {
    $data_lote = mysql_fetch_assoc($query_result_lote);
    $nombre_lote = $data_lote['nombre_lote'];
    error_log("PRESELECCION_DEBUG: Lote: {$nombre_lote}");
} else {
    error_log("PRESELECCION_ERROR: No se pudo obtener lote. Error: " . mysql_error());
}

// Si todos los nombres fueron obtenidos, procedemos
if ($nombre_lote && $nombre_analito && $nombre_unidad) {
    $resultadosRepo = new ResultadosRepository();
    $raw_results = $resultadosRepo->resultadoPorId($nombre_lote, $nombre_analito, $nombre_unidad, $fecha_corte);

    error_log("PRESELECCION_DEBUG: Resultados obtenidos: " . count($raw_results));

    $hay_selecciones_guardadas = isset($_SESSION['selecciones_consenso_personalizadas'][$id_config_consenso]);

    $ids_guardados = array();
    if ($hay_selecciones_guardadas) {
        $ids_guardados = $_SESSION['selecciones_consenso_personalizadas'][$id_config_consenso];
    }

    // Verificar si hay errores en los resultados
    if (isset($raw_results['error'])) {
        $response_data = array('error' => $raw_results['error']);
    } else if (is_array($raw_results)) {
        foreach ($raw_results as $key => $row) {
            $id_unico = isset($row['id_unico_resultado']) ? $row['id_unico_resultado'] : null;

            $seleccionado_previamente = false;

            if ($hay_selecciones_guardadas) {
                // CASO 1: Ya se guardó algo antes. Respetar esa selección.
                // Verificamos si el ID actual está en el array de IDs guardados.
                // Comparamos como strings para evitar problemas de tipo (ej. "123" vs 123).
                if (in_array((string) $id_unico, array_map('strval', $ids_guardados))) {
                    $seleccionado_previamente = true;
                }
            } else {
                // CASO 2: Es la primera vez que se abre la ventana. Seleccionar todo por defecto.
                $seleccionado_previamente = true;
            }

            $response_data[] = array(
                "id_unico_resultado" => $id_unico,
                "it" => (isset($row['no_contador']) && $row['no_contador'] !== '') ? $row['no_contador'] : ($key + 1),
                "resultado" => isset($row['resultado']) ? $row['resultado'] : '',
                "fecha" => isset($row['fecha_resultado']) ? date("Y-m-d", strtotime($row['fecha_resultado'])) : '',
                "ronda_nombre" => isset($row['no_ronda']) ? $row['no_ronda'] : '',
                "muestra_nombre" => isset($row['codigo_muestra']) ? $row['codigo_muestra'] : '',
                "id_laboratorio" => isset($row['no_laboratorio']) ? $row['no_laboratorio'] : '',
                "nombre_laboratorio" => isset($row['nombre_laboratorio']) ? $row['nombre_laboratorio'] : '',
                "nombre_metodologia" => isset($row['nombre_metodologia']) ? $row['nombre_metodologia'] : '',
                "nombre_programa" => isset($row['nombre_programa']) ? $row['nombre_programa'] : '',
                "seleccionado_previamente" => $seleccionado_previamente
            );
        }
        } else {
            $response_data = array('error' => 'El repositorio no devolvió un array de resultados.');
        }
    
} else {
    $response_data = array('error' => 'No se pudieron obtener todos los nombres (lote, analito, unidad) para procesar la solicitud.');
}

echo json_encode($response_data);
exit;
?>