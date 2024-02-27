<?php

namespace Intouch\Framework\ReportEngine;

use Intouch\Framework\Collection\GenericDefinition;

class ReportDefinition extends GenericDefinition {

    // Parametros principales del Reporte
    //

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdCliente = null;

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdReporteDefinicion = 0;
    public $Nombre = '';
    public $Descripcion = '';

    /** 
     * required 
     * validation = isset(@prop) && @prop != "" */
    public $ServerURI = null;

    /** 
     * required 
     * validation = isset(@prop) && @prop != "" */
    public $ReportURI = null;

    /** 
     * required 
     * validation = isset(@prop) && @prop != "" */
    public $ServerUsername = null;
    
    public $ServerOrganization = null;

    /** 
     * required 
     * validation = isset(@prop) && @prop != "" */
    public $ServerPassword = null;


    // Parametros para reportes de gestión
    //
    public $ObtenerOperacion = false;
    public $ObtenerOperacionLabel = '';
    public $ObtenerPlanta = false;
    public $ObtenerPlantaLabel = '';
    public $ObtenerMaquina = false;
    public $ObtenerMaquinaLabel = false;

    // Parametros para reportes de operación
    //
    public $ObtenerDetencion = true;
    public $ObtenerDetencionLabel = '';
    public $ObtenerMantencion = true;
    public $ObtenerMantencionLabel = '';
    public $ObtenerTurno = true;
    public $ObtenerTurnoLabel = '';
    public $ObtenerIntervaloTurno = false;
    public $ObtenerIntervaloTurnoLabel = '';

    // Parametros generales
    //
    public $ObtenerFechaDesde = true;
    public $ObtenerFechaDesdeLabel = '';
    public $ObtenerFechaHasta = true;
    public $ObtenerFechaHastaLabel = '';

}