<?php
require_once __DIR__ . "/Repository.php";
session_start();
class ResultadosRepository extends Repository
{


    /**
     * Devuelve los valores de resultado de una configuracion y una muestra
     *
     * @param [type] $idConfiguracion
     * @param [type] $idMuestra
     * @return array
     */
    public function resultadosPorConfigMuestra($idConfiguracion, $idMuestra)
    {
        $query = "
            SELECT * FROM resultado
            WHERE id_configuracion = " . $idConfiguracion . " AND id_muestra = " . $idMuestra . "
        ";
        return $this->ejecutarQuery($query);
    }

    /**
     * Se encarga de obtener los resultado de todos los participantes
     *
     * @param [type] $idPrograma
     * @param [type] $idAnalito
     * @param [type] $idUnidad
     * @param [type] $idLote
     * @return array
     */
    public function todoLosParticipantesPorAnalito(
        $idPrograma,
        $idUnidad,
        $idLote,
        $idAnalito,
        $fechaCorte,
        $idConfigConsensoActual = null
    ) {
        // Escapar parámetros principales (¡MUY IMPORTANTE!)
        $idPrograma_escaped = mysql_real_escape_string($idPrograma);
        $idUnidad_escaped = mysql_real_escape_string($idUnidad);
        $idLote_escaped = mysql_real_escape_string($idLote);
        $idAnalito_escaped = mysql_real_escape_string($idAnalito);
        $fechaCorte_escaped = mysql_real_escape_string($fechaCorte);
        $query = "SELECT 
        r.id_resultado ,
        r.id_configuracion ,					
        r.valor_resultado,
        cla.id_laboratorio ,
        cla.id_unidad,
        r.fecha_resultado,
        cla.id_analito,
        cla.id_laboratorio,
        r.id_muestra
        from resultado r 
        join configuracion_laboratorio_analito cla on cla.id_configuracion  = r.id_configuracion
        join muestra_programa mp on mp.id_programa = cla.id_programa  and mp.id_muestra = r.id_muestra 
                WHERE cla.id_programa  = '" . $idPrograma_escaped . "' AND 
        cla.id_unidad = '" . $idUnidad_escaped . "' AND
        cla.id_analito = '" . $idAnalito_escaped . "' AND
        mp.id_lote = '" . $idLote_escaped . "' AND
        r.fecha_resultado <= '" . $fechaCorte_escaped . "' AND

        r.valor_resultado is not null and 
        r.valor_resultado != '' 
        ";

        // ---- Lógica para aplicar filtro de selecciones personalizadas ----
        if (
            $idConfigConsensoActual !== null &&
            isset($_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual]) &&
            is_array($_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual]) &&
            !empty($_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual])
        ) {

            $ids_seleccionados = $_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual];
            $ids_escapados_para_sql = [];
            foreach ($ids_seleccionados as $id_sel) {
                if (is_numeric($id_sel)) {
                    $ids_escapados_para_sql[] = intval($id_sel);
                }
            }

            if (!empty($ids_escapados_para_sql)) {
                $query .= " AND r.id_resultado IN (" . implode(",", $ids_escapados_para_sql) . ")";
                error_log("REPO_DEBUG (todoLosParticipantes): Usando IDs seleccionados para config " . $idConfigConsensoActual . ": " . implode(",", $ids_escapados_para_sql));
            } else {
                $query .= " AND 1=0 "; // No hay IDs válidos seleccionados, no traer nada si se esperaba filtro
                error_log("REPO_DEBUG (todoLosParticipantes): IDs seleccionados para config " . $idConfigConsensoActual . " estaban vacíos o no eran numéricos.");
            }
        } else {
            error_log("REPO_DEBUG (todoLosParticipantes): No se usaron selecciones personalizadas para config " . $idConfigConsensoActual);
        }

        $query .= " ORDER BY CAST(r.valor_resultado AS DECIMAL(10, 4))";

        error_log("REPO_DEBUG (todoLosParticipantes): SQL Final: " . $query);
        return $this->ejecutarQuery($query);
    }

    /**
     * Se encarga de obtener los resultado de los participantes de una misma metodologia
     *
     * @param [type] $idPrograma
     * @param [type] $idAnalito
     * @param [type] $idUnidad
     * @param [type] $idLote
     * @param [type] $idMetodologia
     * @return void
     */
    public function todosLosParticipantesMismaMetodologia(
        $idPrograma,
        $idUnidad,
        $idLote,
        $idAnalito,
        $idMetodologia,
        $fechaCorte,
        $idConfigConsensoActual = null
    ) {

        $idPrograma_escaped = mysql_real_escape_string($idPrograma);
        $idUnidad_escaped = mysql_real_escape_string($idUnidad);
        $idLote_escaped = mysql_real_escape_string($idLote);
        $idAnalito_escaped = mysql_real_escape_string($idAnalito);
        $idMetodologia_escaped = mysql_real_escape_string($idMetodologia);
        $fechaCorte_escaped = mysql_real_escape_string($fechaCorte);


        $query = "SELECT 
        r.id_resultado,
        r.id_configuracion,                   
        r.valor_resultado,
        cla.id_laboratorio,
        cla.id_unidad,
        r.fecha_resultado,
        cla.id_analito,
        r.id_muestra
        FROM resultado r 
        JOIN configuracion_laboratorio_analito cla ON cla.id_configuracion  = r.id_configuracion
        JOIN muestra_programa mp ON mp.id_programa = cla.id_programa AND mp.id_muestra = r.id_muestra 
        WHERE cla.id_programa  = '" . $idPrograma_escaped . "' AND 
        cla.id_unidad = '" . $idUnidad_escaped . "' AND
        cla.id_analito = '" . $idAnalito_escaped . "' AND
        cla.id_metodologia = '" . $idMetodologia_escaped . "' AND
        mp.id_lote = '" . $idLote_escaped . "' AND
        r.fecha_resultado <= '" . $fechaCorte_escaped . "' AND
        r.valor_resultado IS NOT NULL AND 
        r.valor_resultado != '' 
    ";

        if (
            $idConfigConsensoActual !== null &&
            isset($_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual]) &&
            is_array($_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual]) &&
            !empty($_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual])
        ) {
            $ids_seleccionados = $_SESSION['selecciones_consenso_personalizadas'][$idConfigConsensoActual];
            $ids_escapados_para_sql = [];
            foreach ($ids_seleccionados as $id_sel) {
                if (is_numeric($id_sel)) {
                    $ids_escapados_para_sql[] = intval($id_sel);
                }
            }

            if (!empty($ids_escapados_para_sql)) {
                $query .= " AND r.id_resultado IN (" . implode(",", $ids_escapados_para_sql) . ")";
                error_log("REPO_DEBUG (mismaMetodologia): Usando IDs seleccionados para config " . $idConfigConsensoActual . ": " . implode(",", $ids_escapados_para_sql));
            } else {
                $query .= " AND 1=0 ";
                error_log("REPO_DEBUG (mismaMetodologia): IDs seleccionados para config " . $idConfigConsensoActual . " estaban vacíos o no eran numéricos.");
            }
        } else {
            error_log("REPO_DEBUG (mismaMetodologia): No se usaron selecciones personalizadas para config " . $idConfigConsensoActual);
        }

        $query .= " ORDER BY CAST(r.valor_resultado AS DECIMAL(10, 3))";

        error_log("REPO_DEBUG (mismaMetodologia): SQL Final: " . $query);
        return $this->ejecutarQuery($query);
    }

    public function resultadoPorId($nombre_lote, $nombre_analito, $nombre_unidad, $fecha_corte)
    {
        $nombre_lote_escaped = mysql_real_escape_string($nombre_lote);
        $nombre_analito_escaped = mysql_real_escape_string($nombre_analito);
        $nombre_unidad_escaped = mysql_real_escape_string($nombre_unidad);
        $fecha_corte_escaped = mysql_real_escape_string($fecha_corte);

        $qry = "SELECT
            resultado.id_resultado as 'id_unico_resultado',
            resultado.valor_resultado as 'resultado',
            resultado.fecha_resultado as 'fecha_resultado',
            programa.nombre_programa as 'nombre_programa',
            ronda.no_ronda as no_ronda,
            contador_muestra.no_contador as no_contador,
            muestra.codigo_muestra as codigo_muestra,
            laboratorio.no_laboratorio as no_laboratorio,
            laboratorio.nombre_laboratorio as nombre_laboratorio,
            metodologia.nombre_metodologia as nombre_metodologia
            from programa
                join muestra_programa on programa.id_programa = muestra_programa.id_programa
                join muestra on muestra.id_muestra = muestra_programa.id_muestra
                join contador_muestra on muestra.id_muestra = contador_muestra.id_muestra
                join ronda on ronda.id_ronda = contador_muestra.id_ronda
                join lote on lote.id_lote = muestra_programa.id_lote
                join resultado on muestra.id_muestra = resultado.id_muestra
                join configuracion_laboratorio_analito on configuracion_laboratorio_analito.id_configuracion = resultado.id_configuracion
                join laboratorio on laboratorio.id_laboratorio = configuracion_laboratorio_analito.id_laboratorio
                join unidad on unidad.id_unidad = configuracion_laboratorio_analito.id_unidad
                join analito on analito.id_analito = configuracion_laboratorio_analito.id_analito
                join metodologia on metodologia.id_metodologia = configuracion_laboratorio_analito.id_metodologia
            where
                resultado.valor_resultado is not null
                and resultado.valor_resultado != ''
                and lote.nombre_lote = '" . $nombre_lote_escaped . "'
                and analito.nombre_analito = '" . $nombre_analito_escaped . "' 
                and unidad.nombre_unidad = '" . $nombre_unidad_escaped . "'
                and resultado.fecha_resultado <= '" . $fecha_corte_escaped . "' 
            order by CAST(resultado.valor_resultado AS DECIMAL(10, 3))";





        return $this->ejecutarQuery($qry);

    }

}