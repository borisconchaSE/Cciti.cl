<?php
namespace Application\Controllers\Assets;

use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\Document\GenericDocument;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Security\Encoder;
use Intouch\Framework\Security\Secure;

class DocumentosController extends BaseController {

    public function anyIndex($id = null) {

        $linkid = $this->GetRequest('linkid');

        // Buscar el documento
        $document = GenericDocument::NewFromEncodedLink($linkid);

        switch($document->GetDocumentDefinition()->DocumentType) {

            case DocumentTypeEnum::PLAN:
                // Verificar si el usuario tiene permisos para ver la imagen
                //
                $idCliente = Session::Instance()->usuario->Cliente->IdCliente;

                if (!isset($idCliente) || $idCliente != $document->GetDocumentDefinition()->IdCliente) {
                    $this->Redirect('accesonoautorizado');
                }

                // Obtener el archivo
                //
                $file = $document->LoadDocument();                
                $this->DownloadXml($file, $document->GetDocumentDefinition()->Filename);

                break;

            // Documentos que requieren sesión del cliente
            //
            case DocumentTypeEnum::LOGO:
                // Verificar si el usuario tiene permisos para ver la imagen
                //
                if ($document->Authorization != 0) {
                    $idCliente = Session::Instance()->usuario->Cliente->IdCliente;

                    if (!isset($idCliente) || $idCliente != $document->GetDocumentDefinition()->IdCliente) {
                        $this->Redirect('accesonoautorizado');
                    }
                }    

                // Obtener el archivo
                //
                $image = $document->LoadDocument();                
                $this->DisplayImage($image);

                exit;
                break;
            case DocumentTypeEnum::FOTO:

                // Verificar si se ha especificado el id de cliente
                //
                if ($document->Authorization != 0) {
                    $idCliente = Session::Instance()->usuario->Cliente->IdCliente;

                    if (!isset($idCliente) || $idCliente != $document->GetDocumentDefinition()->IdCliente) {
                        $this->Redirect('accesonoautorizado');
                    }
                }

                // Obtener el archivo
                //
                $image = $document->LoadDocument();                
                $this->DisplayImage($image);

                exit;
                break;

            case DocumentTypeEnum::AVATAR:

                // Verificar si el usuario tiene permisos para ver la imagen
                //
                $idCliente = Session::Instance()->usuario->Cliente->IdCliente;

                if (!isset($idCliente) || $idCliente != $document->GetDocumentDefinition()->IdCliente) {
                    $this->Redirect('accesonoautorizado');
                }

                // Obtener el archivo
                //
                $image = $document->LoadDocument();                
                $this->DisplayImage($image);

                exit;
                break;

            case DocumentTypeEnum::REPORTE:

                // Verificar si el usuario tiene permisos para ver el reporte
                //
                $idCliente = Session::Instance()->usuario->Cliente->IdCliente;

                if (!isset($idCliente) || $idCliente != $document->GetDocumentDefinition()->IdCliente) {
                    $this->Redirect('accesonoautorizado');
                }

                // Obtener el archivo
                //
                $pdf = $document->LoadDocument();                
                $this->DownloadPdf($pdf, $document->GetDocumentDefinition()->Filename);

                exit;
                break;
                
            // Documentos publicamente disponibles
            //
            case DocumentTypeEnum::IMAGEN:
                exit;
                break;

            default:
                exit;
                break;
        }

        if (isset($document)) {

        }
        else {
            return null;
        }

    }

}