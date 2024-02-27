<?php

namespace Application\BLL\Document\Definitions;

use Intouch\Framework\Document\GenericDocumentDefinition;

class AvatarDocumentDefinition extends GenericDocumentDefinition {

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdUsuario = 0;

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $IdCliente = 0;

    public $Genero = null;
    public $Avatar = null;

    public function __construct(array $definition)
    {
        parent::__construct($definition);
    }
}