<?php

namespace Intouch\Framework\Dao {
    
    class BindVariable {        
        public $Field = '';
        public $Operator = '';
        public $Operand1 = '';
        public $Operand2 = '';
        
        function __construct($field, $operator, $operand1, $operand2 = '') {            
            $this->Field = $field;
            $this->Operator = $operator;
            $this->Operand1 = $operand1;
            $this->Operand2 = $operand2;            
        }
    }    
    
}