<?php

namespace Application\BLL\Document\Definitions;

use Intouch\Framework\Document\GenericDocumentDefinition;

class PlanDocumentDefinition extends GenericDocumentDefinition {

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdCliente = 0;
    public $IdMantencion = null;

    public function __construct(array $definition)
    {
        parent::__construct($definition);
    }
}