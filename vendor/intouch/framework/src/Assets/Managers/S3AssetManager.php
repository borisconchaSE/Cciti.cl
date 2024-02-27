<?php

namespace Intouch\Framework\Assets\Managers;

use Intouch\Framework\Assets\IAssetManager;
use Intouch\Framework\Assets\Resource;

class S3AssetManager implements IAssetManager {

	public function __construct(
		public string $configFilePath = '',
		public string $bucket = ''
	) {}

	/**
	 * Guarda el elemento en un bucket de S3, en la ruta especificada por "location" (ej: fotos/clientes/28/mantencion/1653)
	 * El nombre del elemento será lo que se haya especificado en "name" (ej: image-1.jpeg)
	 * 
	 * @param Resource $asset
	 *
	 * @return bool
	 */
	function SaveAsset(Resource $asset): bool {


        return true;
	}
	
	/**
	 * Recupera el elemento desde un bucket de S3, desde la ruta especificada por "location"/"name"
	 * Devuelve todos los bits leidos en el campo $content del Resource
	 * 
	 * @param Resource $asset 
	 *
	 * @return Resource
	 */
	function GetAsset(Resource $asset): Resource {


		return $asset;
    }
    
	/**
	 * Devuelve el Endpoint del elemento en el bucket de S3 desde la ruta especificada por "location"/"name"
	 * Devuelve el asset con la ruta de acceso en el campo $uri
	 * Obs: el bucket deberá tener configurado el acceso público de lectura en AWS.
	 * 
	 * @param Resource $asset 
	 *
	 * @return Resource
	 */
	function GetAssetUri(Resource $asset): Resource {
		
		return $asset;
	}
    
}