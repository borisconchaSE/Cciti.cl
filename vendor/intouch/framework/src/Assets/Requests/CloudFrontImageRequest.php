<?php

namespace Intouch\Framework\Assets\Requests;

class CloudFrontImageRequest {

    public function __construct(
        public string $bucket,
        public string $key,
        public int $width = 0,
        public int $height = 0,
        public string $resizeMode = 'Disabled',
        public string $fillColor = '',
        public string $backgroundColor = '',
        public bool $grayscale = false,
        public bool $flip = false,
        public bool $flop = false,
        public bool $negative = false,
        public bool $flatten = false,
        public bool $normalize = false,
        public string $rgb = '',
        public bool $smartCropping = false,
        public int $focusIndex = 0,
        public int $cropPadding = 0
    ) {}
    
}