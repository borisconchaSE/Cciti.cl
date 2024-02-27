

                // SERIES Definition: [[KEY]]
                let [[ROOTKEY]]_series_[[KEY]] = chart_[[CHARTKEY]].series.push(
                    am5xy.[[SERIESTYPE]].new([[ROOTKEY]], {
                        name: "[[NAME]]",
                        xAxis: xAxis_[[XAXIS]],
                        yAxis: yAxis_[[YAXIS]],
                        valueYField: "[[YVALUEFIELD]]",
                        sequencedInterpolation: true,
                        categoryXField: "[[XVALUEFIELD]]",
[[OPTIONS]]
                        tooltip: am5.Tooltip.new([[ROOTKEY]], {
                            labelText:"${valueY}"
                        })
                    })
                );

[[TEMPLATE]]
            
                //[[ROOTKEY]]_series_[[KEY]].data.setAll([[ROOTKEY]]_data);