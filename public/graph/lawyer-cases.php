<?php require_once 'config.php';

$lawyers  = get_service("lawyers");


array_shift($lawyers->content);


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
    
    #lawyers_div{
        text-align: right;
        margin-right: 10px;
        margin-bottom: -26px;
        z-index: 15;
        position: relative;
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
            <option value="yearly" <?php if(isset($_REQUEST['period']) && $_REQUEST['period']=="yearly") echo "selected"; /* elseif(!isset($_REQUEST['period'])) echo "selected";*/ ?>>Yearly</option>
        </select>
    </div>
    
    <div id="mybarchart" style="width: 100%;"></div>
    
    <div id="lawyers_div">
        <select id="employee_id" name="employee_id">
            <option value="" >All Lawyers</option>
            <?php foreach($lawyers->content as $lawyer):?>
            <option value="<?php echo $lawyer->id?>" <?php if(isset($_REQUEST['employee_id']) && $_REQUEST['employee_id']==$lawyer->id) echo "selected"; ?>><?php echo ucwords($lawyer->first_name_en." ".$lawyer->last_name_en)?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div id="mylinechart" style="width: 99%;"></div>
    
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->

<script src="../bootstrap/js/bootstrap.min.js"></script>

</body>
</html>


<script src="http://code.highcharts.com/stock/highstock.js"></script>

<script>
<?php
    
    if(isset($_REQUEST["period"]))
        $period = $_REQUEST["period"];
    else
        $period = "weekly";
    
    $graph_data  = get_service("lawyerCasesGraph?period=".$period);
    
    if(isset($_REQUEST["employee_id"]))
        $employee_id = $_REQUEST["employee_id"];
    else
        $employee_id = "";
    
    if($employee_id!="")
    {
        $respnse_graph_data = get_service("lawyerResponseTimeGraph?employee_id=".$employee_id.'&period='.$period);
        $graph_data->content[0]->response_time_graph_data = $respnse_graph_data->content->response_time_graph_data;
    }

?>
    
$(function () {
    
    Highcharts.setOptions({
        colors: ['#9E292B', '#CFA04E']
    });
    
    <?php if(is_array($graph_data->content[0]->cases_graph_data)):?>
            
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
                <?php foreach($graph_data->content[0]->cases_graph_data as $lawyer_cases): ?>
                  '<?php echo $lawyer_cases->first_name_en ?>',
                <?php endforeach ?>
            ],
//            max: 7,
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
            name: 'Open',
            data: [
                <?php foreach($graph_data->content[0]->cases_graph_data as $lawyer_cases): ?>
                    <?php echo $lawyer_cases->open ?>,
                <?php endforeach ?>
            ]
        }, {
            name: 'Close',
            data: [
                <?php foreach($graph_data->content[0]->cases_graph_data as $lawyer_cases): ?>
                    <?php echo $lawyer_cases->close ?>,
                <?php endforeach ?>
            ]
        }]
    }).setSize(
       $(document).width(), 
       '<?php echo count($graph_data->content[0]->cases_graph_data)*55?>',
       false
    );
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
                  '<?php echo $employee_id?$lawyer_response_time->ticket_no:$lawyer_response_time->first_name_en ?>',
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
                  <?php echo $employee_id?$lawyer_response_time->response_time:$lawyer_response_time->response_time_avg ?>,
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
