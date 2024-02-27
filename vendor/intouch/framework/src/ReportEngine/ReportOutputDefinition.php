<?php

namespace Intouch\Framework\ReportEngine;

use Intouch\Framework\Collection\GenericDefinition;

class ReportOutputDefinition extends GenericDefinition {

    /** 
     * required 
     * validation = isset(@prop) && @prop > 0 */
    public $OutputType = null;

    /** 
     * required 
     * validation = isset(@prop) */
    public $SaveReport = false;

    public $ReportFolder = null;
    
    public $ReportFilename = null;

    function __construct(array $definition = array()) {
        parent::__construct($definition);
    }
}