<?php

namespace Intouch\Framework\Assets\Managers;

use Intouch\Framework\Assets\IAssetManager;
use Intouch\Framework\Assets\Resource;

class LocalAssetManager implements IAssetManager {
    
    public function __construct(
        public string $rootPath = '',
        public string $rootUri = ''
    ) {}
    
	/**
	 *
	 * @param Resource $asset
	 *
	 * @return bool
	 */
	function SaveAsset(Resource $asset): bool {
        return false;
	}
	
	/**
	 *
	 * @param Resource $asset 
	 *
	 * @return Resource
	 */
	function GetAsset(Resource $asset): Resource {

        $path = $this->rootPath . '/' . $asset->location . '/' . $asset->name;

        if (file_exists($path)) {
            $content = file_get_contents($path);
            $asset->content = $content;
        }
        else {
            $asset->content = null;
        }

        return $asset;
	}
	
	/**
	 *
	 * @param Resource $asset 
	 *
	 * @return Resource
	 */
	function GetAssetUri(Resource $asset): Resource {
        $asset->uri = $this->rootUri . '/' . $asset->location . '/' . $asset->name;

        return $asset;
	}
}
