<?php
session_start(); // Es crucial iniciar la sesión para poder usar $_SESSION

header('Content-Type: application/json; charset=utf-8');

$id_config_consenso = isset($_POST['id_config_consenso']) ? $_POST['id_config_consenso'] : null;
// 'ids_resultados_seleccionados' se espera que sea un array
$ids_resultados_seleccionados = isset($_POST['ids_resultados_seleccionados']) ? $_POST['ids_resultados_seleccionados'] : [];

if (empty($id_config_consenso)) {
    echo json_encode(['status' => 'error', 'message' => 'ID de configuración no proporcionado.']);
    exit;
}

// Asegurarse de que $_SESSION['selecciones_consenso_personalizadas'] es un array
if (!isset($_SESSION['selecciones_consenso_personalizadas']) || !is_array($_SESSION['selecciones_consenso_personalizadas'])) {
    $_SESSION['selecciones_consenso_personalizadas'] = [];
}

// Guardar o limpiar las selecciones para el id_config_consenso específico
// Si se envía un array vacío de ids_resultados_seleccionados, efectivamente se limpian/resetean las selecciones para ese config.
$_SESSION['selecciones_consenso_personalizadas'][$id_config_consenso] = $ids_resultados_seleccionados;

if (empty($ids_resultados_seleccionados)) {
    echo json_encode(['status' => 'success', 'message' => 'Selecciones personalizadas limpiadas para esta configuración.']);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Selecciones personalizadas guardadas. Se usarán en el informe.']);
}
exit;
?>