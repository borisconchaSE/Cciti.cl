<?php

use Application\BLL\DataTransferObjects\Core\departamentoDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\modeloDto;
use Application\BLL\DataTransferObjects\Core\ubicacionDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldDate;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\FormOnChangeEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\PopUpContent;
use Intouch\Framework\Widget\Text;

$BodyContent     =   new Container(
    Classes     :   ['alert alert-danger label-arrow'],
    Attributes  :   ['role','alert'],
    Children    : [
        new Text('Error al visualizar los datos del producto')
    ]
);

## INSTANCIAMOS EL DISPLAY QuE NOS PERMITE GENERAR GRAFICOS, TABLAS y FORMULARIOS
$display        =   new Display();


## -------------------------------------------------------------------------------
## GENERAMOS LOS VALORES POR DEFECTO DE LOS INPUTS
## -------------------------------------------------------------------------------
if(!empty($data->Stock->tipo)){

    if(($data->Stock->tipo) == "Original" ){
        $Tipo   = "Original";
    }else{
        $Tipo   = "Alternativo";
    }


}



if(!empty($data->IdEmpresa_U->Values)){

    $EmpresaU   =   $data->IdEmpresa_U->Values;
    $EmpresaU   =   $EmpresaU[0]->IdEmpresa;

}else{
    $EmpresaU   =   -1;
}

if(!empty($data->Departamento->Values)){

    $Depto      =   $data->Departamento->Values;
    $Depto      =   $Depto[0]->idDepto;

}else{
    $Depto      =   -1;
}

if(!empty($data->Ubicacion->Values)){

    $Ubi        =   $data->Ubicacion->Values;
    $Ubi        =   $Ubi[0]->idubicacion;

}else{
    $Ubi        =   -1;
}




if(!empty($data->Stock->estado_stock)){

    if(($data->Stock->estado_stock) == "En Stock" ){
        $estado   = "En Stock";
    }else{
        $estado   = "Entregado";
    }


}

$Empresa = [
    new empresaDto(
        IdEmpresa           :   -1,
        Descripcion         :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosEmpresa)){

    $Empresa   = array_merge($Empresa,$data->DatosEmpresa->Values);

    $Empresa   =   new GenericCollection(
        DtoName     :   empresaDto::class,
        Key         :   'IdEmpresa',
        Values      :   $Empresa
    ) ;
}



$Area = [
    new departamentoDto(
        idDepto             :   -1,
        Descripcion         :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosArea)){

    $Area   = array_merge($Area,$data->DatosArea->Values);

    $Area   =   new GenericCollection(
        DtoName     :   departamentoDto::class,
        Key         :   'idDepto',
        Values      :   $Area
    ) ;
}

$Ubicacion = [
    new ubicacionDto(
        idubicacion         :   -1,
        Descripcion         :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosUbicacion)){

    $Ubicacion   = array_merge($Ubicacion,$data->DatosUbicacion->Values);

    $Ubicacion   =   new GenericCollection(
        DtoName     :   ubicacionDto::class,
        Key         :   'idubicacion',
        Values      :   $Ubicacion
    ) ;
}

$Marca = [
    new marcaDto(
        idMarca         :   -1,
        Descripcion     :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosMarca)){

    $Marca   = array_merge($Marca,$data->DatosMarca->Values);

    $Marca   =   new GenericCollection(
        DtoName     :   marcaDto::class,
        Key         :   'idMarca',
        Values      :   $Marca
    ) ;
}

$Modelo = [
    new modeloDto(
        idModelo        :   -1,
        Descripcion     :   'Sin Seleccionar',
        idMarca         :   100
    )
];

if(!empty($data->DatosModelo)){

    $Modelo   = array_merge($Modelo,$data->DatosModelo->Values);

    $Modelo   =   new GenericCollection(
        DtoName     :   modeloDto::class,
        Key         :   'idModelo',
        Values      :   $Modelo
    ) ;
}

$EmpresaData    =   $data->Stock->IdEmpresa;
$MarcaData      =   $data->Stock->idMarca;
$ModeloData         =   $data->Stock->idModelo;
$FechaData      =   $data->Stock->Fecha_asignacion;
$FechaData      =   date("d-m-Y", strtotime($FechaData));

