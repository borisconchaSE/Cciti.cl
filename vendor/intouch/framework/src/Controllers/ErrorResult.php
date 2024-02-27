<?php

namespace Intouch\Framework\Controllers;

class ErrorResult extends Result {

    public function __construct(        
                        int $UserUniqueID, 
                        bool $IsSessionActive,
                        string $Request,
                        int $ErrorCode,
                        string $ErrorMessage,
                        string $DebugMessage
    ) {
        parent::__construct(
            ResultType: 'Error',
            UserUniqueID: $UserUniqueID,
            IsSessionActive: $IsSessionActive,
            Request: $Request,
            ErrorCode: $ErrorCode,
            ErrorMessage: $ErrorMessage,
            DebugMessage: $DebugMessage 
        );
    }
}