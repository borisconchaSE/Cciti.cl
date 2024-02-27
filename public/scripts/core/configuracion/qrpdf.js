class qrPDF {


    qrList      = new Array();
    service     = null;
    idqrList    = [];
    exportType  = 1;

    document    = null;

    Settings    = {

    }

    constructor(
        idqrList    = [],
        exportType   = 3
    ) {

        this.idqrList   = (idqrList.length > 0) ? idqrList : [];
        this.exportType = (exportType > 0) ? exportType : 1;
        this.service = new ServiceSvc();

        Swal.fire({
            icon: 'info', 
            text: 'Validando ...', 
        })
        
        this.initloading();

    }

    initloading() {
       
        const self = this;
        if( $('#qrcontainer')  ){
            $('#qrcontainer') .html('')
        }
        // llamaos al controllador
        this.service.GetBaseQr(
            {
                qrList : JSON.stringify(self.idqrList)
            },
            function(response){
                

                if( $('#qrcontainer') ){

                    $('#qrcontainer').html(response);

                    // agregamos un timeout para asegurar la correcta ejecución del codigo
                    setTimeout(() => {
                        self.initQrExport();
                    }, 500);
                 
                }
                
            },
            function(errorCode, errorMessage){
                Swal.fire({
                    icon: 'error', 
                    text: errorMessage, 
                });
            }
        );

    }

    initQrExport() {
        const self = this;  
        Swal.fire({
            icon: 'info',
            title: 'Generando PDF',
            text: 'Esto puede tardar unos segundos',   
            showCancelButton: false,  
            showConfirmButton: false 
        })      
        // buscamos el contenedor con las imagenes del QR
        if( $('#hiddenQryImage') ){    

            
            let Childrens   = $('#hiddenQryImage').children('img');
            let iterations  = Childrens.length;

            if(iterations > 0 ){
                console.log('Iniciando validacion');
                this.getBase64FromArray(Childrens, 0); 
                
            }else{
                Swal.fire({
                    icon: 'error', 
                    text: 'Debe seleccionar uno o más Destinos', 
                })
            } 
        }else{
            Swal.fire({
                icon: 'error', 
                text: 'Debe seleccionar uno o más Destinos', 
            })
        }

    }


    GenerarPDf(){

        const self = this;
        setTimeout(() => { 

            // -----------------------------------------------------------------------------------
            // INICIAMOS EL DOCUMENTO Y CREAMOS TODAS LAS CONFIGURACIONES NECESARIAS
            // -----------------------------------------------------------------------------------
            var username  = $('#UsernameContainer_').text() ? $('#UsernameContainer_').text() : "";
            
            const pageHeight    = 356   - 10;
            const pageWidth     = 216 - 10; 

            const HGrid         = pageHeight / 20;   
            const WGrid         = pageWidth  / 30; 

            var BaseImageSize   = 70;
            var copyLimit       = 1;
            var itemPerLine     = 3;
            var maxLinePerPage  = 4; 
            
            switch(this.exportType){
                case 1      :
                case "1"    :
                    BaseImageSize   = 210;
                    copyLimit       = 1;
                    itemPerLine     = 1;
                    maxLinePerPage  = 1;
                    break;
                case 2      :
                case "2"    :
                    BaseImageSize   = 100;
                    copyLimit       = 6;
                    itemPerLine     = 2;
                    maxLinePerPage  = 3;
                    break;
                case 3      :
                case "3"    :
                    copyLimit       = 12;
                    itemPerLine     = 3;
                    break;
            }

            self.Settings = {
                username    : username,
                page        : {
                    width       : pageWidth,
                    height      : pageHeight
                },
                grid        : {
                    steps       : 30,
                    height      : HGrid,
                    width       : WGrid
                },
                text        : {
                    FontSize    : 13,
                    TextColor   : 40,
                    FontStyle   : 'normal'
                },
                img : {
                    height  : BaseImageSize,
                    width   : BaseImageSize,
                },
                items : {
                    itemPerLine     : itemPerLine,
                    copyLimit       : copyLimit,
                    maxLinePerPage : maxLinePerPage
                }
            } ;

       


            self.document = new jsPDF('P', "mm", 'legal',true);

            self.GenerarBody();

        }, 1000);
      
    }


    GenerarBody() {

      

        const self = this;

        const settings = self.Settings;

        var iterations = self.qrList.length;

        if(iterations > 0){ 
          
            var counter         = 0;
            var lineCounter     = 0;
            var Xindex          = 1;
            var YIndex          = 1;
       
            var pageIndex       = 0; 
            var copyCounter     = 0;
           
            self.NoFreezeAsyncEach(self.document,(self.qrList.length - 1),0,counter,lineCounter,Xindex,YIndex,pageIndex,copyCounter );
        
        


        } 
    }


    NoFreezeAsyncEach(document, total, index,counter,lineCounter,Xindex,YIndex,pageIndex,copyCounter ) {

        console.log(` index : ${index} | total : ${total} `);
        const self      = this;
        const settings  = this.Settings;

        // if(copyCounter >= self.Settings.items.copyLimit){
        //     index++; 
        // }
    
        if(index > total){ 
            Swal.fire({
              icon:   'success',    
              text:   'Archivo Generado',    
            })
            document.save('qrcodes.pdf');
            return;
        }


        // if(copyCounter >= self.Settings.items.copyLimit){   
        //     Xindex      = 1;
        //     YIndex      = 1;
        //     lineCounter = 0;
        //     counter     = 0;
        //     copyCounter = 0;
        //     document.addPage();  
        // }
          
                
        const itemPerLine           = this.Settings.items.itemPerLine;
        const maxLinePerPage        = this.Settings.items.maxLinePerPage;
        const YJump                 = Math.round(settings.img.height / settings.grid.height); 
        const XJump                 = Math.round(settings.img.width / settings.grid.width); 
        
        

        if(counter >= itemPerLine){

            lineCounter++;

            counter = 0;

            Xindex = 1;

            YIndex = YIndex + YJump + 1; 

        }
        counter++;
        
        console.log(lineCounter);
        console.log(maxLinePerPage);
    
        if(lineCounter >= maxLinePerPage){
            Xindex      = 1;
            YIndex      = 1;
            lineCounter = 0;
            counter     = 0;
            console.log('NUEVA PAGINA AGREGADA');
            document.addPage();  
            pageIndex = pageIndex + 1; 
            self.NoFreezeAsyncEach(document,total, index,counter,lineCounter,Xindex,YIndex,pageIndex,copyCounter );    
            return;
        }
        
   
        
       

        // calculamos el salto que deberia pegar     
                 
        let target = self.qrList[index];

        let Xposition = settings.grid.width * Xindex -2;
        let Yposition = settings.grid.height * YIndex - 20;

        document.addImage(
            target,                             // <--- Img Link o Base64
            'JPEG',                             // <--- Formato (al comprimir)
            Xposition,                          // <--- Posición X            
            Yposition,                          // <--- Posicion Y
            settings.img.height, settings.img.width                              // <--- Tamaño
        );
        console.log('IMAGEN AGREGADA');

        Xindex = Xindex + XJump;

        

            
        
        // copyCounter++;
        index++; 
        
       if(self.exportType == 1 || self.exportType == "1" ){   
            Xindex      = 1;
            YIndex      = 1;
            lineCounter = 0;
            counter     = 0;
            copyCounter = 0;
            document.addPage();  
        }
        
        self.NoFreezeAsyncEach(document,total, index,counter,lineCounter,Xindex,YIndex,pageIndex,copyCounter );    
        
        
    }
 


    


    async getBase64FromArray(object, index) {

        // en caso de que el bucle sea finalizado terminamos el proceso
        if(object.length <= index){ 
            console.log('es mayor!')
            this.GenerarPDf();

            return true;
        }
        console.log(`Validando index ${index} de ${object.length -1 }`);
        // separamos el target que vamos a utilizar
        var target  = object[index];

        // traemos el src de la imagen
        var src     = target.currentSrc;

        // validamos si corresponde a un uri o un base64
        var status = await this.checkBase64img(src);
    
        
        if(status){
            // en caso de ser verdadero generamos el push con la imagen a usar
            this.qrList.push(src);
        }else{
            // en caso de no ser una imagen base64 generamos una busqueda por uri
            await getBase64FromUrl(src).then(
                function(imgBase64){
                    self.qrList.push(imgBase64);
                    resolve(true);
                }
            );
        }
        index++;

        this.getBase64FromArray(object,index)
        
  
    }

    async checkBase64img(base64String) {
        let image = new Image()
        image.src = base64String
        return await (new Promise((resolve)=>{
          image.onload = function () {
            if (image.height === 0 || image.width === 0) {
              resolve(false);
              return;
            }
            resolve(true)
          }
          image.onerror = () =>{
            resolve(false)
          }
        }))
    }

}
 