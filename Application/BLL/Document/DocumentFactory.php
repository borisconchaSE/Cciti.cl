<?php

namespace Application\BLL\Document;

use Application\BLL\DataTransferObjects\Administracion\ClienteDto;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Mantenciones\MantencionDto;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\Definitions\FotoDocumentDefinition;
use Intouch\Framework\Document\GenericDocument;
use Intouch\Framework\Document\GenericFileExtensionEnum;
use Intouch\Framework\Environment\Session;

class DocumentFactory {

    /**
     * Retorna una instancia de GenericDocument o una de sus clases derivadas
     * 
     * @param string $proceso
     * @param string $tipoDocumento
     * @param string $params
     * 
     * @return null|GenericDocument
     */
    public static function CreateDocument(string $proceso, string $tipoDocumento, array $params = null) {

        switch ($proceso) {
            case ProcessTypeEnum::PROCESS_EVIDENCIA:
                switch ($tipoDocumento) {
                    case DocumentTypeEnum::FOTO:
                        
                        // Tipo de documento, foto para mantencion => FotoDocument
                        if (!isset($params) || !isset($params['Filename'])) {
                            $filename = GenericDocument::GenerateRandomFilename(GenericFileExtensionEnum::EXT_JPG, '');
                        }
                        else {
                            $filename = $params['Filename'];
                        }

                        $fotoDoc = new FotoDocument(new FotoDocumentDefinition([
                            "IdMonMontaje"      => $params['IdMonMontaje'],
                            "DocumentType"      => DocumentTypeEnum::FOTO,
                            "Filename"          => $filename,
                            "Authorization"     => $params['Authorization']
                        ]));

                        return $fotoDoc;
                    default:
                        return null;
                }
                break;
            case ProcessTypeEnum::PROCESS_SYSTEM:
                switch ($tipoDocumento) {
                    case DocumentTypeEnum::AVATAR:

                        $usuario = null;
                        if (!isset($params) || !isset($params['IdCliente']) || !isset($params['IdUsuario'])) {

                            if (isset(Session::Instance()->usuario) && (Session::Instance()->usuario instanceof UsuarioDto)) {
                                $usuario = Session::Instance()->usuario;
                            }
                        }
                        else {
                            $usuario = new UsuarioDto();
                            $usuario->IdCliente = $params['IdCliente'];
                            $usuario->IdUsuario = $params['IdUsuario'];
                            $usuario->Genero    = $params['Genero'];
                            $usuario->Avatar    = $params['Avatar'];
                        }

                        if ($usuario) {
                            return AvatarDocument::NewFromUsuario($usuario);
                        }
                        else {
                            return null;
                        }
                        

                        break;
                    case DocumentTypeEnum::LOGO:
                        $usuario = null;
                        if (!isset($params) || !isset($params['Cliente'])) {
                            if (isset(Session::Instance()->usuario) && isset(Session::Instance()->usuario->Cliente)) {
                                return LogoDocument::NewFromCliente(Session::Instance()->usuario->Cliente);
                            }
                            else {
                                return null;
                            }
                        }
                        else {
                            return LogoDocument::NewFromCliente($params['Cliente']);
                        }

                        break;
                    default:
                        return null;
                }
                break;
            default:
                return null;
        }

        return null;
    }
}