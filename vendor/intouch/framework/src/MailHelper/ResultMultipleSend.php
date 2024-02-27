<?php

namespace Intouch\Framework\MailHelper;

class ResultMultipleSend {

    public function __construct (
        public $EnviosCorrectos,
        public $EnviosIncorrectos,
        public $CantidadEnviosCorrectos,
        public $CantidadEnviosIncorrectos,
    ) {

    }
}


