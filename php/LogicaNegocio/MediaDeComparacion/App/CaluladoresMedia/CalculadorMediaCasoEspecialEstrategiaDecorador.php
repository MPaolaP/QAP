<?php
require_once __DIR__ . "/CalculadorMediaConFiltrosAtipicosEstrategia.php";
class CalculadorMediaCasoEspecialEstrategiaDecorador extends CalculadorMediaConFiltrosAtipicosEstrategia
{

    private $mediaEvaluacionRepo;

    private $calculadorFiltroAtipicos;

    private $idLaboratorio;

    public function __construct($calculadorFiltroAtipicos)
    {
        $this->calculadorFiltroAtipicos = $calculadorFiltroAtipicos;
    }

    public function setMediaEvaluacionRepo($repo)
    {
        $this->mediaEvaluacionRepo = $repo;
    }

    public function setIdLaboratorio($id)
    {
        $this->idLaboratorio = $id;
    }

    public function calcular($configAnalito, $muestra)
    {
        $mediaEvaluacionEspecial = $this->mediaEvaluacionRepo->getMedia(
            $configAnalito["id_configuracion"],
            $muestra["nivel_lote"],
            $muestra['id_muestra'],
            $this->idLaboratorio
        );

        // if (count($mediaEvaluacionEspecial) > 0 && $mediaEvaluacionEspecial[0]["tipo_digitacion_wwr"] == 4) {
        //     //var_dump($mediaEvaluacionEspecial);
        //     //exit;
        //     //concenso
        //     // if (
        //     //     isset($mediaEvaluacionEspecial[0]["media_estandar"])
        //     //     && is_numeric($mediaEvaluacionEspecial[0]["media_estandar"])
        //     // ) {
        //     //     //es por concenso y ademas la muestra y el analito tienen definido la media se da prioridad
        //     //     return [
        //     //         "media" => $mediaEvaluacionEspecial[0]["media_estandar"],
        //     //         "de" => $mediaEvaluacionEspecial[0]["desviacion_estandar"],
        //     //         "cv" => $mediaEvaluacionEspecial[0]["coeficiente_variacion"],
        //     //         "n" => $mediaEvaluacionEspecial[0]["n_evaluacion"]
        //     //     ];
        //     // }
        //     //como no tienen definido la media se realiza la busqueda de los resultados de laboratorios
        //     //pero en filtro atipicos se aplica el que tenga definido el decorador
        //     //esto con le objetivo de que el decorador pueda tener una estrategi de obtencion de rsultados
        //     //diferente a la que tiene la clase decorada
        //     return parent::calcular($configAnalito, $muestra);
        // }
        //como no es un caso especial se calcula con el filtro de atipoco a decorar
        return $this->calculadorFiltroAtipicos->calcular($configAnalito, $muestra);
    }
}