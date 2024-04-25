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
use Application\BLL\DataTransferObjects\Listas\VWServiciosEntregadosPorClienteDto;
use Application\BLL\Filters\Listas\CambiosGestionFilterDto;
use Application\BLL\Filters\Listas\NuevaListaFilterDto;
use Application\BLL\Filters\Listas\NuevaObservacionFilterDto;
use Application\BLL\Filters\Listas\ParametrosAreaFilterDto;
use Application\BLL\Filters\Listas\ParametrosFiltrarListasFilterDto;
use Application\BLL\Filters\Listas\TipoAreaFilterDto;
use Application\BLL\Filters\PerfilClienteFilterDto;
use Application\BLL\Services\Clientes\ClientesSvc;
use Application\BLL\Services\Core\PerfilSvc;
use Application\BLL\Services\Core\UsuarioSvc; 
use Application\BLL\Services\Listas\DetalleListaClienteSvc;
use Application\BLL\Services\Listas\EstadoListaSvc;
use Application\BLL\Services\Listas\FBBPatentesPorClientesSvc;
use Application\BLL\Services\Listas\ListaClientesSvc;
use Application\BLL\Services\Listas\ListaDetalleLogSvc;
use Application\BLL\Services\Listas\ListaGrupoSvc;
use Application\BLL\Services\Listas\ListaLogSvc;
use Application\BLL\Services\Listas\ListaParametroSvc;
use Application\BLL\Services\Listas\ObservacionClienteSvc;
use Application\BLL\Services\Listas\ProcedimientosSvc;
use Application\BLL\Services\Listas\PropiedadServicioSvc; 
use Application\BLL\Services\Listas\ServicioSvc;  
use Application\BLL\Services\Listas\TipoServicioSvc;
use Application\BLL\Services\Listas\VWCreditosNuevosSvc;
use Application\BLL\Services\Listas\VWCreditosUsadosSvc;
use Application\BLL\Services\Listas\VWDetalleListasSvc;
use Application\BLL\Services\Listas\VWEstadoListasSvc;
use Application\BLL\Services\Listas\VWObservacionesClientesSvc;
use Application\BLL\Services\Listas\VWServicioControlEmisionSvc;
use Application\BLL\Services\Listas\VWServiciosEntregadosPorClienteSvc;
use Application\BLL\Services\Listas\VWServiciosGTSvc;
use Application\BLL\Services\Listas\VWServicioVentaMesonSvc;
use Application\BLL\Services\Listas\VWTraerResponsablesListasSvc;
use Application\BLL\Services\Listas\VWVentaVehiculosNuevosSvc;
use Application\BLL\Services\Listas\VWVentaVehiculosUsadosSvc; 
use Application\Configuration\ConnectionEnum; 
use DateInterval;
use DateTime; 
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Dates\Date;
use Intouch\Framework\Environment\RedisDataTable;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum; 

class ListasBO 
{

    ## FUNCIÓN QUE NOS PERMITE TRAER LAS PROPIEDADEDES QUE PUDEDEN SER USADAS
    ## PARA FILTRAR UNA LISTA
    public function ObtenerGruposPropieadesListas() 
    {
        
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE USUARIOS QUE SE ENCUENTRAN GENERADOS EN LA BBDD
        $PropiedadesSvc     =   new PropiedadServicioSvc(ConnectionEnum::DEFAULT);

        ## UNA VEZ QUE OBTENEMOS LA LISTA DE PROPIEDADES
        $PropieadesDisponibles  =   $PropiedadesSvc->TraerPropieadesDisponibles();

        ## PROCEDEMOS A GENERAR LOS DATOS 
        $ListaPropiedades   =   (object)[];

        ## comenzamos a recorrer cada una de las propieades y procedemos a filtrar segun se necesite
        foreach($PropieadesDisponibles as $Propiedad ){

            $NombreBBDD     =  $Propiedad->NombreTablaAsociada;
            
            ## VALIDAMOS SI EL TIPO DE DATO SE ENCUENTRA DISPONIBLE
            if (!property_exists($ListaPropiedades, $Propiedad->NombreTablaAsociada)) {
                $ListaPropiedades->$NombreBBDD = new TipoAreaFilterDto(
                    IdTipoArea  :   $Propiedad->TipoServicio->IdTipoServicio,
                    NombreAre   :   $Propiedad->TipoServicio->Descripcion,
                    Parametros  :   []
                );
            }

            ## PROCEDEMOS A VALIDAR LOS TIPOS DE PARAMETROS QUE RECIBE LA APLICACIÓN

            ## EN CASO DE QUE SEA LISTA DESPLEGABLE
            ## PROCEDEMOS A BUSCAR LOS DATOS QUE NECESITA
            $valores        =   null; 
            if ( $Propiedad->IdTipoDato == TipoDatoEnum::LISTA_DESPLEGABLE ){

                ## LLAMAMOS UN SERVICIO SEGUN SE NECESITE POR EL TIPO DE INFORMACIÓN
                switch($Propiedad->IdTipoServicio){
                    
                    case TipoServicioEnum::Venta_Nuevos :
                        $svc    =   new VWVentaVehiculosNuevosSvc(ConnectionEnum::DEFAULT);
                        break;
                    case TipoServicioEnum::Venta_Usados :
                        $svc    =   new VWVentaVehiculosUsadosSvc(ConnectionEnum::DEFAULT);
                        break;
                    case TipoServicioEnum::Postventa_GT :
                        $svc    =   new VWServiciosGTSvc(ConnectionEnum::DEFAULT);
                        break;
                    case TipoServicioEnum::Venta_Meson  :
                        $svc    =   new VWServicioVentaMesonSvc(ConnectionEnum::DEFAULT);
                        break;
                    case TipoServicioEnum::Control_Emisión:
                        $svc    =   new VWServicioControlEmisionSvc(ConnectionEnum::DEFAULT);
                        break;
                    case TipoServicioEnum::Creditos_Nuevos:
                        $svc    =   new VWCreditosNuevosSvc(ConnectionEnum::DEFAULT);
                        break;
                    case TipoServicioEnum::Creditos_Usados:
                        $svc    =   new VWCreditosUsadosSvc(ConnectionEnum::DEFAULT);
                        break;
                    default:
                        $svc = null;
                        break;

                }

                if(!empty($svc)){

                    ## UNA VEZ INSTANCIADO EL SERVICE, OBTENEMOS LOS VALORES UNICOS DE LA PROPIEDAD ESTABLECIDA
                    $NombrePropiedad    =   $Propiedad->NombreColumna;

                    $valores    =   $svc->BuscarValoresUnicosColumna($NombrePropiedad);

                }

         
                

            }

            ## UNA VEZ PROCESADO EL CAMPO, PROCEDEMOS A AGREGAR LA PROPIEDAD A LA LISTA
            $ListaPropiedades->$NombreBBDD->Parametros[$Propiedad->IdPropiedadServicio] = new ParametrosAreaFilterDto(
                NombreParametro         :   $Propiedad->Descripcion,
                TipoDato                :   $Propiedad->IdTipoDato,
                Valores                 :   $valores,
                IdPropiedadServicio     :   $Propiedad->IdPropiedadServicio
            );

            $stop = 1;



        }


        return $ListaPropiedades;



 
    }
 
    ## FUNCIÓN QUE NOS PERMITE VALIDAR SI LOS DATOS DE UNA NUEVA VISTA SE ENCUENTRAN
    ## CORRECTAMENTE DIGITADOS 
    private function ValidarDatosNuevaLista(NuevaListaFilterDto $DatosLista){
        if(empty($DatosLista->NombreLista)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'Nombre lista invalido');
        }

