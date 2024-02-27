<?php

namespace Intouch\Framework\ReportEngine;

use Exception;
use Jaspersoft\Client\Client;

abstract class Report {

    public $IdReporteDefinicion = 0;
    public $ReporteDefinicion = null;

    public function __construct(ReportDefinition $reportDefinition)
    {
        if (!isset($reportDefinition)) {
            throw new \Exception("Debe especificar la definición del reporte");
        }
        else if (!($reportDefinition instanceof ReportDefinition)) {
            throw new \Exception("La definicion de reporte no es valida");
        }
        else {

            // SIEMPRE VERIFICAR QUE TENEMOS DEFINIDO EL ID_CLIENTE DE ESTE REPORTE!!
            //
            if (!isset($reportDefinition->IdCliente) || $reportDefinition->IdCliente <= 0) {                
                throw new \Exception("No se ha especificado el ID de Cliente para este reporte. Este valor es mandatorio");
            }
            else {
                $this->IdReporteDefinicion = $reportDefinition->IdReporteDefinicion;
                $this->ReporteDefinicion = $reportDefinition;
            }
        }
    }

    /**
     * Obtiene los datos para el reporte
     * 
     * @param ReportFilter $filter
     * @param int $idCliente
     * 
     * @return ReportDataset
     */
    abstract function GetDataset(ReportFilter $filter, $idCliente);

    /** 
     * Obtiene el reporte desde el servidor, según los parámetros especificados 
     */
    public function GetReport(ReportFilter $filter, ReportOutputDefinition $outputDefinition) {

        // SIEMPRE VERIFICAR QUE TENEMOS DEFINIDO EL ID_CLIENTE DE ESTE REPORTE!!
        //
        if (!isset($this->ReporteDefinicion) || !isset($this->ReporteDefinicion->IdCliente) || $this->ReporteDefinicion->IdCliente <= 0) {
            throw new \Exception("No se ha especificado el ID de Cliente para este reporte. Este valor es mandatorio");
        }

        // Obtener el dataset para enviar al reporte
        //
        $sourceDataset = $this->GetDataset($filter, $this->ReporteDefinicion->IdCliente);

        // Validar el dataset
        if ( !($sourceDataset instanceof ReportDataset)) {
            throw new \Exception("El dataset obtenido no es una implementación de ReportDataset");
        }
        
        // Obtener el reporte desde el servidor
        //
        $reportResult = $this->RunReport($this->ReporteDefinicion, $filter, $outputDefinition->OutputType, $sourceDataset);

        // Si se solicitó guardar el reporte, bajarlo a disco
        // if ($outputDefinition->SaveReport)
        //     $this->SaveReport($outputDefinition->ReportFolder, $outputDefinition->ReportFilename);

        return $reportResult;
    }

    protected function RunReport(ReportDefinition $reportDefinition, ReportFilter $filter, $outputType, ReportDataset $sourceDataset) {

        // Creamos el cliente de ReportServer y realizamos login con el usuario
        $client = new Client($reportDefinition->ServerURI, $reportDefinition->ServerUsername, $reportDefinition->ServerPassword, $reportDefinition->ServerOrganization);

        $controls = array();

        if (isset($sourceDataset->DataLocation) && $sourceDataset->DataLocation != '') {
            $controls['Location'] = $sourceDataset->DataLocation;
        }

        if (count($controls) <= 0) {
            $controls = null;
        }

        $report = $client->reportService()->runReport($reportDefinition->ReportURI, $outputType, null, null, $controls);

        return $report;
    }

    /**
     * Despliega el HTML necesario para capturar los parámetros del reporte en el sistema
     */
    public function DisplayParameterView() {

    }

    /**
     * Mapea el objeto o arreglo de objetos DATASET en forma de arreglos
     */
    protected function MapearDataset($dataset) {

        return json_encode($dataset, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION);

        /*
        $resultado = array();

        if (is_array($dataset)) {
            foreach($dataset as $data) {
                if (is_array($data)) {
                    array_push($resultado, $this->MapearDataset($data));
                }
                else {
                    array_push($resultado, $this->MapearObjeto($data));
                }
            }

            return $resultado;
        }
        else {

        }
        */
    }

    /**
     * Mapea un objeto en un arreglo asociativo
     */
    protected function MapearObjeto($objeto) {
        // Mapear las propiedades del objeto
        $elemento = new \StdClass();
    }
}