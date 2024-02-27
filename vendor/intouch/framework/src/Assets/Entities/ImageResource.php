<?php

namespace Intouch\Framework\Assets\Entities;

use Intouch\Framework\Assets\Resource;

class ImageResource extends Resource {

    public function __construct(
        string $id, 
        string $name, 
        string $localpath = '', 
        string $location = '',
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
    ) {

        parent::__construct($id, $name, $localpath, $location);
    }

}