        if(empty($DatosLista->IdTipoLista) && $DatosLista->IdTipoLista != 0) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');
        }

        if(empty($DatosLista->Descripcion)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');
        }
        if(empty($DatosLista->IdGrupoUsuario)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');
        }

        if(empty($DatosLista->FechaDesde)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');
        }

        if(empty($DatosLista->FechaHasta)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');
        }

        if(empty($DatosLista->Condiciones)) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');
        }
    }

    ## FUNCIÓN QUE NOS PERMITE GUARDAR LA INFORMACIÓN EN LA BASE DE DATOS
    public function GenerarNuevaListaEnBBDD(NuevaListaFilterDto $DatosLista){

        $stop = 1;

        ## EN PRIMER LUGAR VALIDAMOS LOS CAMPOS DE LA LISTA
        $this->ValidarDatosNuevaLista($DatosLista); 

        ## EN SEGUNDO LUGAR PROCEDEMOS A VALIDAR LAS CONDICIONES DE LA LISTA

        $DatosLista->Condiciones    =   array_filter($DatosLista->Condiciones);

        ## VALIDAMOS SI EXISTEN CONDICIONES SOLICITADAS
        if (count($DatosLista->Condiciones) < 1) {

            ## EN CASO DE QUE NO EXISTAN CONDICIONES PROCEDEMOS A ELIMINARLAS DEL A BBDD
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'TipoListaInvalido');

        } 

        ## PROCEDEMOS A BUSCAR A LA BBDD TODAS LAS OPCIONES DISPONIBLES
        $PropieadesServicio     =   (new PropiedadServicioSvc(ConnectionEnum::DEFAULT))->GetAll();


        ## UNA VEZ QUE SE VALIDA LA EXISTENCIA DE CONDICIONES
        ## SE PROCEDE A CONSTRUIR LA QUERY
        $Parametros =   $this->GenerarParametros($DatosLista->Condiciones,$PropieadesServicio,$DatosLista); 

        ## GUARDAMOS LA INFORMACIÓN DE LA CONSULTA
        $status         =   $this->GuardarNuevaListaEnBBDD($DatosLista, $Parametros);
 


    }

    ## FUNCIÓN QUE NOS PERMITE GENERAR UN OBJETO CON LOS PARAMETROS ORDENADOS Y ENCADENADOS
    ## EN UN FORMATO FACIL DE MANEJAR
    public  function GenerarParametros(array $Condiciones,GenericCollection $PropiedadServicio, NuevaListaFilterDto $DatosLista) {

        
        ## ESTABLECEMOS TODOS LOS WHERE DEL GRUPO
        $Grupos         =   [];
        $idxGrupos      =   [];
        $Usuario        =   Session::Instance()->usuario;
        $now            =   (new Date('now'))->__toString();

        $Joins          =   [];

        $GruposDto      =   new GenericCollection(
            DtoName     :   ListaGrupoDto::class,
            Key         :   'IdListaGrupo',
            Values      :   []
        ); 

        /* IDENTIFICAMOS CUALES SON LAS PROPIEDADES A USAR */

        foreach($Condiciones as $key => $Condition) {

            ## ESTABLECEMOS EL GRUPO
            $idxGrupos[$key]    =   0; 
            $Grupos[$key]       =  (object) [
                "union"     =>  "",
                "where"     =>  []
            ];
            $Condition->Condiciones   =   array_filter($Condition->Condiciones); 
            $Grupodt        =new ListaGrupoDto(
                IdListaGrupo        :   0,
                IdUsuario           :   $Usuario->IdUsuario,
                TipoUnion           :   $Condition->CondicionDelGrupo,
                FechaCreacion       :   $now,
                IdListaCliente      :   0,
                AreaGrupo           :   $Condition->AreaGrupo
            );

      


            ## PROCEDEMOS A GENERAR LOS WHERE SEGUN LOS GRUPOS 
            foreach($Condition->Condiciones as $item){
                $idxGrupos[$key]++;
                
                ## OBTENEMOS EL ID DE LA PROPIEDAD
                $propiedad  =   $item->property;

                $PropiedadDto   =   $PropiedadServicio->Find( $propiedad);




                ## ESTABLECEMOS EL METODO
                $Condicion  =   "IN";

                ## BUSCAMOS LA PROPIEDAD EN LA BBDD
                $FechaInicio    =   date("Y-m-d", strtotime($DatosLista->FechaDesde));
                $FechaFin       =   date("Y-m-d", strtotime($DatosLista->FechaHasta));

                ## CONSTRUIMOS UN TEMPLATE DEL JOIN QUE SE VA A UTILZAR
                $TemplateJoin   =   " LEFT JOIN $PropiedadDto->TablaVinculante ON $PropiedadDto->TablaVinculante.IdClientes = Clientes.IdClientes AND $PropiedadDto->TablaVinculante.IdCiudad = $DatosLista->IdCiudad AND $PropiedadDto->TablaVinculante.FechaServicio BETWEEN DATE('".$FechaInicio."') AND DATE('".$FechaFin."')";

                if(  !in_array($TemplateJoin,$Joins ) ){
                    $Joins[$PropiedadDto->IdPropiedadServicio]    =     $TemplateJoin; 
                }
                
                    
                ## ESTABLECEMOS SI ES NECEASRIO GENERAR UN AND PREVIO A LA CONSULTA
                $WhereSql   =   $idxGrupos[$key] > 1  ? " AND " : ""; 

                switch( $PropiedadDto->IdTipoDato){
                    default:
                    case TipoDatoEnum::LISTA_DESPLEGABLE:

                        if (in_array(-1,$item->values) || empty($item->values)){
                            $WhereSql   .= " $PropiedadDto->TablaVinculante.$PropiedadDto->NombreColumna IS NOT NULL ";
                        }else{
                            ## ESTABLECEMOS LA CONDICIÓN CORRESPONDIENTE
                            $__Condicion  =       "IN";

                            ## ESTANDARIZAMOS LOS VALORES COMO CORRESPONDE
                            $item->values   =   array_map(function($e){return "'".$e."'";},$item->values);

                            ## CONVERTIMOS LOS VALORES AL FORMATO CORRECTO
                            $valor1     =       implode(",",$item->values);

                            ## GENERAMOS EL WHERE SQL PARA INYECTAR
                            $WhereSql   .= " $PropiedadDto->TablaVinculante.$PropiedadDto->NombreColumna $__Condicion ($valor1) ";

                                            ## GUARDAMOS LOS PARAMETROS PARA INSERTARLOS EN LA BBDD

                            $Grupodt->Parametros[]  =    new ListaParametroDto(
                                IdListaParametro    :   0,
                                IdListaGrupo        :   0,
                                IdPropiedadServicio :   $item->property,
                                NombreColumna       :   $PropiedadDto->NombreColumna,
                                NombreTabla         :   $PropiedadDto->TablaVinculante,
                                ValorUno            :   $valor1,
                                ValorDos            :   null
                            );
                     
                        }


                        
                        break;
                    case TipoDatoEnum::RANGO_TEXTO:
                        
                        ## ESTABLECEMOS LA CONDICIÓN DEL WHERE
                        $__Condicion = "BETWEEN";

                        ## GENERAMOS EL WHERE 
                        $Valor1     =   str_replace([".","-","_"]   ,"",    $item->values->minimo);
                        $Valor2     =   str_replace([".","-","_"]   ,"",    $item->values->maximo);

                        $WhereSql   .= " $PropiedadDto->TablaVinculante.$PropiedadDto->NombreColumna $__Condicion $Valor1 AND $Valor2 ";

                        $Grupodt->Parametros[]  = new ListaParametroDto(
                                IdListaParametro    :   0,
                                IdListaGrupo        :   0,
                                IdPropiedadServicio :   $item->property,
                                NombreColumna       :   $PropiedadDto->NombreColumna,
                                NombreTabla         :   $PropiedadDto->TablaVinculante,
                                ValorUno            :   $Valor1,
                                ValorDos            :   $Valor2
                        );
                    

                        break;
                }   

                $Condicion  =   $Condition->CondicionDelGrupo ?: "";

                switch(strtoupper($Condicion)){
                    case "Y":
                        $Grupos[$key]->union = "AND";
                        break;
                    case "O":
                        $Grupos[$key]->union = "OR";
                        break;
                    default:
                        $Grupos[$key]->union = "";
                        break;

                }
                 
                $Grupos[$key]->where[]   =  $WhereSql;



            }

            ## UNA VEZ GENERADO EN LOS GRUPOS
            ## PROCEDEMOS A GUARDAR LOS DATOS COMO ES DEBIDO
            $stop = 1;
            $GruposDto->Add($Grupodt);





        }


        return (object)[
            "Objetos"       => $GruposDto,
            "Joins"         => $Joins,
            "Where"         => $Grupos
        ];

    }

    ## FUNCIÓN QUE TOMA LAS CONDICIONES DE LA FUNCIÓN GENERARPARAMETROS PARA CONSTRUIR
    ## UNA CONSULTA SQL QUE PERMITA INGRESAR LOS DATOS EN LA BBDD
    public function GenerarQuery($FechaInicio, $FechaFin, $Parametros, int $IdCIudad){


        $stop           = 1;
        $FechaInicio    =   date("Y-m-d", strtotime($FechaInicio));
        $FechaFin       =   date("Y-m-d", strtotime($FechaFin));

        $now            =   (new Date('now'))->__toString(); 

        $QRY =  "SELECT
            [[IdListaCliente]],
            NULL,
            Clientes.IdClientes,
            '".$now."',
            '".$now."',
            ". EstadoGestionClienteListaEnum::SIN_GESTIONAR ."
        FROM Autogestores_Warehouse.Clientes";

        foreach( $Parametros->Joins as $join) {
            $QRY    .= "\n".$join;
        }
        $QRY .= "\n WHERE (";

        if(!empty($Parametros->Where)){


            ## EN PRIMER LUGAR SEPARAMOS LAS CONSULTAS AND Y OR
            $OrWhere            =   [];
            $AndWhere           =   [];  
            $FirstWhere         =   [];
            $SecondIsWhereOR    =   false;

            ## ORDENAMOS LAS CONSULTAS SEGUN EL TIPO
            foreach( $Parametros->Where as $key => $w) {   
                $Union      =   $w->union; 
                if ($key == 1 && key_exists(2,$Parametros->Where) ){
                    $SecondIsWhereOR     =  $Parametros->Where[2]->union == "OR" ? true : false;
                } 
                if ($Union == "OR"){  
                    array_push($OrWhere,$key);

                }elseif ($Union == "AND"){ 
                    array_push($AndWhere,$key); 
                }else{ 
                    array_push($FirstWhere,$key); 
                }

            }


            ## VALIDAMOS EN QUE GRUPO DEBEMOS ENCAJAR EL PRIMER PARAMETRO DE LA QUERY

            if ($SecondIsWhereOR == true){
                $OrWhere    =   array_merge($FirstWhere,$OrWhere);
            }else{
                $AndWhere    =   array_merge($FirstWhere,$AndWhere);
            }


            ## UNA VEZ REALIZADO EL EJERCICIO PROCEDEMOS A CONSTRUIR LA CONSULTA

            $AndQuery   =   "";
            $OrQuery    =   "";

            if(count($OrWhere) > 0){
                foreach($OrWhere as $key){
                    $w  =   $Parametros->Where[$key];
                    $condition  =   implode("\n", $w->where); 
                    $Union      =   $w->union;
                    $GROUP      =   " $Union ( $condition ) "; 
                    $OrQuery   .=  $GROUP; 
                }

            }
            
            if(count($AndWhere) > 0){
                $zx = 0;
                foreach($AndWhere as $key){

                    $w              =       $Parametros->Where[$key];
                    $condition      =       implode("\n", $w->where); 
                    $Union          =       $zx == 0 ? "" : $w->union;
                    $GROUP          =       " $Union ( $condition ) "; 
                    $AndQuery       .=      $GROUP; 
                    $zx++;

                }

            }
         

            ## FINALMENTE CONSTRUIMOS LA QUERY FINAL
            $FINAL_WHERE    =   "";

            if(!empty($AndQuery) && !empty($OrQuery)){
                $FINAL_WHERE    =   
                "
                ( $OrQuery )
                AND
                ( $AndQuery )
                ";
            }elseif(!empty($AndQuery)) {
                $FINAL_WHERE    =   
                " 
                ( $AndQuery )
                ";
            }elseif(!empty($OrQuery)){
                $FINAL_WHERE    =   
                "
                ( $OrQuery ) 
                ";      
            }else{
                throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:"DEBE INGRESAR AL MENOS UNA CONDICIÓN VALIDA");
            }

            $QRY .= $FINAL_WHERE;

        }

        $QRY .= ") ";


        return $QRY;
    }

    ## FUNCIÓN QUE PERMITE GUARDAR LA INFORMACIÓN EN LA BBDD DE PROCEDIMIENTOS
    ## ESTO PERMITE QUE EL PROCEDIMIENTO ALMACENADO SUMADO AL EVENTO AUTOGENEREN UNA LISTA DIRECTO EN LA BBDD
    public function GuardarNuevaListaEnBBDD(NuevaListaFilterDto $DatosLista, $ParametrosConsulta) : ListaClientesDto|null { 

        ## OBTENEMOS EL USUARIO ACTIVO
        $UsuarioDto     =   Session::Instance()->usuario;

        ## OBTENEMOS LA FECHA ACTUAL
        $now            =   (new Date('now'))->__toString();

        ## --------------------------------------------------------------------------------
        ## INSTANCIAMOS LOS SERVICES A UTILIZAR
        ## --------------------------------------------------------------------------------

        ## instanciamos el service de las listas como tal
        $ListaClientesSvc   =   new ListaClientesSvc(ConnectionEnum::DEFAULT);

        ## instanciamos el servide del log de las listas
        $ListaLogSvc        =   new ListaLogSvc(ConnectionEnum::DEFAULT);

        ## instanciamos el service que almacena los grupos 
        $GruposSvc          =   new ListaGrupoSvc(ConnectionEnum::DEFAULT);

        ## instanciamos el service que almacena los parametros
        $ParametrosSvc      =   new ListaParametroSvc(ConnectionEnum::DEFAULT);

        ## --------------------------------------------------------------------------------

        ## EN PRIMER LUGAR GENERAMOS LA LISTA CON TODOS LOS DATOS NECESARIOS
        ## EN SEGUNDO LUGAR ALMACENAMOS EN LA BBDD LA INFORMACIÓN REFERENTE A COMO SE CONSTRUYE LA LISTA

        GenericSvc::BeginMultipleOperations(ConnectionEnum::DEFAULT);
        try{

            ## PROCEDEMOS A CREAR UNA NUEVA LISTA
            $ListaClienteDto    =   new ListaClientesDto(
                IdPropietario       :   $UsuarioDto->IdUsuario,
                Descripcion         :   $DatosLista->Descripcion,
                Activa              :   1,
                Editable            :   $DatosLista->IdTipoLista == 0 ? 1 : 0,
                FechaCreacion       :   $now,
                IdTipoLista         :   $DatosLista->IdTipoLista == 0 ? 1 : 2,
                IdGrupo             :   $DatosLista->IdGrupoUsuario,
                IdResponsable       :   $UsuarioDto->IdUsuario,
                FechaInicio         :   date("Y-m-d", strtotime($DatosLista->FechaDesde)),
                FechaFin            :   date("Y-m-d", strtotime($DatosLista->FechaHasta)),
                Nombre              :   $DatosLista->NombreLista,
                IdEstadoLista       :   1,
                IndListaPublica     :   0,
                IndEliminado        :   0,
                IdCiudad            :   $DatosLista->IdCiudad
               
            ) ;
            
            
            ## INSERTAMOS LA NUEVA LISTA EN LA BBDD
            $ListaClienteDto    =   $ListaClientesSvc->Insert($ListaClienteDto);

            ## GENERAMOS EL LOG DE CREACIÓN DE LA LIST

            $ListaLogDto        =   new ListaLogDto(
                IdUsuario           :   $UsuarioDto->IdUsuario,
                IdTipoLog           :   1,
                DescripcionAccion   :   'CREACIÓN LISTA',
                Detalle             :   'EL USUARIO A CREADO UNA NUEVA LISTA',
                IdListaCliente      :   $ListaClienteDto->IdListaCliente,
                Fecha               :   $now
            ) ;

            ## GUARDAMOS EL LOG EN LA BBDD
            $ListaLogSvc->Insert($ListaLogDto);

            ## ------------------------------------------------------------------
            ## PROCEDEMOS A GUARDAR LOS GRUPOS Y PARAMETROS
            ## ------------------------------------------------------------------
            $dx = 0;
            foreach($ParametrosConsulta->Objetos as $Grupo){
                $dx++;

                $GrupoDto       =   new ListaGrupoDto(
                    IdxGrupo        :   $dx,
                    IdUsuario       :   $UsuarioDto->IdUsuario,
                    TipoUnion       :   $Grupo->TipoUnion ?: "",
                    FechaCreacion   :   date("Y-m-d", strtotime($now)),
                    IdListaCliente  :   $ListaClienteDto->IdListaCliente,
                    AreaGrupo       :   $Grupo->AreaGrupo
                ) ;
                
                ## INSERTAMOS EL GRUPO EN LA BBDD
                $GrupoDto   =   $GruposSvc->Insert($GrupoDto);

                ## UNA VEZ CREADO EL GRUPO
                ## PROCEDEMOS A GUARDAR LA INFORMACIÓN DE LOS PARAMETROS
                foreach($Grupo->Parametros as $item ){

                    ## GENERAMOS EL DTO A INSERTAR EN LA BBDD
                    $ListaParametroDto  =   new ListaParametroDto(
                        IdListaGrupo            :   $GrupoDto->IdListaGrupo,
                        IdPropiedadServicio     :   $item->IdPropiedadServicio,
                        NombreColumna           :   $item->NombreColumna,
                        NombreTabla             :   $item->NombreTabla,
                        ValorUno                :   $item->ValorUno,
                        ValorDos                :   $item->ValorDos
                    ) ; 
                    ## INSERTAMOS EL PARAMETRO EN LA BBDD
                    $ParametrosSvc->Insert($ListaParametroDto); 
                }



            }

            ## UNA VEZ QUE ESTEN TODOS LOS DATOS GUARDADOS EN LA BBDD
            ## PROCEDEMOS A REALIZAR UN COMMIT CON LOS CAMBIOS
            ## ESTA OPERACIÓN GUARDARA LOS DATOS EN LA BASE DE DATOS
            

            ## PROCEDEMOS A GENERAR LA QUERY A PARTIR DE LOS PARAMETROS
            $Consulta       =   $this->GenerarQuery($DatosLista->FechaDesde,$DatosLista->FechaHasta,$ParametrosConsulta, $DatosLista->IdCiudad);


            ## EJECUTAMOS LA CONSULTA PARA CREAR LOS REGISTROS EN LA BBDD
            $this->GenerarDetalleLista($ListaClienteDto->IdListaCliente,$Consulta);

            GenericSvc::SaveMultipleOperations();

            return $ListaClienteDto;  

        } catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            ## GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT,message:"SE HA GENERADO UN PROBLEMA AL ALMACENAR LOS CAMBIOS EN LA BBDD");
        }

    }


    ## FUNCIÓN QUE NOS PERMITE GENERAR EL DETALLE DE LA LISTA Y ALMACENARLOS EN LA BBDD
    public function GenerarDetalleLista($IdLista,$consulta){
 
        
        $INSERTBASE     =   "INSERT IGNORE INTO Autogestores_Warehouse.DetalleListaCliente (IdListaCliente,IdUsuario,IdCliente,FechaIngreso,FechaActualizacion,IdEstadoGestion)";
         
        $FINALQUERY     =   $INSERTBASE . $consulta;
        
        $FINALQUERY     .=   " AND NOT EXISTS (SELECT * FROM Autogestores_Warehouse.DetalleListaCliente dlc WHERE dlc.IdCliente = Clientes.IdClientes AND dlc.IdListaCliente =[[IdListaCliente]]  ) AND Clientes.IdClientes IS NOT NULL ";

        $FINALQUERY       =   str_replace("[[IdListaCliente]]",$IdLista,$FINALQUERY);

        $FINALQUERY     =   str_replace("(","/////////",$FINALQUERY);
        $FINALQUERY     =   str_replace(")","---------",$FINALQUERY);

        ## INSTANCIAMOS EL SERVICE QUE DEBERIA EJECUTAR LA LISTA
        $svc            =   new ProcedimientosSvc(ConnectionEnum::DEFAULT);

        ## INTENTAMOS EJECUTAR LA CONSULTA
        try{

            $Dto        =   new ProcedimientosDto(
                Consulta        :   $FINALQUERY,
                FechaInicio     :   null,
                FechaFIn        :   null,
                Ejecutado       :   0,
                IdListaCliente  :   $IdLista
            ) ;
            
            $svc->Insert($Dto);

            return true;
            
        } catch (\Exception $ex) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_INSERT, message: "NO FUE POSIBLE GENERAR EL DETALLE DE LA LISTA, FAVOR CONTACTAR A SOPORTE Y INDICAR EL NUMERO ID: $IdLista ");
        }


    }


    ## FUNCIÓN QUE NOS PERMITE BUSCAR UNA LISTA POR SU ID Y TRAER TODOS SUS PARAMETROS
    ## ESTA FUNCIÓN CONTIENE COMPROBACIONES DE ACCESO 
    ## EN CASO DE NO CUMPLIRSE ALGUNA CONDICIÓN ARROJARA UN ERROR
    public function BuscarLista(int $IdLista) : object | null {

        
        ## EN PRIMER LUGAR INSTANCIAMOS EL SERVICE QUE NOS PERMITE EXTRAER EL DETALLE DE LA LISTA
        $ListaClientesSvc       =   new ListaClientesSvc(ConnectionEnum::DEFAULT);

        ## INSTANCIAMOS EL SERVICE QUE NOS PERMITE EXTRAER EL DETALLE DE UNA LISTA
        $DetalleListaSvc        =   new VWDetalleListasSvc(ConnectionEnum::DEFAULT);

        ## BUSCAMOS EN REDIS LA SESIÓN DEL USUARIO
        $UsuarioDto             =   Session::Instance()->usuario;

        ## VALIDAMOS SI EL USUARIO TIENE ACCESO A VISUALIZAR ESTA LISTA
        $HasAccess              =   $ListaClientesSvc->ValidarAccesoLista(
            IdLista :   $IdLista,
            Usuario :   $UsuarioDto
        ) ;

        ## VERIFICAMOS EL RESULTADO DE LA VALIDACIÓN, EN CASO DE SER NEGATIVA MOSTRAMOS UN ERROR E IMPEDIMOS EL ACCESO
        if ($HasAccess == false){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_UNAUTHORIZED,message:'No esta autorizado a ver la información');
        }

        ## EN SEGUNDO LUGAR BUSCAMOS TODOS LOS REGISTROS DISPONIBLES DENTRO DE LA LISTA
        $DatosLista              =   $ListaClientesSvc->FindBy([
            new BindVariable('IdListaCliente','=',$IdLista)
        ]);


        ## VALIDAMOS SI LA LISTA REALMENTE EXISTE, EN CASO DE ERROR, NEGAMOS EL ACCESO A LA VISTA
        if (empty($DatosLista)){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_UNAUTHORIZED,message:'No esta autorizado a ver la información');
        } 

        ## UNA VEZ VALIDADA LA EXISTENCIA DE LA LISTA, PROCEDEMOS A BUSCAR LA INFORMACIÓN EN LA BBDD

        if ($DatosLista->IdPropietario == $UsuarioDto->IdUsuario || $DatosLista->IdResponsable == $UsuarioDto->IdUsuario || PerfilSvc::HasAnyRol($UsuarioDto->Perfil,[RolesEnum::R_ADMIN_VER_DETALLE_LISTA, RolesEnum::R_ADMIN_VER_TODAS_LAS_LISTAS])){
            $DatosDetalleLista      =   $DetalleListaSvc->GetBy([
                new BindVariable('IdListaCliente','=',$IdLista)
            ],'OrdenEstadoGestion ASC') ;
        }else{
            $DatosDetalleLista      =   $DetalleListaSvc->GetBy([
                new BindVariable('IdListaCliente','=',$IdLista),
                new BindVariable('IdUsuarioResponsable','=',$UsuarioDto->IdUsuario)

            ],'OrdenEstadoGestion ASC');
        }


        $DatosDetalleLista      =   new GenericCollection(
            DtoName     :   VWDetalleListasDto::class,
            Key         :   'IdDetalleListaCliente',
            Values      :   $DatosDetalleLista->Values ?: []
        ) ;


        ## UNA VEZ QUE TENEMOS LA LISTA DE CLIENTES, PROCEDEMOS A BUSCAR LOS ULTIMOS REGISTROS

        #$DatosDetalleLista      =   $this->BuscarUltimoServicio($DatosDetalleLista);


        ## RETORNAMOS EL RESULTADO DE LA CONSULTA
        return (object)[
            "Lista"         => $DatosLista,
            "Contenido"     => $DatosDetalleLista
        ];



        
    }


    ## FUNCIÓN QUE NOS PERMITE BUSCAR EL ULTIMO SERIVIO DESDE UNA COLECCIÓN DE 
    ## CLIENTRES EXTRAIDOS DESDE LA BBDD (TABLA DETALLELISTACLIENTES)
    public function BuscarUltimoServicio(GenericCollection $DetalleLista) {

        ## TRAEMOS EL SERVICIO CORRESPONDIENTE
        $ServicioSvc        =   new ServicioSvc(ConnectionEnum::DEFAULT);



        $IdsClientes        =   array_map(function(VWDetalleListasDto $x){
            return $x->IdCliente;
        },$DetalleLista->Values);

        $IdsClientes        =   array_filter($IdsClientes);
        $IdsClientes        =   array_unique($IdsClientes);


        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN EN LA BBDD
        $UltimosServicios   =   $ServicioSvc->BuscarUltimoServicio($IdsClientes);

        ## PROCEDEMOS A VINCULAR LOS DATOS

        $datos  =   array_map(function(VWDetalleListasDto $element) use($UltimosServicios) {

            $stop = 1;

            $founded    =   array_map(function($z) use($element){

                $stop = 1;
                if ( $element->IdCliente == $z['IdCliente'] ) {
                    return $z;
                }else{
                    return null;
                }

            }, $UltimosServicios );


            $founded    =   array_filter($founded);

            if(!empty($founded)){

                $founded = $founded[array_keys($founded)[0]];

                $element->IdCliente         =   $founded['IdCliente'];
                $element->IdServicio        =   $founded['IdServicio'];
                $element->FechaServicio     =   $founded['FechaServicio'];
                $element->TipoServicio      =   $founded['TipoServicio'];
                $element->Centro            =   $founded['Centro'];

            }

            return $element; 

        },$DetalleLista->Values );


        return new GenericCollection(
            DtoName :   VWDetalleListasDto::class,
            Key     :   'IdCliente',
            Values  :   $datos
        );

        



    }

    ## FUNCIÓN QUE NOS PERMITE TRAER TODOS LOS TIPOS DE SERVICIO DISPONIBLE
    public function TraerTipoServicio() : GenericCollection{ 

        ## EN PRIMER LUGAR INSTANCIAMOS EL SERVICE CORRESPONDIENTE
        $ListasSvc     =   new TipoServicioSvc(ConnectionEnum::DEFAULT); 


        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $Listas  =   $ListasSvc->GetAll();

        ## por ultimo, retornamos la lista de usuarios
        return $Listas;
    }

    ## FUNCIÓN QUE NOS PERMITE TRAER LOS ESTADOS DE UNA LISTA
    public function TraerEstadoListas() : GenericCollection{

        $EstadosSvc     =   new EstadoListaSvc(ConnectionEnum::DEFAULT); 


        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $Estados  =   $EstadosSvc->GetAll();

        ## por ultimo, retornamos la lista de usuarios
        return $Estados;
    }

    ## FUNCIÓN QUE NOS PERMITE TRAER LA LISTA DE USUARIOS ACCESIBLES POR UN USUARIO EN ESPECIFICO
    public function TraerResponsables(int $IdUsuario) : GenericCollection | null{

        ## EN PRIMER LUGAR INSTANCIAMOS EL SERVICE QUE NOS VA A TRAER LOS RESPONSABLES
        $ListaResponsableSvc    =   new VWTraerResponsablesListasSvc(ConnectionEnum::DEFAULT);

        ## BUSCAMOS LOS RESULTADOS EN LA BBDD
        return  $ListaResponsableSvc->BuscarResponsablesDisponibles($IdUsuario);

    }

    public function ValidarListasDisponibles(int $IdUsuario) : bool {

        ## EN PRIMER LUGAR INSTANCIAMOS EL SERVICE QUE NOS PERMITE CONSULTAR CON LA BBDD
        $VWEstadoListaSvc   =   new VWEstadoListasSvc(ConnectionEnum::DEFAULT);

        ## PROCEDEMOS A CONSULTAR SI EL USUARIO TIENE ACCESO A ALGUNA LISTA
        $Estado             =   $VWEstadoListaSvc->ValidarSiExistenListas($IdUsuario);

        return $Estado;

    }


    ## FUNCIÓN QUE NOS PERMITE BUSCAR INFORMACIÓN EN LA BBDD
    public function BuscarListas(ParametrosFiltrarListasFilterDto $Filtros): GenericCollection | null {

        ## NE PRIMER LUGAR INSTANCIAMOS EL SERVICE QUE DEBEMOS USAR
        $VWEstadoListaSvc       =   new VWEstadoListasSvc(ConnectionEnum::DEFAULT);

        ## PROCEDEMOS A GENERAR UN QUERYABLE SEGUN LAS CONDICIONES SOLICITADAS
        $QHERE                  =   [];


        ## VALIDAMOS SI EXISTE LA CONDICIÓN DE FECHA DE REACIÓN 
        if(!empty($Filtros->FechaDesde)) { 

            $FechaInicio    =   (new DateTime($Filtros->FechaDesde))->format('Y-m-d');;

            array_push($QHERE, "DATE(FechaCreacion) = DATE('$FechaInicio')" );
        }

        ## VALIDAMOS SI SE HA SELECCIONADO UN FILTRO DEL AREA
        if( !empty($Filtros->IdTipoServicio) && !in_array("-1", $Filtros->IdTipoServicio)){

            ## LIMPIAMOS DATOS VACIOS Y REPETIDOS
            $TipoServicios      =   array_filter(array_unique($Filtros->IdTipoServicio));
    

            ## PROCEDEMOS A GENERAR LOS QUERYABLES
            $FiltroServicio     =   array_map(function($x){
                return " AreaGrupo RLIKE '$x' ";
            },$TipoServicios);

            $TipoServicios      =  "(" .implode(" AND ",$FiltroServicio) . ")"; 

            ## LOS INCORPORAMOS AL QWHERE
            array_push($QHERE,$TipoServicios);

        }

        ## VALIDAMOS Y FILTRAMOS POR LOS USUARIOS RESPONSABLES
        if( !empty($Filtros->IdUsuario) && !in_array("-1", $Filtros->IdUsuario)){ 

            $IdUsuario      =   implode(" , ",$Filtros->IdUsuario);
            
            array_push($QHERE, " IdUsuario IN ($IdUsuario) " );
        }

        ## VALIADMOS Y FILTRAMOS POR EL ESTADO DE LA LISTA 
        if( !empty($Filtros->IdEstadoLista) && !in_array("-1", $Filtros->IdEstadoLista)){ 
            $IdEstadoLista      =   implode(" , ",$Filtros->IdEstadoLista);

            array_push($QHERE, " IdEstadoLista IN ($IdEstadoLista) " );
        }

        ## VALIDAMOS Y FILTRAMOS POR LOS CONTACTOS PENDIENTES 
        if( !empty($Filtros->contactospendientes) && $Filtros->contactospendientes == 1){ 
            array_push($QHERE, "Avance < 100");
        }else{
            array_push($QHERE, "Avance = 100");
        }

        ## UNA VEZ VALIDADO LOS FILTROS, PROCEDEMOS A BUSCAR LA INFORMACIÓN EN LA BBDD
        $datos      =    $VWEstadoListaSvc->BuscarListas($QHERE, Session::Instance()->usuario->IdUsuario  );

        return $datos;


    }

 
    ## FUNCIÓN QUE NOS PERMITE CAMBIAR EL ESTADO DE UNA LISTA
    public function CambiarEstadoLista(int $IdLista) : bool {

        $ListaClientesSvc           =   new ListaClientesSvc(ConnectionEnum::DEFAULT);
        $LogSvc                     =   new ListaLogSvc(ConnectionEnum::DEFAULT);
        $Usuario                    =   Session::Instance()->usuario; 
         
        ## EN PRIMER LUGAR VALIDAMOS SI EL USUARIO TIENE ACCESO A LA LISTA
        $HasAccess                  =   $ListaClientesSvc->ValidarAccesoLista(
            IdLista :   $IdLista,
            Usuario :   $Usuario
        );

        if ($HasAccess != true){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_UPDATE, message:'Sin acceso al recurso');
        }

        ## EN CASO DE QUE EXISTA AUTORIZACIÓN PROCEDEMOS A BUSCAR LA LISTA
        $ListaDto           =   $ListaClientesSvc->FindByForeign('IdListaCliente',$IdLista);

        ## UNA VEZ QUE TENEMOS EL DTO PROCEDEMOS A CAMBIAR EL ESTADO
        $stio = 1;

        $status                     =   $ListaDto->IdEstadoLista == EstadoListaEnum::DESACTIVA ? EstadoListaEnum::ACTIVA : EstadoListaEnum::DESACTIVA;
        $status_response            =   $ListaDto->IdEstadoLista == EstadoListaEnum::DESACTIVA ? true : false;

        $ListaDto->IdEstadoLista    =   $status;

        GenericSvc::BeginMultipleOperations(ConnectionEnum::DEFAULT);

        try{

            ## ACTUALIZAMOS LOS DATOS EN LA BBDD
            $ListaClientesSvc->Update($ListaDto);

            ## 
            $ListaLogDto    =   new ListaLogDto(
                IdUsuario           :   $Usuario->IdUsuario,
                IdTipoLog           :   TipoLogEnum::CAMBIO_ESTADO_LISTA,
                DescripcionAccion   :   "La lista ha sido desactivada",
                Detalle             :   "La lista ha sido desactivada",
                IdListaCliente      :   $IdLista,
                Fecha               :   (new Date('now'))->__toString()
            );

            $LogSvc->Insert($ListaLogDto); 

            GenericSvc::SaveMultipleOperations();

            return $status_response;
        } catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_INSERT, message: "No tiene acceso al recurso");
        }

    }

    ## FUNCIÓN QUE NOS PERMITE REASIGNAR ELEMENTOS DENTRO DE LA BBDD
    public function ReasignarUsuarios(array $ListaClientes, int $IdLista, int $IdUsuario) {
        
 
        
        $IdUsuarioLogged        =   Session::Instance()->usuario->IdUsuario;
        $guid                   =   "tbListaDetalleClientes_".$IdLista;
        $guidSearch             =   "RPHPDATATABLE_".$IdUsuarioLogged. "_" .$guid; 
        ## VALIDAMOS SI EL INPUT ES CORRECTO
        $CacheData              =   RedisDataTable::Instance()->$guidSearch;

        ## EN PRIMER LUGAR INSTANCIAMOS TODOS LOS SERVICES A UTILIZAR
        $ListaSvc               =   new ListaClientesSvc(ConnectionEnum::DEFAULT);
        $DetalleListaSvc        =   new DetalleListaClienteSvc(ConnectionEnum::DEFAULT); 
        
        ## VALIDAMOS LA EXISTENCIA DEL USUARIO
        $UsuarioDto     =   (new UsuarioSvc(ConnectionEnum::CORE))->FindBy([
            new BindVariable('Eliminado','!=',1),
            new BindVariable('IdUsuario','=',$IdUsuario)
        ]) ;

        if (empty($UsuarioDto) or !$UsuarioDto instanceof UsuarioDto){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El usuario seleccionado no existe o no se encuentra disponible');
        }



        ## VALIDAMOS SI LA LISTA SE PUEDE MODIFICAR
        $ListaDto       =   $ListaSvc->FindByForeign('IdListaCliente',$IdLista);

        ## VALIDAMOS SI LA LISTA EXISTET O SE PUEDE MOFICIAR
        if(empty($ListaDto) || $ListaDto->IdEstadoLista == EstadoListaEnum::DESACTIVA){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'No puede modificar una lista desactivada');
        }
        

        GenericSvc::BeginMultipleOperations(ConnectionEnum::DEFAULT);
        try{
            
            ## ACTUALIZAMOS LOS DATOS EN LA BBDD
            foreach($ListaClientes as $item){

                ## OBTENEMOS EL DETALLE QUE DEBEMOS MODIFICAR
                $Detalle        =   $DetalleListaSvc->FindByForeign('IdDetalleListaCliente',$item);

                if (empty($Detalle) || !$Detalle instanceof DetalleListaClienteDto || $Detalle->IdEstadoGestion == EstadoGestionClienteListaEnum::CONTACTADO)  {
                    throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_INSERT, message: "uno o más de los recursos no pueden ser modificados o no existen");
                }
                
                ## ASIGNAMOS EL NUEVO USUARIO QUE CORRESPONDE
                $Detalle->IdUsuario     =   $IdUsuario;

                ## ACTUALIZAMOS EL REGISTRO EN LA BBDD
                $DetalleListaSvc->Update($Detalle);

            }

            ## ACTUALIZAMOS LOS DATOS EN EL CACHE DE  

            $CacheData->Values      =   array_map(function($x) use($ListaClientes, $UsuarioDto) { 
            
                if (in_array($x['IdDetalleListaCliente'], $ListaClientes)){
                    $x['SiglaUsuarioResponsable']   = $UsuarioDto->Sigla;
                    $x['NombreUsuarioResponsable']  = $UsuarioDto->Nombre;
                    $x['IdUsuarioResponsable']      = $UsuarioDto->IdUsuario;
                };

                return $x;

            },$CacheData->Values);


            GenericSvc::SaveMultipleOperations(); 
            RedisDataTable::Instance()->$guid   =   $CacheData;

            $template   =  '
            <center>
                <div class="profile-circle">
                    '. $UsuarioDto->Sigla .'
                </div>
            </center>';

            return $template;
        } catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_INSERT, message: "No es posible asignar estos recursos");
        }
        


    }


    ## FUNCIÓN QUE NOS PERMITE CAMBIAR EL ESTADO DE LAS GESTIONE REALIZADAS
    public function CambiarEstadoGestion(CambiosGestionFilterDto $datos) : bool{


        ## EN PRIMER LUGAR NOS TRAEMOS EL USUARIO QUE INICIO SESIÓN
        $UsuarioDto             =   Session::Instance()->usuario;

        ## EN SEGUNDO LUGAR, INSTANCIAMOS TODOS LOS SERVICES QUE VAMOS A USAR
        $DetalleListaSvc        =   new DetalleListaClienteSvc(ConnectionEnum::DEFAULT);

        $ClienteSvc             =   new ClientesSvc(ConnectionEnum::DEFAULT);

        $ListaDetalleLogSvc     =   new ListaDetalleLogSvc(ConnectionEnum::DEFAULT);

        $now                    =   (new Date('now'))->__toString();

        
        ## EN PRIMER LUGAR PROCEDEMOS A BUSCAR EL DTO QUE VAMOS A MODIFICAR
        $DetalleDto             =   $DetalleListaSvc->Find($datos->IdDetalleLista);
        $IdUsuarioAsignado      =   $DetalleDto->IdUsuario;

        if (
            Session::Instance()->usuario->IdUsuario != $IdUsuarioAsignado || 
            !PerfilSvc::HasAnyRol(Session::Instance()->usuario->Perfil,[
                RolesEnum::R_ADMIN_VER_TODAS_LAS_LISTAS,
                RolesEnum::R_USER_GESTIONAR_CLIENTES
            ])
        ){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_READ,message:"RECURSO NO ENCONTRADO");   
        }

        ## VALIDAMOS QUE EL DETALLE REALMENTE EXISTA
        if (empty($DetalleDto) || !$DetalleDto instanceof DetalleListaClienteDto || $DetalleDto->IdEstadoGestion == EstadoGestionClienteListaEnum::CONTACTADO){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_READ,message:"RECURSO NO ENCONTRADO");
        }

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DEL CLIENTE
        $ClienteDto         =   $ClienteSvc->Find($DetalleDto->IdCliente);

        ## VALIDAMOS SI EL CLIENTE EXISTE O ESTA ACTIVO
        if (empty($ClienteDto) || !$ClienteDto instanceof ClientesDto){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_READ,message:"RECURSO NO ENCONTRADO");
        }            


        ## VALIDAMOS SI EXISTEN DIFERENCIAS ENTRE LOS DATOS DEL CLIENTE
        $IndDiffNombre      =   trim(strtolower($ClienteDto->NombreCliente))    != trim(strtolower($datos->NombreCliente))      ?   true    :   false;
        $IndDiffCorreo      =   trim(strtolower($ClienteDto->CorreoCliente))    != trim(strtolower($datos->Correo))             ?   true    :   false;
        $IndDiffNumero      =   trim(strtolower($ClienteDto->TelefonoPrimario)) != trim(strtolower($datos->NumeroContacto))     ?   true    :   false;

        ## VALIDAMOS SI EXISTE UN CAMBIO EN EL ESTADO DE LA GESTIÓN
        $IndDiffEstado      =   $DetalleDto->IdEstadoGestion    !=  $datos->EstadoGestion   ?   true    :   false;
        $IndDiffCanal       =   $DetalleDto->FormaContacto      !=  $datos->Canal           ?   true    :   false;

        ## VALIDAMOS SI REALMENTE EXISTEN CAMBIOS EN LA BBDD
        if ( $IndDiffCanal == false && $IndDiffNombre == false && $IndDiffCorreo == false && $IndDiffNumero == false && $IndDiffEstado == false ){
            ## EN CASO QUE NO EXISTAN DIFERENCIAS, PROCEDEMOS A RETORNAR VERDADERO
            return true;
        }
 
        GenericSvc::BeginMultipleOperations(ConnectionEnum::DEFAULT); 
        
        $EstadoAnterior     =   null;

        try{

            ## EN CASO DE QUE SI EXISTA UNA DIFERENCIA, PROCEDEMOS A CAMBIAR LOS PARAMETROS

            ## INICIAMOS EL PROCESO CON EL ESTADO DE GESTIÓN
            if ($IndDiffEstado == true || $IndDiffCanal == true){

                $EstadoAnterior                 =   $DetalleDto->IdEstadoGestion;
                ## CAMBIAMOS EL ESTADO DE GESTIÓN EN EL OBTENO
                $DetalleDto->IdEstadoGestion    =   $datos->EstadoGestion;
                $DetalleDto->FormaContacto      =   $datos->Canal;

                ## ACTUALIZAMOS EL CAMBIO EN LA BASE DE DATOS
                $DetalleListaSvc->Update($DetalleDto);


            }

            ## PROCEDEMOS A GUARDAR LOS CAMBIOS REALIZADOS EN EL CLIENTE
            if ( $IndDiffNombre == true || $IndDiffCorreo == true || $IndDiffNumero == true ){


                $ClienteDto->CorreoCliente      =   $IndDiffCorreo      ==  true    ?   strtolower($datos->Correo)                  :   $ClienteDto->CorreoCliente;
                $ClienteDto->TelefonoPrimario   =   $IndDiffNumero      ==  true    ?   $datos->NumeroContacto                      :   $ClienteDto->TelefonoPrimario;
                $ClienteDto->NombreCliente      =   $IndDiffNombre      ==  true    ?   ucwords($datos->NombreCliente)              :   ucwords($ClienteDto->NombreCliente);
                $ClienteSvc->Update($ClienteDto);

            }

            ## UNA VEZ ACTUALIZADO, GUARDAMOS UN REGISTRO DEL CAMBIO REALIZADO
            $ListaDetalleLogSvc->Insert(
                new ListaDetalleLogDto(
                    IdListaCliente      :   $datos->IdListaClientes,
                    IdUsuario           :   $UsuarioDto->IdUsuario,
                    IdDetalleLista      :   $datos->IdDetalleLista,
                    IdEstadoAnterior    :   $EstadoAnterior,
                    IdEstadoActual      :   $datos->EstadoGestion,
                    Descripcion         :   "EL ESTADO DEL CLIENTE HA SIDO MODIFICADO",
                    IdTipoLog           :   TipoLogEnum::CAMBIO_ESTADO_GESTION,
                    NumeroAnterior      :   $IndDiffNumero == true ? $ClienteDto->TelefonoPrimario  : null,
                    CorreoAnterior      :   $IndDiffCorreo == true ? $ClienteDto->CorreoCliente     : null,
                    NombreAnterior      :   $IndDiffNombre == true ? $ClienteDto->NombreCliente     : null,
                    IndCambioNumero     :   $IndDiffNumero,
                    IndCambioNombre     :   $IndDiffNombre,
                    IndCambioCorreo     :   $IndDiffCorreo,
                    Fecha               :   $now
                )
            );
                

            

            GenericSvc::SaveMultipleOperations();

            $guid           =   "tbListaDetalleClientes_".$datos->IdListaClientes;
            $UsuarioId      =   Session::Instance()->usuario->IdUsuario;
            $guidSearch     =   "RPHPDATATABLE_".$UsuarioId."_".$guid; 
            ## VALIDAMOS SI EL INPUT ES CORRECTO
            $CacheData      =   RedisDataTable::Instance()->$guidSearch;

            $CacheData->Values      =   array_map(function($x) use($datos) { 
                
                if ($x['IdDetalleListaCliente'] == $datos->IdDetalleLista){ 

                    $Estado                             =   "SIN GESTIONAR";

                    switch($datos->EstadoGestion){
                        case EstadoGestionClienteListaEnum::CONTACTADO:
                            $Estado     =   "CONTACTADO";
                            break;
                        case EstadoGestionClienteListaEnum::CONTACTO_INCORRECTO:
                            $Estado     =   "CONTACTO INCORRECTO";
                            break;
                        case EstadoGestionClienteListaEnum::NO_CONTESTA:
                            $Estado     =   "NO CONTESTA";
                            break;
                        case EstadoGestionClienteListaEnum::NO_EXISTE:
                            $Estado     =   "NO EXISTE";
                            break;
                        case EstadoGestionClienteListaEnum::SIN_GESTIONAR:
                            $Estado     =   "SIN GESTIONAR";
                            break;

                    }

                    $x['IdEstadoGestion']               =   $datos->EstadoGestion;
                    $x['NombreCliente']                 =   ucwords($datos->NombreCliente);
                    $x['TelefonoPrimario']              =   $datos->NumeroContacto;
                    $x['CorreoCliente']                 =   $datos->Correo;
                    $x['EstadoGestion']                 =   $Estado;

                };

                return $x;

            },$CacheData->Values); 

            RedisDataTable::Instance()->$guid   =   $CacheData; 

            return true;

        } catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'Parametros invalidos');
        }

        return true;

    }

    public function GuardarNuevaObservacion(NuevaObservacionFilterDto $datos) : bool {

        ## INSTANCIAMOS EL SERVICE A GENERAR
        $ObservacionSvc         =   new ObservacionClienteSvc(ConnectionEnum::DEFAULT);
        $ListaLogSvc            =   new ListaLogSvc(ConnectionEnum::DEFAULT);
        $DetalleSvc             =   new DetalleListaClienteSvc(ConnectionEnum::DEFAULT);
        

        ## OBTENEMOS EL ID DEL USUARIO QUE
        $LoginUser              =   Session::Instance()->usuario;
        $DetalleDto             =   $DetalleSvc->FindBy("IdDetalleListaCliente",$datos->IdDetalleLista);


        GenericSvc::BeginMultipleOperations(ConnectionEnum::DEFAULT);

        try{    

            $now        =   (new Date('now'))->__toString();
            
            $ObservacionSvc->Insert(
                new ObservacionClienteDto(
                    IdCliente           :   $datos->IdCliente,
                    IdUsuario           :   $LoginUser->IdUsuario,
                    IdTIpoObservacion   :   $datos->IdTipoObservacion ?: null,
                    Observacion         :   $datos->Descripcion,
                    Fecha               :   $now,
                    Titulo              :   $datos->Titulo
                ) 
            ) ;

            $ListaLogSvc->Insert(
                new ListaLogDto(
                    IdUsuario           :   $LoginUser->IdUsuario,
                    IdTipoLog           :   TipoLogEnum::AGREGAR_COMENTARIO_CLIENTE,
                    DescripcionAccion   :   "Se agrego un nuevo comentario al cliente",
                    Detalle             :   "Observación Agregada",
                    IdListaCliente      :   null,
                    Fecha               :   $now,
                )
            ) ;

            GenericSvc::SaveMultipleOperations();

            return true;

        } catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations(); 
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT,message:"SE HA GENERADO UN PROBLEMA AL ALMACENAR LOS CAMBIOS");
        }


        return true;

    }


    ## OBTENEMOS LAS OBSERVACIONES DE UN CLIENTE A TRAVEZ DE UN SERVICIO
    public function ObtenerObservacionesCliente(int $IdCliente) : GenericCollection | null {
  
        return ( new VWObservacionesClientesSvc(ConnectionEnum::DEFAULT))->GetBy([
            new BindVariable('IdCliente','=',$IdCliente)
        ],"Fecha DESC");

    }

    public static function TransformarDiffAMensaje(DateInterval $Diff){
        $stop = 1;

        $Niveles    =   [
            (object)[
                "Active"        =>  $Diff->y ? true : false,
                "Diff"          =>  $Diff->y,
                "Icon"          =>  "Año",
                "Plural"        =>  "s"
            ],
            (object)[
                "Active"        =>  $Diff->m ? true : false,
                "Diff"          =>  $Diff->m,
                "Icon"          =>  "Mes",
                "Plural"        =>  ""
            ],
            (object)[
                "Active"        =>  $Diff->d ? true : false,
                "Diff"          =>  $Diff->d,
                "Icon"          =>  "Día",
                "Plural"        =>  "s"
            ],
            (object)[
                "Active"        =>  $Diff->h ? true : false,
                "Diff"          =>  $Diff->h,
                "Icon"          =>  "Hora",
                "Plural"        =>  "s"
            ],
            (object)[
                "Active"        =>  true,
                "Diff"          =>  $Diff->i,
                "Icon"          =>  "Minuto",
                "Plural"        =>  "s"
            ],
        ];

        $Mensaje        =   array_filter($Niveles,function($item){
            return $item->Active;
        });
        
        $Mensaje        =   array_filter($Mensaje);

        return $Mensaje[min(array_keys($Mensaje))]; 
 
        
        
        
        
 
    }


    ## ESTA FUNCIÓN NOS PERMITE BUSCAR UN CLIENTE DEPENDIENDO DEL TIPO DE BUSQUEDA
    public function BuscarClientePorCategoria(string $Categoria, string $Val){

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN        
        $Datos = null;

        switch($Categoria){
            case "Rut":
                $Service        =   new ClientesSvc(ConnectionEnum::DEFAULT);
                $Datos          =   $Service->GetBy([
                    new BindVariable('Rut','=',$Val)
                ]);
                break;
            case "Patente":
                
                ## INSTANCIAMOS EL SERVICIO
                $Service        =   new FBBPatentesPorClientesSvc(ConnectionEnum::DEFAULT);
                
                ## BUSCAMOS LAS PATENTES QUE PUEDEN TENER CLIENTES
                $Datos          =   $Service->GetBy([
                    new BindVariable('Patente','=',$Val)
                ]);

                ## VERIFICAMOS QUE VENGAN DATOS
                if(empty($Datos)) {
                    throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'Parametros invalidos');
                }

                ## CONVERTIMOS EL OBJETO A ARRAY
                $ListaIDs   =   array_map(function($x){
                    return $x->IdCliente;
                }, $Datos->Values);

                $ListaIDs   =   array_filter($ListaIDs);

                if (empty($ListaIDs)){
                    throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER,message:'Parametros invalidos');
                }

                ## PROCEDEMOS A BUSCAR LOS POSIBLES CLIENTES

                $Datos      =   (new ClientesSvc(ConnectionEnum::DEFAULT))->GetBy([
                    new BindVariable('IdClientes','IN',$ListaIDs)
                ]); 

                break;
        }


        return $Datos;



    }


    public function TraerPefilCliente(int $IdCliente) : PerfilClienteFilterDto { 

        ## BUSCAMOS EL HISTORIAL DEL CLIENTE
        $HistorialCliente       =   ( new VWServiciosEntregadosPorClienteSvc(ConnectionEnum::DEFAULT) )->GetByForeign('IdCliente',$IdCliente);
        $ClienteDto             =   (new ClientesSvc(ConnectionEnum::DEFAULT))->FindBy([
            new BindVariable('IdClientes','=',$IdCliente)
        ]);
 
        return new PerfilClienteFilterDto(
            Cliente     :   $ClienteDto,
            Historial   :   $HistorialCliente
        ) ; 

    }


    ## FUNCIÓN QUE NOS PERMITE GENERAR LA LISTA DE BADGES DE LOS SERVICIOS
    public static function GenerarWidgetsBadgeDetalleServicio(VWServiciosEntregadosPorClienteDto $datos) : array {

        $response       =   [];


        $IncludeModelo  =   true;
        $IncludeMarca   =   true;


        switch($datos->IdTipoServicio){ 

            ## ---------------------------------------------------------------------------------------------------------------------------
            ## WIDGETS VENTA NUEVOS
            ## ---------------------------------------------------------------------------------------------------------------------------
            case TipoServicioEnum::Venta_Nuevos:
                if(!empty($datos->PrecioLista)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong>Precio Lista:</strong> $".number_format($datos->PrecioLista,0,",",".")."</span>";
                }
                break;
            ## --------------------------------------------------------------------------------------------------------------------------- 
            ## ---------------------------------------------------------------------------------------------------------------------------
            ##  CONSTRUCCIÓN WIDGETS DE CREDITOS USADOS 
            ## ---------------------------------------------------------------------------------------------------------------------------
            case TipoServicioEnum::Creditos_Usados:
                
                if(!empty($datos->NombreProducto)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong>Producto:</strong> ".$datos->NombreProducto."</span>";
                }
                if(!empty($datos->MontoPagare)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong>Pagaré:</strong> $".number_format($datos->MontoPagare,0,",",".")."</span>";
                } 
                if(!empty($datos->Pie)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong>Pie:</strong> $". number_format($datos->Pie,0,",",".") ."</span>";
                }
                if(!empty($datos->ValorCuota)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong>Valor Cuota:</strong> $". number_format($datos->ValorCuota,0,",",".") ."</span>";
                }
            
                if(!empty($datos->Plazo)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong>Cuotas:</strong> ".$datos->Plazo."</span>";
                }

                if(!empty($datos->Sueldo)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong>Sueldo Registrado:</strong> $".number_format($datos->Sueldo,0,",",".")."</span>";
                }

                 

                break;
            ## ---------------------------------------------------------------------------------------------------------------------------
            ## ---------------------------------------------------------------------------------------------------------------------------
            ## WIDGETS VENTA USADOS
            ## ---------------------------------------------------------------------------------------------------------------------------
            case TipoServicioEnum::Venta_Usados: 
               

                if(!empty($datos->MarcaRetoma)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong> Marca Retoma: </strong> ".$datos->MarcaRetoma."</span>";
                }

                if(!empty($datos->ModeloRetoma)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong> Modelo Retoma: </strong> ".$datos->ModeloRetoma."</span>";
                }

                if(!empty($datos->AnioRetoma)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong> Año Retoma: </strong> ".$datos->AnioRetoma."</span>";
                }
                break; 
            ## ---------------------------------------------------------------------------------------------------------------------------  
            ## --------------------------------------------------------------------------------------------------------------------------- 
            ## CREDITOS NUEVOS
            ## --------------------------------------------------------------------------------------------------------------------------- 
            case TipoServicioEnum::Creditos_Nuevos:
                if(!empty($datos->NombreProducto)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong> Producto: </strong> ".$datos->NombreProducto."</span>";
                }
                if(!empty($datos->Pie)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong> Pie: </strong> $". number_format($datos->Pie,0,",",".") ."</span>";
                }
                if(!empty($datos->MontoPagare)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong> Pagaré: </strong> $".number_format($datos->MontoPagare,0,",",".")."</span>";
                } 
                if(!empty($datos->Plazo)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'><strong> Cuotas: </strong> ".$datos->Plazo."</span>";
                }
                break;
            
            case TipoServicioEnum::Postventa_GT:
                if(!empty($datos->KilometrajeGT)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong>Kilometraje:</strong>  ".number_format($datos->KilometrajeGT,0,",",".")."</span>";
                }
                break;
            case TipoServicioEnum::Venta_Meson:
                $IncludeModelo = false;
                if(!empty($datos->FamiliaVentaMeson)){
                    $response[] =   "<span class='badge rounded-pill badge-soft-primary'>".number_format($datos->FamiliaVentaMeson,0,",",".")."</span>";
                } 
                break;



            
        }  


        ## VERIFICMOS SI EXISTA EL CHASIS
        if(!empty($datos->NumeroChasis)){
            array_unshift($response,"<span class='badge rounded-pill badge-soft-primary'><strong>Chasis: </strong>".$datos->NumeroChasis."</span>");
        }

        ## VERIFICAMOS SI EXISTE LA PATENTE
        if(!empty($datos->Patente)){
            array_unshift($response,"<span class='badge rounded-pill badge-soft-primary'><strong>Patente:</strong> ".$datos->Patente."</span>");
        }

  

        ## VERIFICAMOS QUE EL MODELO DEL VEHIUCLO VENGA EN LA CONSULTA
        if(!empty($datos->ModeloServicio) && $IncludeModelo == true ){
            array_unshift($response,"<span class='badge rounded-pill badge-soft-primary'>".$datos->ModeloServicio."</span>");
        }
        
        ## VERIFICAMOS SI EXISTE LA MARCA DEL SERVICIO
        if(!empty($datos->MarcaServicio) && $IncludeMarca == true){
            array_unshift($response,"<span class='badge rounded-pill badge-soft-primary'>".$datos->MarcaServicio."</span>");
        }


        ## VERIFICMOS SI EXSITE EL PRECIO DE LISTA
        if(!empty($datos->PrecioLista)){
            $response[] =   "<span class='badge rounded-pill badge-soft-primary'> <strong> Precio Lista: </strong> $".number_format($datos->PrecioLista,0,",",".")."</span>";
        }




        


        return $response;
    }

}