<?php

namespace Intouch\Framework\MailHelper;

class MailConfig {

    public function __construct (
        public $SmtpServer = '',
        public $SmtpUser = '',
        public $SmtpPassword = '',
        public $SmtpPort = 0
    ) {

    }
    
}