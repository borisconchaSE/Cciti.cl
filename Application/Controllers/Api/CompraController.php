<?php

namespace Application\Controllers\Api;

use Application\BLL\Services\Core\marcaSvc;
use Application\BLL\Services\Core\modeloSvc;
use Application\BLL\BusinessObjects\Core\CompraBO;
use Application\BLL\Services\Core\empresaSvc;
use Application\BLL\Services\Core\estadoFCSvc;
use Application\BLL\Services\Core\estadoOCSvc;
use Application\BLL\Services\Core\estadosactivosSvc;
use Application\BLL\Services\Core\proveedorSvc;
use Application\BLL\Services\Core\tipoproductoSvc;
use Application\Configuration\ConnectionEnum;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Controllers\BaseController; 
use Intouch\Framework\Annotation\Attributes\ReturnActionResult;
use Intouch\Framework\Annotation\Attributes\ReturnActionViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Intouch\Framework\View\Display;

#[Route(Authorization: 'APP_KEY', AppKey: 'dummie')] 
class CompraController extends BaseController
{
    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }

    // FUNCION PARA LLAMAR EL POPUP PARA AGREGA UN NUEVO PRODUCTO
    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupAgregarProducto($identificador) 
    {

        // PROCEDEMOS A BUSCAR LA INFORMACIÓN QUE USAREMOS EN LA VISTA
        

        // OBTENEMOS TODAS LAS MARCAS EXISTENTES EN LA BBDD
        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS TODOS LOS MODELOS EXISTENTES EN LA BBDD
        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LAS EMPRESAS EXISTENTES EN LA BBDD
        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS PROVEEDORES EXISTENTES EN LA BBDD
        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS ESTADOS OC (ORDEN COMPRA) EXISTENTES EN LA BBDD
        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS ESTADOS FC (FACTURA COMPRA) EXISTENTES EN LA BBDD
        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS TIPOS DE PRODUCTO EXISTENTES EN LA BBDD
        if($identificador == 'Agregar Tonner'){

            $DatosTipo          =   (new CompraBO(ConnectionEnum::TI))->GetTipo($identificador);

        }elseif($identificador == 'Agregar Activo'){

            $DatosTipo          =   (new CompraBO(ConnectionEnum::TI))->GetTipo($identificador);

        }else{

            $DatosTipo          =   (new CompraBO(ConnectionEnum::TI))->GetTipo($identificador);

        }

        // OBTENEMOS LOS ESTADOS EXISTENTES EN BBDD
        $DatosActivo        =   (new estadosactivosSvc(ConnectionEnum::TI))->GetAll();

        // GENERAMOS UN ARRAY CON LOS DATOS PARA LA VISTA

        $data   =  (object) [
            "DatosMarca"            =>  $DatosMarca,
            "DatosModelo"           =>  $DatosModelo,
            "DatosEmpresa"          =>  $DatosEmpresa,
            "DatosProveedor"        =>  $DatosProveedor,
            "DatosOC"               =>  $DatosOC,
            "DatosFC"               =>  $DatosFC,
            "DatosTipo"             =>  $DatosTipo,
            "DatosActivo"           =>  $DatosActivo

        ]; 

        // VALIDAMOS PARA QUE VISTA DE POPUP VA LA INFORMACION
        if($identificador == 'Agregar Tonner'){

            // RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Compra')->RenderView('PopupAgregarProducto',$data);

        }elseif($identificador == 'Agregar Activo'){
            // RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Generales')->RenderView('PopupAgregarGenerales',$data);

        }else{
            // RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Gastos')->RenderView('PopupAgregarGastos',$data);

        }     
    }

    // FUNCION PARA LLAMAR EL POPUP EDITAR COMPRA (TONNER, GASTO, ACTIVO)
    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupEditarCompra(int $IdO_C) 
    { 

        // LISTA PARA VALIDAR LA VISTA DE GASTOS
        $ListasGastos = [4,7];

        // PROCEDEMOS A BUSCAR LA INFORMACIÓN EN EL BO

        // BUSCAMOS LA INFORMACION DEL PRODUCTO
        $CompraDto          =   (new CompraBO())->GetCompra($IdO_C);

        // BUSCAMOS LA MARCA DEL PRODUCTO
        $Marca              =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('Descripcion',$CompraDto->marca);

        // BUSCAMOS EL MODELO DEL PRODUCTO
        $Modelo             =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('Descripcion',$CompraDto->modelo);

        // BUSCAMOS SU PROVEEDOR
        $Proveedor          =   (new proveedorSvc(ConnectionEnum::TI))->FindByForeign('idProveedor',$CompraDto->idProveedor);
        // $Proveedor          =   (new proveedorSvc(ConnectionEnum::TI))->GetBy(new BindVariable('idProveedor','=',$CompraDto->idProveedor));

        // OBTENEMOS LAS MARCAS DE LOS PRODUCTOS
        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS MODELOS DE LOS PRODUCTOS
        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LAS EMPRESAS DE LA COMPAÑIA
        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS PROVEEDORES EXISTENTES DE LA COMPAÑIA
        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS TODOS LOS ESTADOS OC
        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS TODOS LOS ESTADOS FC 
        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS TODOS LOS TIPOS DE PRODUCTO
        $DatosTipo          =   (new tipoproductoSvc(ConnectionEnum::TI))->GetAll();

        // OBTENEMOS LOS ESTADOS ACTIVOS
        $DatosActivo         =   (new estadosactivosSvc(ConnectionEnum::TI))->GetAll();





        // GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Compra"                =>  $CompraDto,
            "Marca"                 =>  $Marca,
            "Modelo"                =>  $Modelo,
            "DatosMarca"            =>  $DatosMarca,
            "DatosModelo"           =>  $DatosModelo,
            "DatosEmpresa"          =>  $DatosEmpresa,
            "DatosProveedor"        =>  $DatosProveedor,
            "DatosOC"               =>  $DatosOC,
            "DatosFC"               =>  $DatosFC,
            "DatosTipo"             =>  $DatosTipo,
            "Proveedor"             =>  $Proveedor,
            "DatosActivo"           =>  $DatosActivo,
        ];

        // VALIDAMOS A QUE VISTA VAN LOS DATOS
        if(in_array($CompraDto->idTipoProducto,$ListasGastos)){
            // RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Gastos')->RenderView('PopupEditarGastos',$dataView);
        }elseif($CompraDto->idTipoProducto == 1){
            // RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Compra')->RenderView('PopupEditarCompra',$dataView);
        }else{   
            // RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Generales')->RenderView('PopupEditarGenerales',$dataView);
        }
    } 

    // FUNCION QUE GUARDA A INFORMACION DEL NUEVO PRODUCTO
    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionResult]
    public function GuardarNuevoProducto($NuevoCompra) 
    {  

        // INSTANCIAMOS LA BO PARA LA NUEVA COMPRA
        $CompraBO          =   new CompraBO();

        // INSTANCIAMOS LA FUNCION QUE VA A GUARDAR EL NUEVO PRODUCTO O COMPRA
        $CompraDto         =   $CompraBO->GuardarProducto($NuevoCompra);

        // VALIDAMOS SI LOS DATOS VIENEN VACIOS
        if ( empty($CompraDto) ){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT, message:'ha ocurrido un error inesperado');
        }

        // RETORNAMOS LA INFORMACION PARA INYECTARLA DE VUELTA A LA VISTA DE LA TABLA
        if(isset($NuevoCompra->Proveedor)){

            $estadoactivo   =   (new estadosactivosSvc(ConnectionEnum::TI))->FindByForeign('IdEstadoActivo',$NuevoCompra->Estado_Activo);
            $Proveedor      =   (new proveedorSvc(ConnectionEnum::TI))->FindByForeign('idProveedor',$NuevoCompra->idProveedor);
            $TipoProduct    =   (new tipoproductoSvc(ConnectionEnum::TI))->FindByForeign('idTipoProducto',$NuevoCompra->tipo);
            $Estado_oc      =   (new estadoOCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_oc',$NuevoCompra->idEstado_oc);
            $Estado_FC      =   (new estadoFCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_FC',$NuevoCompra->idEstado_FC);
            $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$NuevoCompra->IdEmpresa);

            return (object)[
                "idO_C"                         =>  $CompraDto->idO_C,
                "Fecha_Compra"                  =>  $CompraDto->Fecha_compra,
                "Empresa"                       =>  $Empresa->IdEmpresa,
                "Rut_Proveedor"                 =>  $Proveedor->Rut,
                "Proveedor"                     =>  $Proveedor->Nombre,
                "Nombre_Producto"               =>  $CompraDto->Descripcion,
                "Orden_Compra"                  =>  $CompraDto->Orden_compra,
                "Factura_Compra"                =>  $CompraDto->Factura_compra,
                "Cantidad"                      =>  $CompraDto->Cantidad,
                "Precio_Total"                  =>  $CompraDto->Precio_total,
                "Tipo"                          =>  $TipoProduct->DescripcionProducto,
                "Estado_Activo"                 =>  $estadoactivo->DescripcionActivo,
                "Estado_OC"                     =>  $Estado_oc->Descripcion,
                "Estado_FC"                     =>  $Estado_FC->Descripcion,
            ] ;
        }else{
            return (object)[
                "idO_C"                         =>  $CompraDto->idO_C,
                "Fecha_Compra"                  =>  $CompraDto->Fecha_compra,
                "Nombre_Producto"               =>  $CompraDto->Descripcion,
                "Marca"                         =>  $CompraDto->marca,
                "Modelo"                        =>  $CompraDto->modelo,
                "Orden_Compra"                  =>  $CompraDto->Orden_compra,
                "Factura_Compra"                =>  $CompraDto->Factura_compra,
                "Precio_Unitario"               =>  $CompraDto->Precio_U,
                "Cantidad"                      =>  $CompraDto->Cantidad,
                "Precio_Total"                  =>  $CompraDto->Precio_total,
                "Tipo"                          =>  $CompraDto->tipo,
                "Proveedor"                     =>  $CompraDto->idProveedor,
                "Estado_OC"                     =>  $CompraDto->idEstado_oc,
                "Estado_FC"                     =>  $CompraDto->idEstado_FC,
                "Empresa"                       =>  $CompraDto->IdEmpresa,
            ] ;
        }
    }

    // FUNCION QUE CAMBIA LOS DATOS DE UNA COMPRA EXISTENTE
    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function CambiarParametrosCompra($DatosCompra) 
    { 
        
        // VALIDAMOS LOS CAMPOS INGRESADOS PARA EVITAR INYECCIONES SQL

        // VALIDAMOS LA FECHA DE COMPRA DEL PRODUCTO TENGA LA LONGITUD DE DATOS CORRESPONDIENTE
        if ( strlen($DatosCompra->Fecha_compra) > 10   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Fecha ingresada invalida");
        }

        // VALIDAMOS QUE LA DESCRIPCION NO VENGA VACIA
        if ( strlen($DatosCompra->Descripcion) < 1   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Descripcion ingresada invalida");
        }

        // VALIDAMOS QUE EL PRECIO TOTAL CUMPLA LA LONGITUD CORRESPONDIENTE
        if ( strlen($DatosCompra->Precio_total) < 4 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Precio total ingresado invalida");
        }

        // -----------------------------------------------------------------------
        // UNA VEZ VALIDADO LOS INPUTS
        // PROCEDEMOS A INSTANCIAR EL BO 
        // PARA REALIZAR LAS ACCIONES DEL NEGOCIO
        // -----------------------------------------------------------------------
        $CompraBO      =   new CompraBO();
        

        // PROCEDEMOS A ACTUALIZAR LA INFORMACIÓN EN LA BBDD
        $status         =   $CompraBO->UpdateCompra($DatosCompra);
        
        // OBTENEMOS LA INFORMACION A INYECTAR A LA VISTA DE LA TABLA
        $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$DatosCompra->idMarca);
        $Modelo         =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('idModelo',$DatosCompra->idModelo);
        $Proveedor      =   (new proveedorSvc(ConnectionEnum::TI))->FindByForeign('idProveedor',$DatosCompra->idProveedor);
        $Estado_oc      =   (new estadoOCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_oc',$DatosCompra->idEstado_oc);
        $Estado_FC      =   (new estadoFCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_FC',$DatosCompra->idEstado_FC);
        $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$DatosCompra->IdEmpresa);
        $estadoactivo   =   (new estadosactivosSvc(ConnectionEnum::TI))->FindByForeign('IdEstadoActivo',$DatosCompra->Estado_Activo);
        
        // VALIDAMOS SI SE AGREGO CORRECTAMENTE LA INFORMACION
        if ( $status != true ){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_UPDATE, message: "No ha sido posible actualizar el producto");
        }
        
        // DEVOLVEMOS LOS DATOS A INYECTAR EN LA VISTA DE LAS COMPRAS, GASTOS O ACTIVOS
        if(isset($DatosCompra->Proveedor)){

            $estadoactivo   =   (new estadosactivosSvc(ConnectionEnum::TI))->FindByForeign('IdEstadoActivo',$DatosCompra->Estado_Activo);
            $Proveedor      =   (new proveedorSvc(ConnectionEnum::TI))->FindByForeign('idProveedor',$DatosCompra->idProveedor);
            $TipoProduct    =   (new tipoproductoSvc(ConnectionEnum::TI))->FindByForeign('idTipoProducto',$DatosCompra->tipo);
            $Estado_oc      =   (new estadoOCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_oc',$DatosCompra->idEstado_oc);
            $Estado_FC      =   (new estadoFCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_FC',$DatosCompra->idEstado_FC);
            $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$DatosCompra->IdEmpresa);

            return (object)[
                "idO_C"                         =>  $DatosCompra->idO_C,
                "Fecha_Compra"                  =>  $DatosCompra->Fecha_compra,
                "Empresa"                       =>  $Empresa->IdEmpresa,
                "Rut_Proveedor"                 =>  $Proveedor->Rut,
                "Proveedor"                     =>  $Proveedor->Nombre,
                "Nombre_Producto"               =>  $DatosCompra->Descripcion,
                "Orden_Compra"                  =>  $DatosCompra->Orden_compra,
                "Factura_Compra"                =>  $DatosCompra->Factura_compra,
                "Cantidad"                      =>  $DatosCompra->Cantidad,
                "Precio_Total"                  =>  $DatosCompra->Precio_total,
                "Tipo"                          =>  $TipoProduct->DescripcionProducto,
                "Estado_Activo"                 =>  $estadoactivo->DescripcionActivo,
                "Estado_OC"                     =>  $Estado_oc->Descripcion,
                "Estado_FC"                     =>  $Estado_FC->Descripcion,
            ] ;

        }elseif(isset($DatosCompra->Marca) && $DatosCompra->Cantidad == 0){
            
            $Proveedor      =   (new proveedorSvc(ConnectionEnum::TI))->FindByForeign('idProveedor',$DatosCompra->idProveedor);
            $TipoProduct    =   (new tipoproductoSvc(ConnectionEnum::TI))->FindByForeign('idTipoProducto',$DatosCompra->tipo);
            $Estado_oc      =   (new estadoOCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_oc',$DatosCompra->idEstado_oc);
            $Estado_FC      =   (new estadoFCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_FC',$DatosCompra->idEstado_FC);
            $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$DatosCompra->IdEmpresa);

            return (object)[
                "idO_C"                         =>  $DatosCompra->idO_C,
                "Fecha_Compra"                  =>  $DatosCompra->Fecha_compra,
                "Nombre_Producto"               =>  $DatosCompra->Descripcion,
                "Orden_Compra"                  =>  $DatosCompra->Orden_compra,
                "Factura_Compra"                =>  $DatosCompra->Factura_compra,
                "Precio_Total"                  =>  $DatosCompra->Precio_total,
                "Tipo"                          =>  $TipoProduct->DescripcionProducto,
                "Proveedor"                     =>  $Proveedor->Nombre,
                "Estado_OC"                     =>  $Estado_oc->Descripcion,
                "Estado_FC"                     =>  $Estado_FC->Descripcion,
                "Empresa"                       =>  $Empresa->IdEmpresa,
            ] ;

        }else{

            return (object)[
                "idO_C"                         =>  $DatosCompra->idO_C,
                "Fecha_Compra"                  =>  $DatosCompra->Fecha_compra,
                "Nombre_Producto"               =>  $DatosCompra->Descripcion,
                "Marca"                         =>  $Marca->Descripcion,
                "Modelo"                        =>  $Modelo->Descripcion,
                "Orden_Compra"                  =>  $DatosCompra->Orden_compra,
                "Factura_Compra"                =>  $DatosCompra->Factura_compra,
                "Precio_Unitario"               =>  $DatosCompra->Precio_U,
                "Cantidad"                      =>  $DatosCompra->Cantidad,
                "Precio_Total"                  =>  $DatosCompra->Precio_total,
                "Tipo"                          =>  $DatosCompra->tipo,
                "Proveedor"                     =>  $Proveedor->Descripcion,
                "Estado_OC"                     =>  $Estado_oc->Descripcion,
                "Estado_FC"                     =>  $Estado_FC->Descripcion,
                "Empresa"                       =>  $Empresa->Descripcion
            ] ;

        }



    }

    // FUNCION PARA OBTENER LOS MODELOS PERTENECIENTE A UNA MARCA
    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function GetbyMarca($DatosMarca) 
    { 
        // INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD
        $ModeloService     =   new modeloSvc(ConnectionEnum::TI);

        // GUARDAMOS LA ID DE MARCA EN UNA VARIABLE PARA BUSCAR LOS MODELOS
        $idMarca       =     $DatosMarca->idMarca;

        try{
            // BUSCAMOS LA INFORMACIÓN DE LOS MODELOS
            $datos          =   $ModeloService->GetByForeign('idMarca',$idMarca);

        } catch (\Exception $ex) {

            // GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function GetbyProveedor($DatosProveedor) 
    { 
        // INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD
        $ProveedorService     =   new proveedorSvc(ConnectionEnum::TI);

        // GUARDAMOS LA ID DE MARCA EN UNA VARIABLE PARA BUSCAR LOS MODELOS
        $idProveedor       =     $DatosProveedor->idProveedor;

        try{
            // BUSCAMOS LA INFORMACIÓN DE LOS MODELOS
            $datos          =   $ProveedorService->GetByForeign('idProveedor',$idProveedor);

        } catch (\Exception $ex) {

            // GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }
   

}