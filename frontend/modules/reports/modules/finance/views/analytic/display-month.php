<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;
/* @var $this yii\web\View */

?>





<div class="row">
    <div class="col-md-3">
        <div class="col-md-12">
                            
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-entities"><i class="fa fa-calendar"></i></span>
                    <div class="info-box-content bg-entities">
                        <span class="info-box-number"><?= date('F Y',strtotime($yearmonth)) ?></span>
                    </div>
                    <div class="info-box-content"><span class="info-box-text">Samples  Handled</span>
                        <span class="info-box-number"><div id="samplecount" class="delay" data-url="/reports/finance/analytic/getsamples?yearmonth=<?= $yearmonth?>&lab_id=<?= $lab_id?>"><div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div></div></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon box-action-content bg-green bg-hover" id="btn_addfactor" data-url="<?= $yearmonth?>"><i class="fa fa-plus"></i></span>
                    <div class="info-box-content"><span class="info-box-text">Link A</span>
                        <span class="info-box-number">Factor</span>
                    </div>
                </div>
            </div>

        	<div class="col-md-12 col-sm-12 col-xs-12" id="factors">
                <!-- <div class="info-box bg-red">
                    <span class="info-box-icon box-action"><i class="fa fa-minus"></i></span>
                    <div class="info-box-content"><span class="info-box-text">Pandemic</span>
                        <span class="info-box-number"><a href="#" style="color:white">Limited Services</a></span>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border bg-panel">
                    
                    <div id="customercount" class="delay" data-url="/reports/finance/analytic/getcustomers?yearmonth=<?= $yearmonth?>&lab_id=<?= $lab_id?>"><div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div></div>
                </div>
                <!-- /.box-header -->

            </div>
        </div>
    </div>

	<div class="col-md-9">
        <div class="box box-solid">
            <div class="box-header with-border bg-bigpanel">
                <div id="tests" class="delay" data-url="/reports/finance/analytic/gettestsperformed?yearmonth=<?= $yearmonth?>&lab_id=<?= $lab_id?>"><div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div></div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            	
	        </div>
    	</div>
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

    

    jQuery(document).ready(function ($) {
        $('#btn_addfactor').click(function () {
            LoadModal("Choose a Factor","/reports/finance/analytic/addfactors?yearmonth="+(this).getAttribute("data-url"));
        });
    });


});
</script>