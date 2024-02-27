<?php

namespace Intouch\Framework\ReportEngine;

use Intouch\Framework\Collection\GenericDefinition;

class ReportDataset extends GenericDefinition {

    /** 
     * required 
     * validation = is_array(@prop) && count(@prop) > 0 */
    public $DataLocation = null;

    /** 
     * required 
     * validation = !isset(@prop) || (is_array(@prop)) */
    public $Parameters = null;

    function __construct(array $definition = array()) {
        parent::__construct($definition);
    }
}