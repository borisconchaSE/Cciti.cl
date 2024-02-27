<?php

namespace Intouch\Framework\Controllers;

use Intouch\Framework\Controllers\Result;

class ActionViewResult extends Result {

    public function __construct(
                        public $ViewContent,        
                        int $UserUniqueID, 
                        bool $IsSessionActive,
                        string $Request) {

        parent::__construct(
            ResultType: 'ActionView',
            UserUniqueID: $UserUniqueID,
            IsSessionActive: $IsSessionActive,
            Request: $Request,
            ErrorCode: 0,
            ErrorMessage: '',
            DebugMessage: ''
        );
    }

}