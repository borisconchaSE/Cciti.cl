<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Core\CacheableSingleton;
use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\CacheSingle;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;

#[CacheSingle, ConfigDetails(name: 'system.config.json')]
class SystemConfig extends BaseConfig {
    
    public $ApplicationName = '';
    public $ApplicationDescription = '';
    public $ApplicationCopyright = '';
    public $ApplicationWebsite = '';
    public $ApplicationWebsiteLink = '';
    public $ImageEndPoint = '';
    public $PdfToolPath = '';
    public $Watermark = '';
    public $DemoUser = '';
    public $Languages = '';
    public $Producto = '';
    public $ShowLanguageSelector = true;
    public $ReportServerURI = '';
    public $ServerUsername = '';
    public $ServerPassword = '';    

    public $CloudFrontImageApiEndpoint = '';
    public $CloudFrontImageMainFolder = '';

    public $CloudFrontAssetApiEndpoint = '';
    public $CloudFrontAssetMainFolder  = '';

    public $LocalEnvironment = true;

    public $SmtpServer      = '';
    public $SmtpUser        = '';
    public $SmtpPassword    = '';
    public $SmtpPort        = '';
    public $SmtpSender      = '';

    public $SendGridApiKey        = "";
    public $MailReceiptCrear      = "";
    public $MailReceiptAprobar    = "";
    public $MailReceiptRechazar   = "";
    public $MailReceiptAsignar    = "";
    public $MailReceiptRendir     = "";
    public $MailReceiptAdmin      = "";

    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }
}