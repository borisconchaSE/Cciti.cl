<?php

namespace Application\BLL\Document\Definitions;

use Intouch\Framework\Document\GenericDocumentDefinition;

class ReportDocumentDefinition extends GenericDocumentDefinition {

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdCliente = 0;

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdTipoReporte = 0;

    public $IdDetencion = null;
    public $IdMantencion = null;
    public $IdOperacion = null;
    public $IdPlanta = null;

    public function __construct(array $definition)
    {
        parent::__construct($definition);
    }
}