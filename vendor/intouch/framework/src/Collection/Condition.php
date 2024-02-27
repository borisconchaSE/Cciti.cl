<?php

namespace Intouch\Framework\Collection;

class Condition extends GenericDefinition {

    /** validation = @prop != "" */
    public $FieldName = "";
    /** validation = @prop != "" */
    public $Operator = "";
    /** validation = isset(@prop) */
    public $Value = "";
    /** validation = @prop != "" */
    public $FieldType = "";

    public function __construct(array $definition) {
        parent::__construct($definition, GenericValidator::class);
    }

}