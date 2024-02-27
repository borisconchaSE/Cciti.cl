<?php

namespace Intouch\Framework\Configuration;

use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Application\BLL\Services\Core\PerfilSvc;
use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;
use Intouch\Framework\Collection\GenericCollection;

#[CacheMulti, ConfigDetails(name: 'restrictions.config.json')]
class RestrictionConfig extends BaseConfig {

    public $IdRestriction = '';
    public $PositiveAction = '';
    public $NegativeAction = '';
    public $Roles = array();

    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public static function CalcularAccion($idRestriction, PerfilDto $perfil) {

        $configValues = self::Instance();

        $restrictions = new GenericCollection(
            Key: 'IdRestriction',
            DtoName: RestrictionConfig::class,
            Values: $configValues
        );

        // Buscar la restriccion
        if ($restrictions->Exists($idRestriction)) {
            $restriction = $restrictions->Find($idRestriction);

            $roles = $restriction->Roles;
            $positiveAction = $restriction->PositiveAction;
            $negativeAction = $restriction->NegativeAction;

            if (isset($roles) && isset($positiveAction) && isset($negativeAction)) {
                // Verificar si el perfil consultado tiene alguno de estos roles
                if (PerfilSvc::HasAnyRol($perfil, $roles)) {
                    return $positiveAction;
                }
                else {
                    return $negativeAction;
                }
            }
        }       
            
        return "Hide";        
    }
}