<?php

namespace Intouch\Framework\Controllers;

use Intouch\Framework\Controllers\Result;

class CacheTableDataResult extends Result {

    public function __construct(
                        public $Result,        
                        int $UserUniqueID, 
                        bool $IsSessionActive,
                        string $Request) {

        parent::__construct(
            ResultType: 'CacheTableDataResult',
            UserUniqueID: $UserUniqueID,
            IsSessionActive: $IsSessionActive,
            Request: $Request,
            ErrorCode: 0,
            ErrorMessage: '',
            DebugMessage: ''
        );
    }

}