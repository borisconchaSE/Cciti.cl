<?php

namespace Application\Controllers\Api;

use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Controllers\BaseController;
use Application\BLL\Services\Core\UsuarioSvc;
use Application\BLL\BusinessObjects\Core\UsuarioBO;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\Filters\UserLoginFilterDto;
use Application\BLL\DataTransferObjects\WS\CredentialDataWS;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\DocumentFactory;
use Application\BLL\Document\ProcessTypeEnum;
use Application\BLL\Filters\IdiomaFilterDto;
use Application\BLL\Filters\MensajeFilterDto;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\View\Display;
use Intouch\Framework\Mensajes\Mensaje;
use Intouch\Framework\Annotation\Attributes\ReturnActionResult;
use Intouch\Framework\Annotation\Attributes\ReturnActionViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Dao\OperatorEnum;
use Intouch\Framework\Environment\Request;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Intouch\Framework\Environment\Session;

#[Route(Authorization: 'APP_KEY', AppKey: 'dummie')]
class CoreController extends BaseController
{
    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }

    #[Route(Methods: ['POST'], RequireSession:false)]
    #[ReturnActionResult]
    public function Test(String $loginName, String $password, String $otra = '', String $ultima = '') 
    {
        return 'OK';
    }

    #[Route(Methods: ['GET', 'POST'], RequireSession:false)]
    #[ReturnActionResult]
    public function Login(UserLoginFilterDto $userLogin = null)
    {
        if (!isset($userLogin)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_MISSING_PARAMETER, message: 'Ocurrió un problema al autenticar al usuario', debugMessage: 'Existe un problema con la llamada al servicio. El parametro no corresponde');
            return;
            //return $this->RenderJsonResult(1, "Existe un problema con la llamada al servicio. El nombre del parametro no corresponde", "Ocurrió un problema al autenticar al usuario", null);
        }

        $usuarioSvc = new UsuarioSvc(ConnectionEnum::CORE);
        $usuario = $usuarioSvc->Login($userLogin->LoginName, $userLogin->Password);

        if (!$usuario) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DEFAULT, message: 'El nombre de usuario o la contraseña no son correctos. Corrija la información e inténtelo nuevamente.', debugMessage: 'Credenciales invalidas');
            return;
        }

        // Asignacion previa para carga de funcionalidades        
        Session::Instance()->usuario = $usuario;        

        $usuarioBO = new UsuarioBO();        
        $usuarioFull = $usuarioBO->LoadUsuarioFull($usuario);

        // Almacenar al usuario en sesion
        Session::Instance()->usuario = $usuarioFull;
        Session::Instance()->loginOrigin = "web";        

        $data = new \StdClass();
        $data->UrlRedirect = $usuarioFull->Perfil->LandingPage;

        // Cuando se ha iniciado una nueva sesión, las variables de TipoProducto e Idioma
        // pueden haber cambiado respecto de los valores por defecto encontrados en la pagina de login
        // Por lo tanto debemos forzar al Singleton de Display a actualizarse
        Display::GetRenderer('', true);

        return $data;
    }
 

    #[Route(Methods: ['POST'], RequireSession:false)]
    #[ReturnActionResult]
    public function VerifyLogin(UserLoginFilterDto $userLogin) {

        if (!isset($userLogin)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_MISSING_PARAMETER, message: 'al autenticar al usuario');
            return;
        }

        $usuarioSvc = new UsuarioSvc(ConnectionEnum::CORE);
        $usuario = $usuarioSvc->Login($userLogin->LoginName, $userLogin->Password);

        if (!isset($usuario) || !$usuario) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DEFAULT, message: 'El nombre de usuario o la contraseña no son correctos. Corrija la información e inténtelo nuevamente.', debugMessage: 'error login usuario');
            return;
        }

        $usuarioBO = new UsuarioBO();
        $usuarioFull = $usuarioBO->LoadUsuarioFull($usuario);

        $data = new \StdClass();
        $data->Usuario = $usuarioFull;

        return $data;
    }

    #[Route(Methods: ['POST'], RequireSession:false)]
    #[ReturnActionResult]
    public function Traducir(MensajeFilterDto $mensaje) {
        
        if (!isset($mensaje)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_MISSING_PARAMETER, message: 'al actualizar idioma');
            return;
        }

        if (Mensaje::ActualizarMensaje($mensaje->MessageId, $mensaje->Entries, Session::Instance()->idioma)) {
            return 0;
        }
        else {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DEFAULT, message: 'al actualizar idioma');
            return;
        }
    }

    #[Route(Methods: ['POST'], RequireSession:false)]
    #[ReturnActionResult]
    public function CambiarIdioma(IdiomaFilterDto $idioma) {

        Session::Instance()->idioma = $idioma->CodigoIdioma;

        // Actualizar los menus de funcionalidades en la sesión de usuario
        Mensaje::ActualizarIdiomaFuncionalidades(Session::Instance()->idioma, Session::Instance()->locale);

        return 0;
    }

    #[Route(Methods: ['POST'], RequireSession:false)]
    #[ReturnActionViewResult]
    public function Mensajes(MensajeFilterDto $mensaje) {

        if (!isset($mensaje)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_MISSING_PARAMETER, message: 'al obtener idiomas');
            return;
        }

        // Obtener la lista de idiomas existentes
        $idiomas = Mensaje::ObtenerIdiomas();

        // Obtener los mensajes correspondientes a este ID
        $mensajes = Mensaje::ObtenerMensajes($mensaje->MessageId);

        $data = new \StdClass();
        $data->Idiomas = $idiomas;
        $data->Mensajes = $mensajes;
        $data->Alternativo = $mensaje->Alternativo;

        return Display::GetRenderer('Core')->RenderView('mensajes', $data);
    }

    #[Route(Methods: ['POST'])]
    #[ReturnActionResult]
    public function Files() {

        if (!isset($_FILES) || count($_FILES) <= 0) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_MISSING_PARAMETER, message: 'al subir los archivos');
            return;
        }

        // Guardar el archivo
        $proceso        = Request::Instance()->proceso;
        $tipo           = Request::Instance()->tipo;   
        $observacion    = Request::Instance()->observacion;
        $idMontaje      = Request::Instance()->IdMonMontaje ?: Session::Instance()->IdMonMontajeEvidencia;
      


        if (isset($proceso) && isset($tipo)) {
            $proceso = strtolower($proceso);
            $tipo = strtolower($tipo);

            // Procesos de subida de archivos conocidos
            //
            switch($proceso) {
                // Procesos conocidos con subida de archivos               
                case ProcessTypeEnum::PROCESS_EVIDENCIA:
                case ProcessTypeEnum::PROCESS_DETENCION: //  todo for future implementation
                case ProcessTypeEnum::PROCESS_BITACORA: //  todo for future implementation
                    break;
                // No existe el tipo solicitado
                default:
                    throw new BusinessException(code: ExceptionCodesEnum::ERR_DEFAULT, message: 'al subir los archivos', debugMessage: 'Proceso no especificado');
                    return;
            }

            switch ($proceso . '-' . $tipo) {
                // Tipos de archivo conocidos para cada proceso
                case ProcessTypeEnum::PROCESS_EVIDENCIA .'-'. DocumentTypeEnum::FOTO:
                case ProcessTypeEnum::PROCESS_DETENCION .'-'. DocumentTypeEnum::FOTO: //  todo for future implementation
                case ProcessTypeEnum::PROCESS_BITACORA .'-'. DocumentTypeEnum::FOTO: //  todo for future implementation
                    break;
                // No existe el tipo solicitado
                default:
                    throw new BusinessException(code: ExceptionCodesEnum::ERR_DEFAULT, message: 'al subir los archivos', debugMessage: 'Proceso-Tipo no especificado');
                    return;              
            }
        }
        else {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DEFAULT, message: 'al subir los archivos', debugMessage: 'Proceso o Tipo no especificado');
            return;
        }

        // Obtener información de sesión necesarios
        //


        $idCliente = -1;

        // Obtener el id de cliente
        //
        $usuario = Session::Instance()->usuario;

       
        
        $filesResult = array();
        $fileIdx = 1;

        // Limpiar la lista de archivos de la sesión del usuario
        //
        if (isset(Session::Instance()->uploadedFiles))
            Session::Instance()->uploadedFiles = [];
        //unset($_SESSION['uploaded_files']);

        $fileUpload = 'fileUpload';
        if (isset($_FILES)) {
            foreach($_FILES as $n => $f) {
                $fileUpload = $n;
                break;
            }
        }

        $cantFiles = count($_FILES[$fileUpload]['name']);

        for ($idxFile = 0; $idxFile < $cantFiles; $idxFile++ ) {

            $FILE = [
                "name"      => $_FILES[$fileUpload]['name'][$idxFile],
                "type"      => $_FILES[$fileUpload]['type'][$idxFile],
                "tmp_name"  => $_FILES[$fileUpload]['tmp_name'][$idxFile],
                "error"     => $_FILES[$fileUpload]['error'][$idxFile],
                "size"      => $_FILES[$fileUpload]['size'][$idxFile],
            ];

            $fileError = false;

            $filename = $FILE['name'];

            if (!isset($FILE['error']) || is_array($FILE['error'])) {

                array_push($filesResult, array([
                    "FileId"        => 0,
                    "FileName"      => $filename, 
                    "ResultError"   => "Parametros de archivo invalidos",
                    "IdMonMontaje"  => $idMontaje
                ]));

                $fileError = true;
            }
            else {
                switch ($FILE['error']) {
                    case UPLOAD_ERR_OK:                        
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        array_push($filesResult, array([
                            "FileId"        => 0,
                            "FileName"      => $filename, 
                            "ResultError"   => "No se envio ningun archivo",
                            "IdMonMontaje"  => $idMontaje
                        ]));
                        
                        $fileError = true;
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        array_push($filesResult, array([
                            "FileId"        => 0,
                            "FileName"      => $filename, 
                            "ResultError"   => "Se excedio el tamaño maximo permitido para el archivo",
                            "IdMonMontaje"  => $idMontaje
                        ]));
                        
                        $fileError = true;
                        break;
                    default:
                        array_push($filesResult, array([
                            "FileId"        => 0,
                            "FileName"      => $filename, 
                            "ResultError"   => "Oucrri&oacute; un error imprevisto, por favor intente nuevamente",
                            "IdMonMontaje"  => $idMontaje
                        ]));
                        
                        $fileError = true;
                        break;
                }

                if (!$fileError) {

                    // Generar el documento asociado al proceso y tipo de archivo
                    $document = DocumentFactory::CreateDocument($proceso, $tipo, [
                        "IdCliente"         => $idCliente,
                        "IdMonMontaje"      => $idMontaje
                    ]);

                    // Si es de tipo foto, intentamos corregir la orientacion
                    if (isset($tipo) && $tipo == 'foto') {
                        // Aplicar correccion de orientación
                        $filename = $FILE['name'];
                        $filePath = $FILE['tmp_name'];
                        $exif = exif_read_data($FILE['tmp_name']);

                        $ext = strtolower(pathinfo($FILE['name'], PATHINFO_EXTENSION));

                        if ($ext == "png") {
                            $imageResource = imagecreatefrompng($filePath);
                        }
                        else if ($ext == "jpg" || $ext == "jpeg") {
                            $imageResource = imagecreatefromjpeg($filePath);
                        }

                        if (!empty($exif['Orientation'])) {                            
                            switch ($exif['Orientation']) {
                                case 3:
                                $image = imagerotate($imageResource, 180, 0);
                                break;
                                case 6:
                                $image = imagerotate($imageResource, -90, 0);
                                break;
                                case 8:
                                $image = imagerotate($imageResource, 90, 0);
                                break;
                                default:
                                $image = $imageResource;
                            } 
                        }
                        else {
                            $image = $imageResource;
                        }

                        // Guardar la foto corregida
                        if ($image !== false)
                            imagejpeg($image, $filePath, 90);

                        if ($imageResource !== false)
                            imagedestroy($imageResource);

                        try {
                            if (isset($image) && $image !== false)
                                imagedestroy($image);
                        }
                        catch (\Exception $ex) {

                        }
                    }

                    // Copiar el archivo a su destino definitivo
                    //$targetFileName = (new \DateTime())->format('YmdHis') . "_" . $fileIdx . "_" . $filename;
                    $targetFileName = $document->GetFilename();

                    $targetFolder = $document->GetFolder();

                    // Crear si no existe
                    if (!file_exists($targetFolder)) {
                        mkdir($targetFolder, 0777, true);
                    }

                    //$target = SITE_ROOT. $uploads_dir . "/" . $targetFileName;
                    $target = $document->GetPath();

                    if (!$result = move_uploaded_file($FILE['tmp_name'], $target)) {

                        array_push($filesResult, array([
                            "FileId"        => $fileIdx,
                            "FileName"      => $filename,
                            "ResultError"   => "Oucrri&oacute; un error imprevisto, por favor intente nuevamente",
                            "IdMonMontaje"  => $idMontaje
                        ]));
                        
                        $fileError = true;
                    }
                    else {

                        chmod($target, 0775);
                        chown($target, 'www-data:www-data');

                        // Agregar el archivo a la sesion del usuario
                        if (!isset(Session::Instance()->uploadedFiles)) {
                            Session::Instance()->uploadedFiles = array();
                        }

                        $upl = Session::Instance()->uploadedFiles;

                        $upl[$fileIdx] = [
                            "FileId" => $fileIdx,
                            "Document" => $document
                        ];

                        Session::Instance()->uploadedFiles = $upl;

                        array_push($filesResult, array([
                            "FileId"        => $fileIdx,
                            "FileName"      => $filename,
                            "ResultError"   => "",
                            "IdMonMontaje"  => $idMontaje
                        ]));

                        $fileIdx++;
                    }
                }

            }
        }

        return $filesResult;
    }

}