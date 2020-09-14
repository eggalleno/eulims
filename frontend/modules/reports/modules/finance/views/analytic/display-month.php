<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;
/* @var $this yii\web\View */

?>





<div class="row">
    <div class="col-md-3">
        <div class="box box-solid">
            <div class="box-header with-border" style="background-color:#3c8dbc !important;color:white">
                <h3 class="box-title">Factors</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">


                <div id="carousel-lab" class="carousel slide" data-ride="carousel" data-interval="false">
                    
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                            <div class="info-box-content"><span class="info-box-text">Month</span>
                                <span class="info-box-number"><a href="#" style="color:white"><?= date('F Y',strtotime($yearmonth)) ?></a></span>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-inner">
                    	<div class="col-md-12 col-sm-12 col-xs-12">
                    		<div class="info-box bg-blue">
	                    		<span class="info-box-icon"><i class="fa fa-money"></i></span>
						        <div class="info-box-content"><span class="info-box-text">Samples  Handled</span>
							        <span class="info-box-number"><a href="#" style="color:white;font-size:25px;" ><div id="samplecount" class="delay" data-url="/reports/finance/analytic/getsamples?yearmonth=<?= $yearmonth?>&lab_id=<?= $lab_id?>"><div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div></div></a></span>
							    </div>
	                    	</div>
	                    </div>

                    	<div class="col-md-12 col-sm-12 col-xs-12">
                    		<div class="info-box bg-green">
	                    		<span class="info-box-icon"><i class="fa fa-plus"></i></span>
						        <div class="info-box-content"><span class="info-box-text">Partnership  </span><p style="background-color:#ffe2d8   !important;color:red">Forecasting Ongoing Development</p>
							        <span class="info-box-number"><a href="#" style="color:white;font-size:25px;" >DOH</a></span>
							    </div>
	                    	</div>
	                    </div>
	                   
	                    <div class="col-md-12 col-sm-12 col-xs-12">
                    		<div class="info-box bg-red">
	                    		<span class="info-box-icon"><i class="fa fa-minus"></i></span>
						        <div class="info-box-content"><span class="info-box-text">Pandemic</span><p style="background-color:#ffe2d8   !important;color:red">Forecasting Ongoing Development</p>
							        <span class="info-box-number"><a href="#" style="color:white">Limited Services</a></span>
							    </div>
	                    	</div>
	                    </div>
	                    
	                    
	                </div>
	            </div>
	        </div>
    	</div>
	</div>

	<div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border" style="background-color:#00a65a !important;color:white">
                <div id="tests" class="delay" data-url="/reports/finance/analytic/gettestsperformed?yearmonth=<?= $yearmonth?>&lab_id=<?= $lab_id?>"><div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div></div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            	
	        </div>
    	</div>
	</div>

	<div class="col-md-3">
        <div class="box box-solid">
            <div class="box-header with-border" style="background-color:#ffa500 !important;color:white">
                
                <div id="customercount" class="delay" data-url="/reports/finance/analytic/getcustomers?yearmonth=<?= $yearmonth?>&lab_id=<?= $lab_id?>"><div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div></div>
            </div>
            <!-- /.box-header -->

    	</div>
	</div>


</div>



<script type="text/javascript">


jQuery(document).ready(function ($) {
    
    var element_sample = document.getElementById("samplecount");
    var url_sample = element_sample.getAttribute("data-url");
    $('#samplecount').load(url_sample);

    var element_customer = document.getElementById("customercount");
    var url_customer = element_customer.getAttribute("data-url");
    $('#customercount').load(  url_customer);

    var element_tests = document.getElementById("tests");
    var url_tests = element_tests.getAttribute("data-url");
    $('#tests').load(url_tests);

});
</script>