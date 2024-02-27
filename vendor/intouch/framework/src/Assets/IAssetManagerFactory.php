<?php

namespace Intouch\Framework\Assets;

interface IAssetManagerFactory {

    /** Obtener un asset manager para lectura de imagenes */
    function GetImageReaderAM(): IAssetManager;

    /** Obtener un asset manager para lectura de js y css de plugins */
    function GetAssetReaderAM(): IAssetManager;

}