## VALIDAMOS SI EXISTE O NO LA INFORMACIÓN DEL PRODUCTO ENTREGADO
if($data->Stock != null){

    ## AGREGAMOS EL BTN QUE NOS PERMITE GUARDAR LOS CAMBIOS
    $display->AddButton(
        new FormButton(
            Key             :   'btnGuardarCambiosStock',
            FormKey         :   'frmEditarStockEntregado',
            Child           :   new Text('Guardar Cambios'),
            ButtonStyle     :     ButtonStyleEnum::BUTTON_SOFT_SUCCESS,
            Events          :   [
                new FormButtonOnClickEvent()
            ]
        )
    );

    // GENERAMOS LA ESTRUCTURA QUE TENDRA EL FORMULARIO PARA EDITAR STOCK
   $display->AddFormFromObject( 
        formKey         :   'frmEditarStockEntregado',
        object          :   (object)[ 
            "id_stock"          =>  $data->Stock->id_stock,
            "Descripcion"       =>  $data->Stock->Descripcion,
            "Fecha_asignacion"  =>  $data->Stock->Fecha_asignacion,
            "Cantidad"          =>  $data->Stock->Cantidad,
            "Precio_Unitario"   =>  $data->Stock->Precio_Unitario, 
            "estado_stock"      =>  $data->Stock->estado_stock,
            "IdEmpresa"         =>  $data->Stock->IdEmpresa, 
            "IdEmpresaU"        =>  $EmpresaU,
            "idMarca"           =>  $MarcaData,
            "idModelo"          =>  $ModeloData,
            "idDepto"           =>  $Depto,
            "idubicacion"       =>  $Ubi

         ],
        keyFieldName    :   'id_stock',
        rowGroups       :   [ 
            new FormRowGroup(
                Key: 'frg-informaciónEditarStock', 
                Rows: [
                    [
                        new FormRowFieldHidden(
                            PropertyName    :   'id_stock',
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Descripcion',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Nombre Producto',
                            Required        :   true,
                            Colspan         :   4
                        ), 
                        new FormRowFieldDate(
                            PropertyName    :   'Fecha_asignacion',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_DATE,
                            Label           :   'Fecha Entrega',
                            Placeholder     :   $FechaData,    
                            Required        :   true,
                            Colspan         :   4, 
                            Events          :   [
                                new FormOnChangeEvent()
                            ]
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'tipo',
                            Label: 'Tipo',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : [
                                    (object)    [
                                        "tipo"              =>   "Original",
                                        "Descripcion"       =>   "Original"
                                    ],
                                    (object)    [
                                        "tipo"              =>   "Alternativo",
                                        "Descripcion"       =>   "Alternativo"
                                    ]
                                ],
                                Key             : 'tipo',
                                Description     : 'Descripcion',
                                SelectedValue   : $Tipo,
                                DisplaySearch   : true
                            ),
                            Events      :   [new FormOnChangeEvent()]
                        )
                    ],
                    [
                        new FormRowFieldText(
                            PropertyName    :   'Cantidad',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Cantidad',
                            Required        :   true,
                            Colspan         :   4
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Precio_Unitario',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Precio Unitario',
                            Required        :   true,
                            Colspan         :   4,
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idMarca',
                            Label: 'Marca',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Marca,
                                Key             : 'idMarca',
                                Description     : 'Descripcion',
                                SelectedValue   : $MarcaData,
                                DisplaySearch   : true
                            ), 
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idModelo',
                            Label: 'Modelo',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Modelo,
                                Key             : 'idModelo',
                                Description     : 'Descripcion',
                                SelectedValue   : $ModeloData,
                                DisplaySearch   : true
                            ), 
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'estado_stock',
                            Label: 'Estado Producto',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : [
                                    (object)    [
                                        "tipo"              =>   "En Stock",
                                        "Descripcion"       =>   "En Stock"
                                    ],
                                    (object)    [
                                        "tipo"              =>  "Entregado",
                                        "Descripcion"       =>  "Entregado"
                                    ]
                                ],
                                Key             : 'tipo',
                                Description     : 'Descripcion',
                                SelectedValue   : $estado,
                                DisplaySearch   : true
                            ),
                            Events      :   [new FormOnChangeEvent()]
                        ),
                    ],
                    [
                        new FormRowFieldSelect(
                            PropertyName    :   'IdEmpresa',
                            Label           :   'Empresa Producto',
                            Colspan         :   4,
                            Required        : true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Empresa,
                                Key             : 'IdEmpresa',
                                Description     : 'Descripcion',
                                SelectedValue   : $EmpresaData,
                                DisplaySearch   : true
                            ),
                        ),
                        new FormRowFieldSelect(
                            PropertyName    :   'IdEmpresaU',
                            Label           :   'Empresa Usuario',
                            Colspan         :   4,
                            Required        : true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Empresa,
                                Key             : 'IdEmpresa',
                                Description     : 'Descripcion',
                                SelectedValue   : $EmpresaU,
                                DisplaySearch   : true
                            ),
                        ),
                        new FormRowFieldSelect(
                            PropertyName    :   'idDepto',
                            Label           :   'Departamento',
                            Colspan         :   4,
                            Required        : true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Area,
                                Key             : 'idDepto',
                                Description     : 'Descripcion',
                                SelectedValue   : $Depto,
                                DisplaySearch   : true
                            ),
                        ),
                        new FormRowFieldSelect(
                            PropertyName    :   'idubicacion',
                            Label           :   'Ubicación',
                            Colspan         :   4,
                            Required        : true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Ubicacion,
                                Key             : 'idubicacion',
                                Description     : 'Descripcion',
                                SelectedValue   : $Ubi,
                                DisplaySearch   : true
                            ),
                        )
                    ]
                ]
            ) 
        ],
        fillData        :   true
   );

    //AGREGAMOS LA ESTRUCTURA DEL FOMULARIO A UNA VARIABLE U OBJETO
   $BodyContent         =   new Container(
    Children:[
        $display->Widgets()['frmEditarStockEntregado']
    ]
   ) ;

 


} 


// INYECTAMOS LA VARIABLE U OBJETO QUE CONTIENE EL FORMULARIO EN EL POPUP PARA EDITAR EL PRODUCTO
$content = new Container(
    Classes: ['view-content'],
    Styles: [],
    Children: [
        $BodyContent
    ]
);

// GENERAMOS LA ESTRUCTURA DEL POPUP PARA EDITAR EL PRODUCTO
$popUp = new PopUpContent(
    Key                 : 'PopupEditarStock', 
    Title               : 'Editar Producto',
    DismissButtonText   : 'Cerrar',
    DismissButtonStyle  : ButtonStyleEnum::BUTTON_SOFT_PRIMARY,
    SubTitle            : '',
    Buttons             :   [
        $display->Widgets()['btnGuardarCambiosStock']
    ],
    Content             : $content
);

// DIBUJAMOS LOS DATOS QUE TENDRA EL FORMULARIO
$popUp->Draw();

// DIBUJAMOS LOS SCRIPTS GENERADOS POR EL FRAMEWORK
$display->DrawScripts(addLoadEvent:false);