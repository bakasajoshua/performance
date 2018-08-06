<div id="{{$div}}"></div>

<script type="text/javascript">
	
    $(function () {
        $('#{{$div}}').highcharts({
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: ''
            },
            xAxis: [{
                categories: {!! json_encode($categories) !!}
            }],
            
            yAxis: { // Primary yAxis
                labels: {
                    formatter: function() {
                        return this.value;
                    },
                },
                title: {
                    text: '',
                    style: {
                        color: '#89A54E'
                    }
                },
    
            },
            tooltip: {
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#999',
                shadow: false,
                shared: true,
                useHTML: true,
                yDecimals: 0,
                valueDecimale: 0,
                headerFormat: '<table class="tip"><caption>{point.key}</caption>'+'<tbody>',
                pointFormat: '<tr><th style="color:{series.color}">{series.name}:</th>'+'<td style="text-align:right">{point.y}</td></tr>',
                footerFormat: '<tr><th>Total:</th>'+'<td style="text-align:right"><b>{point.total}</b></td></tr>'+'</tbody></table>'
            },
            legend: {
                layout: 'horizontal',
                align: 'right',
                x: -100,
                verticalAlign: 'bottom',
                y: -25,
                floating: false,
                backgroundColor: '#FFFFFF'
            },
            navigation: {
                buttonOptions: {
                    verticalAlign: 'bottom',
                    y: -20
                }
            },
            colors: [
                '#F2784B',
                '#1BA39C',
                '#913D88'
            ],     
            series: {!! json_encode($outcomes) !!}
        });
    });
</script>