<?php

namespace Intouch\Framework\Assets\Managers;

use Intouch\Framework\Assets\IAssetManager;
use Intouch\Framework\Assets\Resource;
use Intouch\Framework\Assets\Entities\ImageResource;
use Intouch\Framework\Assets\Entities\PdfResource;

class CloudFrontAssetManager implements IAssetManager {

	public function __construct(
		public string $apiEndpoint = '',
		public bool $useSignedUrls = false,
		public string $mainFolder = ''
	) {}
    
	/**
	 *
	 * Esta funcion no se puede llamar
	 * 
	 * @param Resource $asset
	 *
	 * @return bool
	 */
	function SaveAsset(Resource $asset): bool {
        throw new \Exception('CloudFront sólo puede utilizarse en operaciones de lectura');
	}
	
	/**
	 * Recupera el elemento desde CloudFront, desde la ruta especificada por "endpoint"/"location"/"name"
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
	 *
	 * @param Resource $asset 
	 *
	 * @return Resource
	 */
	function GetAssetUri(Resource $asset): Resource {

		if ($asset instanceof ImageResource) {
			$asset->uri = $this->GetImageUri($asset);
		}
		else if ($asset instanceof PdfResource) {
			$asset->uri = $this->GetPdfUri($asset);
		}

		return $asset;
	}

	function GetImageUri(ImageResource $asset): null|string {

		$resource = new \stdClass();
		$resource->bucket = "website-assets-shutdown-cl";
		$resource->key = $this->mainFolder . ( ($asset->location != '') ? '/' . $asset->location : '');

		if ($asset->width > 0) {
			$resource->edits = new \stdClass();
			$resource->edits->resize = new \stdClass();
			$resource->edits->resize->width = 100;			
			$resource->edits->resize->fit = "inside";
		}

		$json = str_replace("\\", "", json_encode($resource));
		$encoded = base64_encode($json);

		return $this->apiEndpoint . '/' . $encoded;
	}

	function GetPdfUri(PdfResource $asset): null|string {

		$endpoint = $this->apiEndpoint . '/' . $asset->location;

		// Construct the CloudFront request
		return null;
	}

	private function SolicitarRecurso($ruta) {

	}
}