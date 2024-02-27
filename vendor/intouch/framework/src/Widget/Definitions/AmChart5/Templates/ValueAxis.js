

                // Y-AXIS Definition: [[KEY]]
                let yAxis_[[KEY]] = chart_[[CHARTKEY]].yAxes.push(
                    am5xy.ValueAxis.new([[ROOTKEY]], {
                        maxDeviation: 0.3,
                        renderer: am5xy.AxisRendererY.new([[ROOTKEY]], {[[RENDEROPTIONS]]})
                    })
                );
                //yAxis_[[KEY]].data.setAll([[ROOTKEY]]_data);