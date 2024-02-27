// -----------------------------------------------------------------------------
// JSON DATA
// -----------------------------------------------------------------------------

window.[[TABLE_KEY]]_process_data = [];
window.[[TABLE_KEY]]template__data = [];
class [[TABLE_KEY]]_TemporalJSComponent_{
 
    dtable      = null;
    
    // metadata
    rowLength   = 0;

    HiddeButtons = [[HIDEBUTTONS]];


    _jsondata   = [];

    _no_render_data = [];

    constructor(){ 
       
    }

    init() { 

        var btnList             = 
        [
            [[CUSTOMBUTTONS]] 
        ];
        
        
        if( this.HiddeButtons == true ){
            btnList = [];
        }
         
        const table             = $('#[[TABLE_KEY]]').DataTable({
            language : {
                "sProcessing":     "Procesando...",
                "sLengthMenu":	   "Mostrar _MENU_",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           " _START_ - _END_ de _TOTAL_ registros",
                "sInfoEmpty":      " 0 - 0 de e 0 registros",
                "sInfoFiltered":   "(filtrado a _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "&nbsp;&nbsp;Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguientes",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            order     : [] ,
            dom         : 'Bflrtip',   
            pagingType  : 'simple_numbers',
            columnDefs: [
                {
                    targets: 1,
                    className: 'noVis'
                }
            ],
            searchDelay: 1500,        
            buttons   : btnList, 
            processing: true,
            responsive: true,
            serverSide: true,
            ajax : {
                url: "/api/table/getdata?guid=[[TABLE_KEY]]",
                type: "GET",
                dataSrc: function(d){
                    const hasButtons = [[HASBUTTON]];
                    window.[[TABLE_KEY]]_process_data = [];
                    window.[[TABLE_KEY]]template__data = [];
                    if(d?.data?.length > 0){

                  
                        const table = $('#[[TABLE_KEY]]').DataTable();
                        // obtenemos el total de columnas usadas en esta tabla
                        const rowLength  =  table.columns().header().length; 
                    
                        const roles = []; 
                        
                        
                        var process_data = (_jsondata,rowLength,datatable,roles,index) => {
             
                            
                            // en caso de que el index sea mayor al json se devuelve
                            if(index >= _jsondata.length){
                                return true;
                            }
                
                        
                
                            let element =  _jsondata[index];  
                
                            var data            = new Array(rowLength);
                            var tempaltelist    = new Array(rowLength);
                            
                            for(var x = 0; x < rowLength; x++ ){
                                var template = {};
                                switch(x){
                                    [[__switchFunction__]] ;
                                    default:
                                        break;
                                }

                              
                                              
                
                                data[x] = template;  
                                
                                var btntempalte = "";
                                
                                if(x == rowLength -1) {                                    
                                    if(hasButtons == true){ 
                                
                                                    
                                                    [[BTN_LIST_FNC]]    

                                                    var isbuttongrouped = [[__GROUPED__STATUS__]];
                                                    
                                                    var tempTemplate =     `<div class="text-right one-line-text" >${btntempalte}</div>`;
                                               
                                                    if(isbuttongrouped == true){
                                                        tempTemplate = 
                                                        `
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary" style="position: relative;  cursor: pointer;  white-space: nowrap; " id="" name="" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa fa-bars"></i>
                                                                <div class="hide badge-element" style="position: absolute;  right: 2px;  top: 0px;  cursor: pointer; " id="Badge">
                                                                <span class="label label-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="BadgeContent">
                                                                </span>
                                                                </div>
                                                            </button>
                                                            <ul class="dropdown-menu m-t-xs animated flipInX dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                                ${btntempalte}
                                                            </ul>
                                                        </div>
                                                        `;

                                                    }
                                                    
                                     
                                                    
                                                    tempaltelist[x] = $.trim(tempTemplate);                                                    
                                    }else{
                                        tempaltelist[x] = template.Template; 
                                    } 

                                }else{
                                    tempaltelist[x] = template.Template;    
                                }
                            }
                
                            element                 = null;
                            _jsondata[index]        = null; 
                            index++;
                            
                            
                            window.[[TABLE_KEY]]_process_data.push(data);
                            window.[[TABLE_KEY]]template__data.push(tempaltelist);
                            process_data(_jsondata,rowLength,table,roles,index);
                            
                
                        }

                        process_data(d.data,rowLength,table,roles,0); 
                        return window.[[TABLE_KEY]]template__data

                    }else{
                        return [];
                    }
                }
                
            } , 
          
            fnDrawCallback: function(settings,data) { 
              
                
                
                var table           = $('#[[TABLE_KEY]]').DataTable();
                
 
                if($('[data-bs-toggle="tooltip"]')){
               
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                }
               
                
                var myRowData = null;
                [[_BUTTON_EVENTS_TEMPLATE_]]

                [[XHRCALLBACK]]
               
            },
            createdRow: function( row, data, dataIndex, cell ) {

 
              
                var tempdata = window.[[TABLE_KEY]]_process_data[dataIndex];


                var initrowLength =  tempdata.length;
                
                var self = $(this).DataTable(); 
            

                var filtered = new Array(initrowLength);
                
             
                
                for(var index = 0; index < initrowLength; index++ ){
                    

                    let target      = tempdata[index]; 

                    if(target != null){ 
                        
                    
                        let cellTarget   = $( row ).find(`td:eq(${index})`);

                        let rowTarget    = $(row);

                        if(cellTarget.length > 0){

                        
                        
                            if(target.data != null){

                                [[TR_ATTRIBUTE_ADD]]
                            }

                            let indexLength = (target.Styless) ? target.Styless.length : 0;

                            for(var StyleIndex = 0; StyleIndex < indexLength; StyleIndex++ ){                       
                                if( target.Styless[StyleIndex][0] && target.Styless[StyleIndex][1] ){
                                    let css_property    = target.Styless[StyleIndex][0];
                                    let css_value       = target.Styless[StyleIndex][1]
                                    cellTarget.css(css_property,css_value );
                                }
                            

                            }

                            var CellProperty = target.cellProperty;
                            cellTarget.addClass(target.Class);  
                            cellTarget.data('data-property-name',CellProperty);
                            cellTarget.data('data-pk'           ,target.pk);
                            cellTarget.attr('data-property-name',CellProperty);
                            cellTarget.attr('data-pk'           ,target.pk);
                            filtered[index] = target.Template;

                            target.element  = null;
                            target.roles    = null;
                        }
                    }
                     

                } 
 
                
                
                // self.row(dataIndex).data( filtered ).draw();
                // table.settings()[0].jqXHR.abort()
             
              
               
            }
        })

        table.buttons().container()
        .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
 

        $('#[[TABLE_KEY]]_wrapper').prepend(
            `
                <div id='[[TABLE_KEY]]_row_container'>
                  
                    <div class='row' id='[[TABLE_KEY]]_filter_container'>
                        <div class='col-sm-6' id='[[TABLE_KEY]]_page_length'>
                        </div>
                        <div class='col-sm-6' id='[[TABLE_KEY]]_search_filter'>
                        </div>
                    </div>
                </div> 
            `
        )
        $('#[[TABLE_KEY]]_wrapper').append(
            `
                <div id='[[TABLE_KEY]]_footer_container'>
                  
                    <div class='row' id='[[TABLE_KEY]]_footer_page_len_actual'>
                        <div class='col-sm-6 dt-container-footer' id='[[TABLE_KEY]]_footer_page_length' style="display: inline-flex;">
                        </div>
                        <div class='col-sm-6' id='[[TABLE_KEY]]_footer_pagination'>
                        </div>
                    </div>
                </div> 
            `
        )

      

        
        /* MODIFICAMOS LA ESTRUCTURA DEL HEADER */
        $('.dt-buttons.btn-group').detach().appendTo("#[[TABLE_KEY]]_page_length");
        $('#[[TABLE_KEY]]_filter').detach().appendTo("#[[TABLE_KEY]]_search_filter");
        $('#[[TABLE_KEY]]').addClass("table-striped")
        
        /* MODIFICAMOS LA ESTRUCTURA DEL FOOTER */
        $('#[[TABLE_KEY]]_length').detach().appendTo("#[[TABLE_KEY]]_footer_page_length");
        $('#[[TABLE_KEY]]_info').detach().appendTo("#[[TABLE_KEY]]_footer_page_length");
        $('#[[TABLE_KEY]]_paginate').detach().appendTo("#[[TABLE_KEY]]_footer_pagination");


        setTimeout(() => {
            table.draw(false);   
            console.log('ok') 
        }, 2000);
        
        

        
        
        ;

        // if(this._jsondata.length > 0){
        //     process_data(this._jsondata,rowLength,table,roles,0);
        // } 
        // this._jsondata = null;

        
      
        


    }
    

    RenderLoadingState = {

        Render : () => {
            
            let container = "[[TABLE_KEY]]_wrapper"

        }

    }

    // --------------------------------------------------------------------------------------------------------
    // TEMPLATE CON LA CONFIGURACIÓN DE DE LAS CELDAS
    // --------------------------------------------------------------------------------------------------------

    [[CELL_CONFIG_TEMPLATE]] 


}
// -----------------------------------------------------------------------------
// SE INICIALIZA LA TABLA
// -----------------------------------------------------------------------------

window.[[TABLE_KEY]]_controller =  new [[TABLE_KEY]]_TemporalJSComponent_();
 
window.[[TABLE_KEY]]_controller.init();
 

// --------------------------------------------------------------------------------------------------------
// TEMPLATE CON LA CONFIGURACION DE LOS BOTONES
// --------------------------------------------------------------------------------------------------------
[[BTN_CONFIG_TEMPLATE]]

 

//# sourceURL=dinamicloaded_[[__SCRIPT__ID__]]123.js