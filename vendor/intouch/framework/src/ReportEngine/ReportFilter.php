<?php

namespace Intouch\Framework\ReportEngine;

use Intouch\Framework\Collection\GenericDefinition;

class ReportFilter extends GenericDefinition {
    public $IdOperacion = null;
    public $IdPlanta = null;
    public $IdDetencion = null;
    public $IdMantencion = null;
    public $FechaInicioTurno = null;
    public $FechaFinTurno = null;
    public $FechaDesde = null;
    public $FechaHasta = null;

    function __construct(array $definition = array()) {
        parent::__construct($definition);
    }
}