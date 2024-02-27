<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Series;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\String\Concat;
use Intouch\Framework\Widget\Definitions\AmChart5\Basic;
use Intouch\Framework\Widget\Definitions\AmChart5\Series\SeriesTypeEnum;

#[Widget(Template: 'Series', Path: '../Templates', Extension: '.js')]
class Series extends Basic {

    public function __construct(
        string                  $Key,
        AxisDefinition          $XAxis,            
        AxisDefinition          $YAxis,
        string                  $SeriesType = SeriesTypeEnum::COLUMN_SERIES,
        ?SeriesDefinition       $Definition = null,
    ) {

        $options = '';
        $template = '';
        $fillTemplate = '';
        $strokeTemplate = '';
        $needColor = true;

        if (isset($Definition)) {

            if ($Definition instanceof ColumnSeriesDefinition) {
                if (isset($Definition->Stacked)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tstacked: " . ( $Definition->Stacked ? 'true' : 'false' ));
                }

                if (isset($Definition->LegendLabelText)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tlegendLabelText: \"" . $Definition->LegendLabelText . "\"");
                }

                if (isset($Definition->LegendRangeLabelText)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tlegendRangeLabelText: \"" . $Definition->LegendRangeLabelText. "\"");
                }
                
                // Fill Color
                if (isset($Definition->FillColor)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tfill: am5.color(\"" . $Definition->FillColor . "\")");
                    $needColor = false;
                }

                // Stroke Color
                if (isset($Definition->StrokeColor)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tstroke: am5.color(\"" . $Definition->StrokeColor . "\")");
                    $needColor = false;
                }

                // Fill Opacity
                if (isset($Definition->FillOpacity))
                    $template = Concat::AddWithCommaLine($template, "\t\t\t\t\tfillOpacity: " . $Definition->FillOpacity);

                // Stroke Width
                if (isset($Definition->StrokeWidth))
                    $template = Concat::AddWithCommaLine($template, "\t\t\t\t\tstrokeWidth: " . $Definition->StrokeWidth);

                // Corner Radius
                if (isset($Definition->TopLeftRadius))
                    $template = Concat::AddWithCommaLine($template, "\t\t\t\t\tcornerRadiusTL: " . $Definition->TopLeftRadius);
                if (isset($Definition->TopRightRadius))
                    $template = Concat::AddWithCommaLine($template, "\t\t\t\t\tcornerRadiusTR: " . $Definition->TopRightRadius);

                // Tooltip
                $template = Concat::AddWithCommaLine($template, "\t\t\t\t\ttooltipText: \"\${valueY}\"");

                if ($template == '') {

                        $template = "
                        [[ROOTKEY]]_series_" . $Key . ".columns.template.setAll({
                            cornerRadiusTL: 5, cornerRadiusTR: 5 
                        });
        
                        [[ROOTKEY]]_series_" . $Key . ".columns.template.adapters.add(
                            \"fill\", 
                            function(fill, target) {
                                return chart_[[CHARTKEY]].get(\"colors\").getIndex(
                                    [[ROOTKEY]]_series_" . $Key . ".columns.indexOf(target)
                                );
                            }
                        );
                        ";                    
                }
                else {
        
                    
                        $template = "
                        [[ROOTKEY]]_series_" . $Key . ".columns.template.setAll({
        " . $template . "
                        });
        ";
        
                        if ($needColor) {
                            $template .= "
                            [[ROOTKEY]]_series_" . $Key . ".columns.template.adapters.add(
                                \"fill\", 
                                function(fill, target) {
                                    return chart_[[CHARTKEY]].get(\"colors\").getIndex(
                                        [[ROOTKEY]]_series_" . $Key . ".columns.indexOf(target)
                                    );
                                }
                            );
                            ";
                        }

                }

            }
            else if ($Definition instanceof LineSeriesDefinition) {
                if (isset($Definition->FillColor)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tfill: am5.color(\"" . $Definition->FillColor . "\")");
                    $needColor = false;
                }

                if (isset($Definition->LegendLabelText)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tlegendLabelText: \"" . $Definition->LegendLabelText. "\"");
                }

                if (isset($Definition->LegendRangeLabelText)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tlegendRangeLabelText: \"" . $Definition->LegendRangeLabelText. "\"");
                }

                // Stroke Color
                if (isset($Definition->StrokeColor)) {
                    $options = Concat::AddWithCommaLine($options, "\t\t\t\t\t\tstroke: am5.color(\"" . $Definition->StrokeColor . "\")");
                    $needColor = false;
                }

                // Fill Opacity y Visible
                if (isset($Definition->FillOpacity))
                    $fillTemplate = Concat::AddWithCommaLine($fillTemplate, "\t\t\t\t\tfillOpacity: " . $Definition->FillOpacity);

                if (isset($Definition->Visible)) {
                    $fillTemplate = Concat::AddWithCommaLine($fillTemplate, "\t\t\t\t\tvisible: " . ( $Definition->Visible ? 'true' : 'false' ));
                }

                // Stroke Width
                if (isset($Definition->StrokeWidth))
                    $strokeTemplate = Concat::AddWithCommaLine($strokeTemplate, "\t\t\t\t\tstrokeWidth: " . $Definition->StrokeWidth);
                
                // Stroke Dash
                if (isset($Definition->StrokeDash) && is_array($Definition->StrokeDash) && count($Definition->StrokeDash) == 2) {
                    $strokeTemplate = Concat::AddWithCommaLine($strokeTemplate, "\t\t\t\t\tstrokeDasharray: [" . $Definition->StrokeDash[0] . "," . $Definition->StrokeDash[1] . "]");
                }
            }
            else {
                throw new \Exception('La definicion de series debe ser Columna o Linea');
            }

            if ($strokeTemplate != '') {
                $template .= "
                [[ROOTKEY]]_series_" . $Key . ".strokes.template.setAll({
                    $strokeTemplate
                  });
                ";
            }

            if ($fillTemplate != '') {
                $template .= "
                [[ROOTKEY]]_series_" . $Key . ".fills.template.setAll({
                    $fillTemplate
                  });
                ";
            }

        }

        if (isset($Definition->LegendLabelText)) {
            $template .= "
                legend_[[CHARTKEY]].data.push([[ROOTKEY]]_series_" . $Key .");
            ";
        }
        
        parent::__construct(
            Key     : $Key,
            Replace : [
                'KEY'           => $Key,
                'NAME'          => $Key,
                'SERIESTYPE'    => $SeriesType,
                'XAXIS'         => $XAxis->AxisKey,
                'XVALUEFIELD'   => $XAxis->PropertyName,
                'YAXIS'         => $YAxis->AxisKey,
                'YVALUEFIELD'   => $YAxis->PropertyName,
                'OPTIONS'       => (($options != "") ? $options . ',' : ''),
                'TEMPLATE'      => $template,
                'ROOTKEY'       => function() { return $this->RootKey; }, // debe ser funcion, para que evalúe el valor al momento de dibujarse y no en el constructor
                'CHARTKEY'      => function() { return $this->ChartKey; },
            ]
        );

    }

}