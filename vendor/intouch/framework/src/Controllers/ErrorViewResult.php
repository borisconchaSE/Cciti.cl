<?php

namespace Intouch\Framework\Controllers;

class ErrorViewResult extends Result {

    public function __construct(   
                        public $ViewContent,                     
                        int $UserUniqueID, 
                        bool $IsSessionActive,
                        string $Request,
                        int $ErrorCode,
                        string $ErrorMessage,
                        string $DebugMessage
    ) {
        parent::__construct(
            ResultType: 'ErrorView',
            UserUniqueID: $UserUniqueID,
            IsSessionActive: $IsSessionActive,
            Request: $Request,
            ErrorCode: $ErrorCode,
            ErrorMessage: $ErrorMessage,
            DebugMessage: $DebugMessage 
        );
    }
}