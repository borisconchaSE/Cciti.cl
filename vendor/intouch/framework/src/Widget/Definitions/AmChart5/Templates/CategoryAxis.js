

                // X-AXIS Definition: [[KEY]]
                let xRenderer_[[KEY]] = am5xy.AxisRendererX.new(
                    [[ROOTKEY]], 
                    { 
                        minGridDistance: 30 
                    }
                );

                xRenderer_[[KEY]].labels.template.setAll({
                    centerY: am5.p50,
                    centerX: am5.p100,
                    paddingRight: 15
                });

                let xAxis_[[KEY]] = chart_[[CHARTKEY]].xAxes.push(
                    am5xy.CategoryAxis.new([[ROOTKEY]], {
                        maxDeviation: 0.3,
                        categoryField: "[[PROPERTYNAME]]",
                        renderer: xRenderer_[[KEY]],
                        //tooltip: am5.Tooltip.new([[ROOTKEY]], {})
                    })
                );
            
                //xAxis_[[KEY]].data.setAll([[ROOTKEY]]_data);