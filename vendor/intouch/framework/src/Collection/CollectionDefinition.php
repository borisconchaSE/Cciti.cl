<?php

namespace Intouch\Framework\Collection;

class CollectionDefinition extends GenericDefinition {
    public $Key = "";
    public $DtoName = "";
    public $Values = array();
    public $Indexes = array();

    public function __construct(array $definition)
    {
        parent::__construct($definition);
    }
}