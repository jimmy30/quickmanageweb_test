<?php require_once 'config.php'; ?>
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
  <link rel="stylesheet" href="../plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    
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
            <option value="yearly" <?php if(isset($_REQUEST['period']) && $_REQUEST['period']=="yearly") echo "selected"; /* elseif(!isset($_REQUEST['period'])) echo "selected"; */?>>Yearly</option>
        </select>
    </div>
    
    <div class="chart" style="height: 100%">
        <div id="world-map-markers" style="height: 60%;"></div>
        <div id="mylinechart" style="height: 40%; width: 99%;"></div>
    </div>
    
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="http://code.highcharts.com/stock/highstock.js"></script>

</body>
</html>


<script>
<?php
    
    if(isset($_REQUEST["period"]))
        $period = $_REQUEST["period"];
    else
        $period = "weekly";
    
    
    $graph_country_lat_long = array();
    
    $country_graph_data  = get_service("customerCountryGraph?period=".$period);
    
    if(is_array($country_graph_data->content->country_graph_data))
    foreach($country_graph_data->content->country_graph_data as $country)
    {
        $file = fopen("../countries.csv","r");
        while(! feof($file))
        {
           $row = fgetcsv($file);

           if(strtolower($country->region_name_en) == strtolower($row[3]))
           {
               //echo $nationality_county." == ".$row[3]."<br>";
               $country->lat = $row[1];
               $country->long = $row[2];
           }
        }
        fclose($file);

        $graph_country_lat_long[] = $country;
    }
    
    $graph_data  = get_service("lawyerCasesGraph?period=".$period);

?>
    
$(function () {
    
    Highcharts.setOptions({
        colors: ['#9E292B', '#CFA04E']
    });
    
    <?php if(is_array($graph_data->content[0]->cases_graph_data)):?>
            
      $('#world-map-markers').vectorMap({
        map: 'world_mill_en',
        normalizeFunction: 'polynomial',
        hoverOpacity: 0.7,
        hoverColor: false,
        backgroundColor: 'transparent',
        regionStyle: {
          initial: {
            fill: 'rgba(210, 214, 222, 1)',
            "fill-opacity": 1,
            stroke: 'none',
            "stroke-width": 0,
            "stroke-opacity": 1
          },
          hover: {
            "fill-opacity": 0.7,
            cursor: 'pointer'
          },
          selected: {
            fill: 'yellow'
          },
          selectedHover: {}
        },
        markerStyle: {
          initial: {
            fill: '#92c341',
            stroke: '#111'
          }
        },
        markers: [
        <?php foreach($graph_country_lat_long as $nationality): ?>
           {latLng: [<?php echo $nationality->lat ?>, <?php echo $nationality->long ?>], name: '<?php echo $nationality->region_name_en."(".$nationality->user_count.")" ?>' },
        <?php endforeach ?>

    ]
      });

    <?php endif?>
    
    Highcharts.chart('mylinechart', {
        chart: {
            marginTop: 50,
        },
        title: {
            text: null,
            x: -20 //center
        },
        xAxis: {
            categories: [
                <?php 
                if(is_array($graph_data->content[0]->response_time_graph_data))
                foreach($graph_data->content[0]->response_time_graph_data as $lawyer_response_time): ?>
                  '<?php echo $lawyer_response_time->first_name_en ?>',
                <?php endforeach ?>
            ],
            allowDecimals: false,
            endOnTick:false 

        },
        yAxis: {
            title: {
                text: 'Response Time'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'Hours',
            data: [
                <?php 
                if(is_array($graph_data->content[0]->response_time_graph_data))
                foreach($graph_data->content[0]->response_time_graph_data as $lawyer_response_time): ?>
                  <?php echo $lawyer_response_time->response_time_avg ?>,
                <?php endforeach ?>
            ],
             marker: {
                    enabled: false
                }
        }]
    });
    
    $('#period').on('change', function(){
        
        location.href = window.location.href.split('?')[0]+"?period="+$(this).val();
        
    })

    $('#employee_id').on('change', function(){
        
        location.href = window.location.href.split('?')[0]+"?employee_id="+$(this).val()+'&period=<?php echo $period?>';
        
    })

    
    
});
</script>
