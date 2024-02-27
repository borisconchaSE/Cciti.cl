<?php

namespace Application\Resources;

use Intouch\Framework\Assets\Managers\{CloudFrontAssetManager, LocalAssetManager, S3AssetManager};
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Assets\IAssetManagerFactory;
use Intouch\Framework\Assets\IAssetManager;

/** Construye el administrador de recursos necesario según environment, tipo de recurso y tipo de operacion */
class AssetManagerFactory implements IAssetManagerFactory {

    /** Obtener un asset manager para lectura de imagenes */
    public function GetImageReaderAM():  IAssetManager {

        $config = SystemConfig::Instance();

        if ($config->LocalEnvironment) {
            return new LocalAssetManager(rootPath: SITE_ROOT . '/public/images', rootUri: SITE_URL . '/images');
        }
        else {
            return new CloudFrontAssetManager(apiEndpoint: $config->CloudFrontImageApiEndpoint, mainFolder: $config->CloudFrontImageMainFolder, useSignedUrls: false);
        }
    }

    /** Obtener un asset manager para lectura de js y css de plugins */
    public function GetAssetReaderAM():  IAssetManager {

        $config = SystemConfig::Instance();

        if ($config->LocalEnvironment) {
            return new LocalAssetManager(rootPath: SITE_ROOT . '/public', rootUri: SITE_URL . '');
        }
        else {
            return new CloudFrontAssetManager(apiEndpoint: $config->CloudFrontAssetApiEndpoint, useSignedUrls: false);
        }
    }

}