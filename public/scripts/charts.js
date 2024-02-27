$(function () {

    /**
     * Pie charts data and options used in many views
     */

    $("span.pie").peity("pie", {
        fill: ["#62cb31", "#edf0f5"]
    });

    $(".line").peity("line",{
        fill: '#62cb31',
        stroke:'#edf0f5'
    });

    $(".bar").peity("bar", {
        fill: ["#62cb31", "#edf0f5"]
    });

    $(".bar_dashboard").peity("bar", {
        fill: ["#62cb31", "#edf0f5"]
    });

});

var shutdownPieTheme = {
    palette: {
      line: [
        ['#FBFCFE', '#00BAF2', '#00BAF2', '#00a7d9'], /* light blue */
        ['#FBFCFE', '#E80C60', '#E80C60', '#d00a56'], /* light pink */
        ['#FBFCFE', '#9B26AF', '#9B26AF', '#8b229d'], /* light purple */
        ['#FBFCFE', '#E2D51A', '#E2D51A', '#E2D51A'], /* med yellow */
        ['#FBFCFE', '#FB301E', '#FB301E', '#e12b1b'], /* med red */
        ['#FBFCFE', '#00AE4D', '#00AE4D', '#00AE4D'], /* med green */
      ]
    },
    graph: {
      backgroundColor: '#FFFFFF',
      title: {
        fontFamily: 'Open Sans',
        fontSize: 14,
        fontWeight: 400,
        // border: "1px solid black",
        padding: "15px",
        fontColor: "#1E5D9E",
        adjustLayout: true
      },
      subtitle: {
        fontFamily: 'Open Sans',
        fontSize: 12,
        fontWeight: 400,
        fontColor: "#777",
        padding: "5"
      },
      legend: {
        layout: '1x3',
        item: {
          fontFamily: "Open Sans",
          fontColor: '#373a3c',
          fontSize: '11px',
          fontWeight: 400
        },
        toggleAction: 'remove',
        align: 'center',
        verticalAlign: 'top',
        margin: '5px 20px 0px 25px',
        padding: '5px',
        borderWidth: 0
      },
      plot: {
        backgroundColor: '#FBFCFE',
        marker: {
          size: 4
        },        
        detach: false,
        borderWidth: 1,
        borderColor: '#FFF',
        hoverState: {
          visible: false
        },    
        valueBox: {
            placement: 'out',
            text: '%npv%',
            fontFamily: "Open Sans",
            fontSize: 11
        },
      },
      tooltip: {
        visible: true,
        text: "%kl<br><span style='color:%color'>%t: </span>%v<br>",
        backgroundColor: "white",
        borderColor: "#e3e3e3",
        borderRadius: "5px",
        bold: true,
        fontSize: "12px",
        fontColor: "#2f2f2f",
        textAlign: 'left',
        padding: '15px',
        shadow: true,
        shadowAlpha: 0.2,
        shadowBlur: 5,
        shadowDistance: 4,
        shadowColor: "#a1a1a1"
      },
      plotarea: {
        backgroundColor: 'transparent',
        borderRadius: "0 0 0 10",
        borderRight: "0px",
        borderTop: "0px",
        margin: "dynamic",
        /*borderLeft: '1px solid #1E5D9E',
        borderBottom: '1px solid #1E5D9E',*/
      },
      scaleX: {
        zooming: true,
        offsetY: -20,
        lineWidth: 0,
        padding: 20,
        margin: 20,
        label: {
          text: "Scale-X"
        },
        item: {
          padding: 5,
          fontColor: "#1E5D9E",
          fontFamily: 'Open Sans',
          rules: [{
            rule: "%i == 0",
            visible: false
          }]
        },
        tick: {
          lineColor: '#D1D3D4',
          rules: [{
            rule: "%i == 0",
            visible: false
          }]
        },
        guide: {
          visible: true,
          lineColor: '#D7D8D9',
          lineStyle: 'dotted',
          lineGapSize: '4px',
          rules: [{
            rule: "%i == 0",
            visible: false
          }]
        }
      },
      scaleY: {
        zooming: true,
        lineWidth: 0,
        label: {
          text: "Scale-Y"
        },
        item: {
          padding: "0 10 0 0",
          fontColor: "#1E5D9E",
          fontFamily: 'Open Sans',
          rules: [{
            rule: "%i == 0",
            offsetX: 10,
            offsetY: 10,
            backgroundColor: 'none'
          }]
        },
        tick: {
          lineColor: '#D1D3D4',
          rules: [{
            rule: "%i == 0",
            visible: false
          }]
        },
        guide: {
          visible: true,
          lineColor: '#D7D8D9',
          lineStyle: 'dotted',
          lineGapSize: '4px',
          rules: [{
            rule: "%i == 0",
            visible: false
          }]
        }
      },
      scrollX: {
        bar: {
          backgroundColor: 'none',
          borderLeft: 'none',
          borderTop: 'none',
          borderBottom: 'none'
        },
        handle: {
          backgroundColor: '#1E5D9E',
          height: 5
        }
      },
      scrollY: {
        borderWidth: 0,
        bar: {
          backgroundColor: 'none',
          width: 14,
          borderLeft: 'none',
          borderTop: 'none',
          borderBottom: 'none'
        },
        handle: {
          borderWidth: 0,
          backgroundColor: '#1E5D9E',
          width: 5
        }
      },
      zoom: {
        backgroundColor: '#1E5D9E',
        alpha: .33,
        borderColor: '#000',
        borderWidth: 1
      },
      preview: {
        borderColor: '#1E5D9E',
        borderWidth: 1,
        adjustLayout: true,
        handle: {
          backgroundColor: '#1E5D9E',
          borderColor: '#1E5D9E'
        },
        mask: {
          backgroundColor: '#FFF',
          alpha: .95,
        }
      }
    }
  };

  var shutdownPieThemeSmall = {
      palette: {
        line: [
          ['#FBFCFE', '#00BAF2', '#00BAF2', '#00a7d9'], /* light blue */
          ['#FBFCFE', '#E80C60', '#E80C60', '#d00a56'], /* light pink */
          ['#FBFCFE', '#9B26AF', '#9B26AF', '#8b229d'], /* light purple */
          ['#FBFCFE', '#E2D51A', '#E2D51A', '#E2D51A'], /* med yellow */
          ['#FBFCFE', '#FB301E', '#FB301E', '#e12b1b'], /* med red */
          ['#FBFCFE', '#00AE4D', '#00AE4D', '#00AE4D'], /* med green */
        ]
      },
      graph: {
        backgroundColor: '#FFFFFF',
        title: {
          fontFamily: 'Open Sans',
          fontSize: 14,
          fontWeight: 400,
          // border: "1px solid black",
          padding: "15px",
          fontColor: "#1E5D9E",
          adjustLayout: true
        },
        subtitle: {
          fontFamily: 'Open Sans',
          fontSize: 12,
          fontWeight: 400,
          fontColor: "#777",
          padding: "5"
        },
        legend: {
          layout: '1x1',
          item: {
            fontFamily: "Open Sans",
            fontColor: '#373a3c',
            fontSize: '10px',
            fontWeight: 400
          },
          toggleAction: 'remove',
          align: 'left',
          verticalAlign: 'top',
          margin: '5px 40px 20px 25px',
          padding: '5px',
          borderWidth: 0
        },
        plot: {
          backgroundColor: '#FBFCFE',
          marker: {
            size: 4
          },        
          detach: false,
          borderWidth: 1,
          borderColor: '#FFF',
          hoverState: {
            visible: false
          },    
          valueBox: {
              placement: 'out',
              text: '%npv%',
              fontFamily: "Open Sans",
              fontSize: 11
          },
        },
        tooltip: {
          visible: true,
          text: "%kl<br><span style='color:%color'>%t: </span>%v<br>",
          backgroundColor: "white",
          borderColor: "#e3e3e3",
          borderRadius: "5px",
          bold: true,
          fontSize: "12px",
          fontColor: "#2f2f2f",
          textAlign: 'left',
          padding: '15px',
          shadow: true,
          shadowAlpha: 0.2,
          shadowBlur: 5,
          shadowDistance: 4,
          shadowColor: "#a1a1a1"
        },
        plotarea: {
          backgroundColor: 'transparent',
          borderRadius: "0 0 0 10",
          borderRight: "0px",
          borderTop: "0px",
          margin: "dynamic",
          /*borderLeft: '1px solid #1E5D9E',
          borderBottom: '1px solid #1E5D9E',*/
        },
        scaleX: {
          zooming: true,
          offsetY: -20,
          lineWidth: 0,
          padding: 20,
          margin: 20,
          label: {
            text: "Scale-X"
          },
          item: {
            padding: 5,
            fontColor: "#1E5D9E",
            fontFamily: 'Open Sans',
            rules: [{
              rule: "%i == 0",
              visible: false
            }]
          },
          tick: {
            lineColor: '#D1D3D4',
            rules: [{
              rule: "%i == 0",
              visible: false
            }]
          },
          guide: {
            visible: true,
            lineColor: '#D7D8D9',
            lineStyle: 'dotted',
            lineGapSize: '4px',
            rules: [{
              rule: "%i == 0",
              visible: false
            }]
          }
        },
        scaleY: {
          zooming: true,
          lineWidth: 0,
          label: {
            text: "Scale-Y"
          },
          item: {
            padding: "0 10 0 0",
            fontColor: "#1E5D9E",
            fontFamily: 'Open Sans',
            rules: [{
              rule: "%i == 0",
              offsetX: 10,
              offsetY: 10,
              backgroundColor: 'none'
            }]
          },
          tick: {
            lineColor: '#D1D3D4',
            rules: [{
              rule: "%i == 0",
              visible: false
            }]
          },
          guide: {
            visible: true,
            lineColor: '#D7D8D9',
            lineStyle: 'dotted',
            lineGapSize: '4px',
            rules: [{
              rule: "%i == 0",
              visible: false
            }]
          }
        },
        scrollX: {
          bar: {
            backgroundColor: 'none',
            borderLeft: 'none',
            borderTop: 'none',
            borderBottom: 'none'
          },
          handle: {
            backgroundColor: '#1E5D9E',
            height: 5
          }
        },
        scrollY: {
          borderWidth: 0,
          bar: {
            backgroundColor: 'none',
            width: 14,
            borderLeft: 'none',
            borderTop: 'none',
            borderBottom: 'none'
          },
          handle: {
            borderWidth: 0,
            backgroundColor: '#1E5D9E',
            width: 5
          }
        },
        zoom: {
          backgroundColor: '#1E5D9E',
          alpha: .33,
          borderColor: '#000',
          borderWidth: 1
        },
        preview: {
          borderColor: '#1E5D9E',
          borderWidth: 1,
          adjustLayout: true,
          handle: {
            backgroundColor: '#1E5D9E',
            borderColor: '#1E5D9E'
          },
          mask: {
            backgroundColor: '#FFF',
            alpha: .95,
          }
        }
      }
    };
