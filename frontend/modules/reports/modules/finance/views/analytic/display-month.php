<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */

?>





<div class="row">
    <div class="col-md-4">
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
                <?php
                foreach ($factors as $factor) {
                    echo '<div class="info-box">';
                    if($factor->factor->type)
                        echo '<span class="info-box-icon box-action-content bg-green"><i class="fa fa-thumbs-up"></i></span>';
                    else
                        echo '<span class="info-box-icon box-action-content bg-red"><i class="fa fa-thumbs-down"></i></span>';

                    echo Html::a('x',['/reports/finance/analytic/removefactor?factor_id='.$factor->accompfactor_id,],
                        [
                            'data-confirm' => "Are you sure you want to delete this factor for '".$factor->name."'?",
                            'class'=>'btn btn-small pull-right',
                            // 'style'=>'display:block'
                        ]
                            );
                    echo '<div class="info-box-content">';
                    echo '<span class="info-box-text">'.($factor->factor?$factor->factor->title:"Error Null").'</span>';
                    echo '<span class="info-box-number">'.$factor->name.'</span>';
                    echo '<span class="info-box-text">'.$factor->remarks.'</span>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
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

	<div class="col-md-8">
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