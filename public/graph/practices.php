<?php require_once 'config.php';

$lawyers  = get_service("lawyers");

?>
<!-- Content Wrapper. Contains page content -->

      <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Emirates Advocates Lawyers</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    
<style>
    
    html, body {
        height: 100%;
    }

    .highcharts-button{
        display:none;
    }
    #period_div {
        position: absolute;
        right: 15px;
        z-index: 1;
        top: 15px;
    }
    #lawyers_div {
        position: absolute;
        right: 15px;
        z-index: 1;
        top: calc(60% + 10px);
    }
    #employee_id, #period {
        border: solid 1px #ccc;
        padding: 5px;
        color: #666;
    }
    
    .highcharts-credits{
        display: none;
    }
    
    #legend ul{
        padding-left: 20px;
    }
    #legend ul li{
        list-style: none;
        font-size: 13px;
        font-weight: bold;
    }
    #legend li{
        margin-right: 25px;
        float: left;
    }
    .fa-circle-o {
        display: block;
        width: 12px;
        height: 12px;
        float: left;
        margin-right: 10px;
        margin-top: 3px;
    }
    
    .fa.fa-circle-o.text-red{
        background-color: #9E292B;
    }
    
    .fa.fa-circle-o.text-yellow{
        background-color: #CFA04E;
    }
    
    .fa.fa-circle-o.text-grey{
        background-color: #CCC;
    }
    
</style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body style="overflow-x: hidden">
    <div id="period_div">
        <select id="period" name="period">
            <option value="weekly" <?php if(isset($_REQUEST['period']) && $_REQUEST['period']=="weekly") echo "selected"; ?>>Weekly</option>
            <option value="monthly" <?php if(isset($_REQUEST['period']) && $_REQUEST['period']=="monthly") echo "selected"; ?>>Monthly</option>
            <option value="yearly" <?php if(isset($_REQUEST['period']) && $_REQUEST['period']=="yearly") echo "selected"; /* elseif(!isset($_REQUEST['period'])) echo "selected";*/ ?>>Yearly</option>
        </select>
    </div>
    
        <div id="mybarchart" style="width: 100%;"></div>
        <div>
            <div id="legend">
                  <ul class="chart-legend clearfix">
                    <li><i class="fa fa-circle-o text-red"></i> Open </li>
                    <li><i class="fa fa-circle-o text-yellow"></i> Close </li>
                    <li><i class="fa fa-circle-o text-grey"></i> Unassign </li>
                  </ul>
            </div>
            <div style="width: 95%; height: 250px; text-align: center; margin-left: 10px;">
            <canvas id="pieChart"></canvas>
            </div>
        </div>
        
    
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->

<script src="../bootstrap/js/bootstrap.min.js"></script>

</body>
</html>


<script src="../plugins/chartjs/Chart.js"></script>
<script src="http://code.highcharts.com/stock/highstock.js"></script>

<script>
<?php
    
    if(isset($_REQUEST["period"]))
        $period = $_REQUEST["period"];
    else
        $period = "weekly";
    
    $graph_data  = get_service("lawyerCasesParacticeGraph?period=".$period);

?>


$(function () {
    
    Highcharts.setOptions({
        colors: ['#CFA04E', '#9E292B' ]
    });
    
    Highcharts.chart('mybarchart', {
        chart: {
            type: 'bar',
            marginTop: 50,
            spacingRight:0,
            paddingRight: 0
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: [
                <?php 
                if(is_array($graph_data->content->practice_cases_graph_data))
                foreach($graph_data->content->practice_cases_graph_data as $practice): ?>
                  '<?php echo $practice->practice_name_en ?>',
                <?php endforeach ?>
            ],
//            max: 8,
            tickLength: 0,
            lineColor: 'transparent',
//            scrollbar: {
//                enabled: true,
//                showFull: false
//            },
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            },
            gridLineColor: 'transparent',
            allowDecimals: false,
            endOnTick:false 
        },
        legend: {
            reversed: true,
            layout: 'horizontal',
            align: 'left',
            verticalAlign: 'top',
            floating: true,
            backgroundColor: '#FFFFFF',
            symbolRadius: 0
        },
        plotOptions: {
            series: {
                stacking: 'normal',
                pointPadding: 0,
                groupPadding: 0.1,
            },
        },
        series: [{
            name: 'Close',
            data: [
                <?php 
                if(is_array($graph_data->content->practice_cases_graph_data))
                foreach($graph_data->content->practice_cases_graph_data as $practice): ?>
                    <?php echo $practice->close ?>,
                <?php endforeach ?>
            ]
        }, {
            name: 'Open',
            data: [
                <?php 
                if(is_array($graph_data->content->practice_cases_graph_data))
                foreach($graph_data->content->practice_cases_graph_data as $practice): ?>
                    <?php echo $practice->open ?>,
                <?php endforeach ?>
            ]
        }]
    }).setSize(
       $(document).width(), 
       '<?php echo count($graph_data->content->practice_cases_graph_data)*55?>',
       false
    );
    

    var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
    var pieChart = new Chart(pieChartCanvas);

    var pieOptions = {
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke: true,
      //String - The colour of each segment stroke
      segmentStrokeColor: "#fff",
      //Number - The width of each segment stroke
      segmentStrokeWidth: 1,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 50, // This is 0 for Pie charts
      //Number - Amount of animation steps
      animationSteps: 100,
      //String - Animation easing effect
      animationEasing: "easeOutBounce",
      //Boolean - Whether we animate the rotation of the Doughnut
      animateRotate: true,
      //Boolean - Whether we animate scaling the Doughnut from the centre
      animateScale: false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive: true,
      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio: false,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
      //String - A tooltip template
      tooltipTemplate: "<%=value %> <%=label%> cases"
    };
    
    pieChart.Doughnut([
                {
                  value: '<?php echo $graph_data->content->cases_graph_data!=""?$graph_data->content->cases_graph_data->total_open:0 ?>',
                    color: "#9E292B",
                    highlight: "#9E292B",
                  label: "Open"
                },
                {
                  value: '<?php echo $graph_data->content->cases_graph_data!=""?$graph_data->content->cases_graph_data->total_close:0 ?>',
                    color: "#CFA04E",
                    highlight: "#CFA04E",
                  label: "Close"
                },
                {
                  value: '<?php echo $graph_data->content->cases_graph_data!=""?$graph_data->content->cases_graph_data->total_unassign:0 ?>',
                    color: "#CCC",
                    highlight: "#CCC",
                  label: "Unassign"
                },
              ], pieOptions);

    $('#period').on('change', function(){
        
        location.href = window.location.href.split('?')[0]+"?period="+$(this).val();
        
    })

    
});
</script>
