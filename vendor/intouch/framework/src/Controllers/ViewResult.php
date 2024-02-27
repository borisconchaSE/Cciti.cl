<?php

namespace Intouch\Framework\Controllers;

use Intouch\Framework\Controllers\Result;

class ViewResult extends Result {

    public function __construct(
                        public $ViewContent,        
                        int $UserUniqueID, 
                        bool $IsSessionActive,
                        string $Request) {

        parent::__construct(
            ResultType: 'View',
            UserUniqueID: $UserUniqueID,
            IsSessionActive: $IsSessionActive,
            Request: $Request,
            ErrorCode: 0,
            ErrorMessage: '',
            DebugMessage: ''
        );
    }

}