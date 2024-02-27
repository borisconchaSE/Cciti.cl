            // INICIO DEL GRAFICO
            // -----------------------------------------------------------------------
            am5.ready(function() {

               
                // Creacion del elemento principal del grafico
                // ------------------------------------------
                let [[ROOTKEY]] = am5.Root.new(
[[ROOTCONFIGURATION]]
                );

                // Settings
                // ------------------------------------------
                [[ROOTKEY]].setThemes([
                    am5themes_Animated.new([[ROOTKEY]])
                ]);

[[SETTINGS]]

                // Charts
                // ------------------------------------------
[[CHARTS]]

                // setTimeout( 
                //     () => {
                //         $('#[[DIVCONTAINER]]').find('[role="tooltip"]').parent().children(':last').remove();
                //         //$('#[[DIVCONTAINER]]').find('canvas').first().siblings(':last').hide();
                //     }, 
                //     50
                // );          

            });
            // FIN DEL GRAFICO
            // -----------------------------------------------------------------------