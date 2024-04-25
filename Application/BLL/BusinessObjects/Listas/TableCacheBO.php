<?php

namespace Application\BLL\BusinessObjects\Listas;

use Application\BLL\BusinessEnumerations\Listas\EstadoGestionClienteListaEnum;
use Application\BLL\BusinessEnumerations\Listas\EstadoListaEnum;
use Application\BLL\BusinessEnumerations\Listas\TipoDatoEnum;
use Application\BLL\BusinessEnumerations\Listas\TipoServicioEnum;
use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\BusinessEnumerations\TipoLogEnum;
use Application\BLL\DataTransferObjects\Clientes\ClientesDto;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Listas\DetalleListaClienteDto;
use Application\BLL\DataTransferObjects\Listas\ListaClientesDto;
use Application\BLL\DataTransferObjects\Listas\ListaDetalleLogDto;
use Application\BLL\DataTransferObjects\Listas\ListaGrupoDto;
use Application\BLL\DataTransferObjects\Listas\ListaLogDto;
use Application\BLL\DataTransferObjects\Listas\ListaParametroDto;
use Application\BLL\DataTransferObjects\Listas\ObservacionClienteDto;
use Application\BLL\DataTransferObjects\Listas\ProcedimientosDto;
use Application\BLL\DataTransferObjects\Listas\VWDetalleListasDto;
use Application\BLL\DataTransferObjects\Servicio\ServicioVentaDto;
use Application\BLL\Filters\Listas\CambiosGestionFilterDto;
use Application\BLL\Filters\Listas\NuevaListaFilterDto;
use Application\BLL\Filters\Listas\NuevaObservacionFilterDto;
use Application\BLL\Filters\Listas\ParametrosAreaFilterDto;
use Application\BLL\Filters\Listas\ParametrosFiltrarListasFilterDto;
use Application\BLL\Filters\Listas\TipoAreaFilterDto;
use Application\BLL\Services\Clientes\ClientesSvc;
use Application\BLL\Services\Core\PerfilSvc;
use Application\BLL\Services\Core\UsuarioSvc;
use Application\BLL\Services\Listas\CentroSvc;
use Application\BLL\Services\Listas\DetalleListaClienteSvc;
use Application\BLL\Services\Listas\EstadoListaSvc;
use Application\BLL\Services\Listas\ListaClientesSvc;
use Application\BLL\Services\Listas\ListaDetalleLogSvc;
use Application\BLL\Services\Listas\ListaGrupoSvc;
use Application\BLL\Services\Listas\ListaLogSvc;
use Application\BLL\Services\Listas\ListaParametroSvc;
use Application\BLL\Services\Listas\ObservacionClienteSvc;
use Application\BLL\Services\Listas\ProcedimientosSvc;
use Application\BLL\Services\Listas\PropiedadServicioSvc;
use Application\BLL\Services\Listas\ServicioControlEmisionSvc;
use Application\BLL\Services\Listas\ServicioSvc;
use Application\BLL\Services\Listas\ServicioVentaMesonSvc;
use Application\BLL\Services\Listas\TipoServicioSvc;
use Application\BLL\Services\Listas\VWCreditosNuevosSvc;
use Application\BLL\Services\Listas\VWCreditosUsadosSvc;
use Application\BLL\Services\Listas\VWDetalleListasSvc;
use Application\BLL\Services\Listas\VWEstadoListasSvc;
use Application\BLL\Services\Listas\VWServicioControlEmisionSvc;
use Application\BLL\Services\Listas\VWServiciosGTSvc;
use Application\BLL\Services\Listas\VWServicioVentaMesonSvc;
use Application\BLL\Services\Listas\VWTraerResponsablesListasSvc;
use Application\BLL\Services\Listas\VWVentaVehiculosNuevosSvc;
use Application\BLL\Services\Listas\VWVentaVehiculosUsadosSvc;
use Application\BLL\Services\Servicio\ServicioGTSvc; 
use Application\BLL\Services\Servicio\ServicioVentaSvc;
use Application\Configuration\ConnectionEnum;
use DateTime;
use Exception;
use FuncInfo;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Dates\Date;
use Intouch\Framework\Environment\RedisDataTable;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;

class TableCacheBO 
{

 
    public static function GetAll($guid = "") : GenericCollection | null 
    {   
        if (empty($guid)){
            return null;
        } 

        return RedisDataTable::Instance()->$guid;         
    }

    public static function GetForDataTable($guid, $startIdx,$SearchCriterio,$draw , $length ) { 

        ## OBTENEMOS LOS DATOS PARA FILTRAR 
        $TableData          =   self::GetAll($guid)->Values;

        $datosParaMostrar   =   $TableData;

        $NeedFilter         =   !empty($SearchCriterio) ? true : false;  

        ## REALIZAMOS UN FILTRADO EN CASO DE QUE SEA NECESARIO 
        if ($NeedFilter == true){ 
            ## BUSCAMOS LOS PARAMETROS QUE CUMPLEN LA CONDICIÓN SOLICITADA
            $datosParaMostrar   =   array_map(function($x) use ($SearchCriterio) { 
                ## BUSCAMOS EN TODOS LOS CAMPOS LOS PARAMETROS QUE PODRIAN COINCIDIR
                $status         =   array_map(function($z) use ($SearchCriterio) {  
                    ## VALIDAMOS SI EL TEXTO ESTA PRESENTE EN EL VALOR
                    return strpos( $z,$SearchCriterio) === false  ? null: true;  
                },$x); 
                return !empty(array_filter($status)) ? $x : null;
            },$TableData);

            $datosParaMostrar   =   array_filter($datosParaMostrar);

        }  

        ## REGRESAMOS EL RESULTADO 
        return (object)[
            "draw"              =>  $draw,
            "recordsTotal"      =>  count($TableData),
            "recordsFiltered"   =>  count($datosParaMostrar),
            "data"              =>  array_slice($datosParaMostrar, $startIdx, $length)
        ];
    }
    
    

}