<?php

namespace Intouch\Framework\Document;

use Intouch\Framework\Collection\GenericDefinition;

class GenericDocumentDefinition extends GenericDefinition {

    public $DocumentType = 0;
    public $Filename = '';

    public function __construct(array $definition)
    {
        parent::__construct($definition);
    }
}