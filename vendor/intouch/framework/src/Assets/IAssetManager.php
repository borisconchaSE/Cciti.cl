<?php

namespace Intouch\Framework\Assets;

interface IAssetManager {

    function SaveAsset(Resource $asset): bool;
    function GetAsset(Resource $asset): Resource;
    function GetAssetUri(Resource $asset): Resource;

}