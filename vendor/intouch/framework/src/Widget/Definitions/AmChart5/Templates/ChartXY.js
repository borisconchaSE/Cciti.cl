
                // CHARTXY Definition: [[KEY]]
                // ---------------------------------------
                let chart_[[KEY]] = [[ROOTKEY]].container.children.push(
                    am5xy.XYChart.new([[ROOTKEY]], {
                        panX: true,
                        panY: true,
                        pinchZoomX:true,
                        layout: [[ROOTKEY]].verticalLayout,
                        paddingTop: 0,
                        paddingBottom: 50,
                        paddingLeft: 20,
                        paddingRight: 20,
                    })
                );

                let cursor_chart_[[KEY]] = chart_[[KEY]].set(
                    "cursor", 
                    am5xy.XYCursor.new([[ROOTKEY]], {})
                );

                cursor_chart_[[KEY]].lineY.set("visible", false);

[[LEGEND]]
[[XAXIS]]
[[YAXIS]]
[[SERIES]]
[[DATACOLLECTION]]
[[DATASET]]

                // FIN CHARTXY: [[KEY]]
                // ---------------------------------------
