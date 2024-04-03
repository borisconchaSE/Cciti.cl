<?php

namespace Application\BLL\Helpers;

use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\Document\AvatarDocument;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\DocumentFactory;
use Application\BLL\Document\ProcessTypeEnum;

class AvatarHelper {

    /**
     * Obtiene la URL directa para un Avatar
     * 
     * @param int $idCliente
     * @param int $idUsuario
     * @param string $avatar
     * @param int $genero
     * 
     * @return null|string
     */
    public static function GetAvatar($idCliente = null, $idUsuario = null, $avatar = null, $genero) {

        $genero2 = null;
        $newAvatar = "/assets/avatar/default/USERF.png";

        $avatarDoc = DocumentFactory::CreateDocument(ProcessTypeEnum::PROCESS_SYSTEM, DocumentTypeEnum::AVATAR, [
            "IdCliente"     => $idCliente,
            "IdUsuario"     => $idUsuario,
            "Avatar"        => $avatar,
            "Genero"        => $genero2
        ]);

        if (isset($avatarDoc)) {
            if($genero == 2){
                $avatar = $newAvatar;
            }else{
                $avatar = $avatarDoc->GetUrl();
            }

            return $avatar;
        }
        else {
            return "";
        }
    }

    public static function GetDefaultAvatar($idCliente = -1, $genero = null) {

        $avatarDoc = DocumentFactory::CreateDocument(ProcessTypeEnum::PROCESS_SYSTEM, DocumentTypeEnum::AVATAR, [
            "IdCliente"     => $idCliente,
            "IdUsuario"     => -1,
            "Avatar"        => '',
            "Genero"        => $genero
        ]);

        $avatar = $avatarDoc->GetUrl();

        return $avatar;
    }
}