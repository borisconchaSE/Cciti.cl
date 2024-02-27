<?php

namespace Application\BLL\Document\Definitions;

use Intouch\Framework\Document\GenericDocumentDefinition;

class FotoDocumentDefinition extends GenericDocumentDefinition {

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdCliente = 0;
    public $IdMonMontaje = null;
    public $IdDetencion = null;
    public $IdBitacora = null;

    // Autorizacion para acceso por API
    //
    public $Authorization = 0;

    public function __construct(array $definition)
    {
        parent::__construct($definition);
    }
}