<?php

namespace Application\BLL\Document;

use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\Document\Definitions\AvatarDocumentDefinition;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Intouch\Framework\Document\GenericDocument;

class AvatarDocument extends GenericDocument {

    public function __construct(AvatarDocumentDefinition $definition, $base64Image = null)
    {
        parent::__construct($definition, $base64Image);
    }

    /**
     * Obtiene un AvatarDocument desde la información del usuario
     * 
     * @param UsuarioDto $usuario
     * 
     * @return null|AvatarDocument
     */
    public static function NewFromUsuario(UsuarioDto $usuario) {

        if (!isset($usuario))
            return null;

        $definition = new AvatarDocumentDefinition([
            "IdUsuario"      => $usuario->IdUsuario,
            "IdCliente"      => $usuario->IdCliente,
            "Genero"         => $usuario->Genero,
            "DocumentType"   => DocumentTypeEnum::AVATAR,
            "Filename"       => isset($usuario->Contacto->Avatar) ? $usuario->Contacto->Avatar : ''
        ]);

        return new AvatarDocument($definition);
    }

    public function GetFolder() {

        if ($this->DocumentDefinition instanceof AvatarDocumentDefinition)
            if (isset($this->DocumentDefinition->Filename) && $this->DocumentDefinition->Filename != '')
                return __DIR__ . '/../../FileData/avatar/clientes/' . $this->DocumentDefinition->IdCliente;
            else
                return __DIR__ . '/../../FileData/avatar/default';
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetFolderUrl() {

        if ($this->DocumentDefinition instanceof AvatarDocumentDefinition)
            if (isset($this->DocumentDefinition->Filename) && $this->DocumentDefinition->Filename != '')
                return '/assets/avatar/clientes/' . $this->DocumentDefinition->IdCliente;
            else
                return '/assets/avatar/default';
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetFilename()
    {
        if ($this->DocumentDefinition instanceof AvatarDocumentDefinition)

            if (isset($this->DocumentDefinition->Filename) && $this->DocumentDefinition->Filename != '')
                return $this->DocumentDefinition->Filename;
            else {
                if (isset($this->DocumentDefinition->Genero)) {
                    switch ($this->DocumentDefinition->Genero) {
                        case 0: return "USERF.png";
                        case 1: return "USER.png";
                        default: return "USER.png";
                    }
                }
                else {
                    return "USER.png";
                }
            }
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }        
    }

    public function GetUrl() {

        // Cambiamos a URL directa
        return $this->GetFolderUrl() . '/'
            . $this->GetFilename();

        if ($this->DocumentDefinition instanceof AvatarDocumentDefinition)
            return '/assets/documentos?linkid=' . $this->GetEncodedLink();
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }

    public function GetHttpUrl() {

        // Cambiamos a URL directa
        return $this->GetFolderUrl() . '/'
            . $this->GetFilename();

        if ($this->DocumentDefinition instanceof AvatarDocumentDefinition)
            return SITE_URL_HTTP . '/assets/documentos?linkid=' . $this->GetEncodedLink();
        else {
            throw new \Exception('La definicion de documento recibida no corresponde a la instancia entregada al constructor');
        }

    }
    
}