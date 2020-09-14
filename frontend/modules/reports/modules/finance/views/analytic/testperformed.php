<script type="text/javascript">

	Highcharts.chart('tests', {
    chart: {
        type: 'packedbubble',
        height: '100%'
    },
    title: {
        text: 'Tests Performed - Still on Development on this part'
    },
    tooltip: {
        useHTML: true,
        pointFormat: '<b>{point.name}:</b> {point.value}'
    },
    plotOptions: {
        packedbubble: {
            minSize: '20%',
            maxSize: '100%',
            zMin: 0,
            zMax: 1000,
            layoutAlgorithm: {
                gravitationalConstant: 0.05,
                splitSeries: true,
                seriesInteraction: false,
                dragBetweenSeries: true,
                parentNodeLimit: true
            },
            dataLabels: {
                enabled: true,
                format: '{point.name}',
                filter: {
                    property: 'y',
                    operator: '>',
                    value: 10
                },
                style: {
                    color: 'black',
                    textOutline: 'none',
                    fontWeight: 'normal'
                }
            }
        }
    },
    series: [{
        name: 'Water',
        data: [
        {
            name: "Alkalinity/PH",
            value: 40
        },
        {
            name: "Water Activity",
            value: 40
        },
        {
            name: "PH",
            value: 34
        },
        {
            name: "Trubidity",
            value: 10
        },
        {
            name: "Cyprus",
            value: 7
        }]
    },{
        name: 'Canned Fish',
        data: [
        {
            name: "Crude Fiber",
            value: 40
        },
        {
            name: "Protein",
            value: 30
        }]
    }]
});



</script>