<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\lab\Lab;


$this->registerCssFile("/css/modcss/financeanalytic.css", [
], 'css-fanalytic');


$this->registerJsFile("/js/finance/highcharts.js", [
], 'js-highcharts');

$this->registerJsFile("/js/finance/highcharts-more.js", [
], 'js-highcharts-more');


?>



<div class="row">
	<div class="panel panel-default col-xs-12 col-md-2">
	 	<div class="panel-body">
		    <?php $form = ActiveForm::begin(); ?>

	        <?= $form->field($reportform, 'lab_id')->widget(Select2::classname(), [
		        'data' => ArrayHelper::map(Lab::find()->where('active =:active',[':active'=>1])->all(),'lab_id','labname'),
		        'language' => 'en',
		        'options' => ['placeholder' => 'Select Lab','readonly'=>'readonly'],
		        'pluginOptions' => [
		            'allowClear' => false
		        ]
		    ])->label('Lab'); ?>

		    <?= $form->field($reportform, 'year')->textInput([
                                 'type' => 'number'
                            ]) ?>

		    <div class="form-group">
		        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
		    </div>

			<?php ActiveForm::end(); ?>

       </div>

       <div class="alert alert-warning" style="background: #F4D6B6 !important;margin-top: 1px !important;">
        <a href="#" class="close" data-dismiss="alert" >×</a>

         <p class="note" style="color:#d73925;font-size: 12pt;"><b>Months displayed in RED color are not yet finalize</b><br/> Thank you.</p>
    </div>
   </div>
    <div class="col-xs-12 col-md-10">
       <div class="box box-solid">
       		<div id="divColumnChart" style="display: block">
                                                        <?php
                                                        echo Highcharts::widget([
                                                            'id' => 'labColumnChart',
                                                            'scripts' => [
                                                                'modules/exporting',
                                                                'themes/grid-light',
                                                            ],
                                                            'options' => [
                                                            	'chart' => [
                                                                        'type' => 'column',
                                                                    ],
                                                                'title' => [
                                                                    'text' => 'Income Generated',
                                                                ],
                                                                'xAxis' => [
                                                                    'title' => [
                                                                        'text' => 'Year'
                                                                    ],
                                                                    'categories' => ['January' , 'February', 'March', 'April', 'May','June', 'July', 'August', 'September', 'October','November', 'December'],
                                                                ],
                                                                'yAxis' => [
                                                                    'title' => [
                                                                        'text' => 'No of Firms'
                                                                    ],
                                                                    'stackLabels'=> ['enabled'=> true,]
                                                                ],
                                                                'labels' => [
                                                                    'items' => [
                                                                        [
                                                                            'style' => [
                                                                                'left' => '50px',
                                                                                'top' => '18px',
                                                                                'color' => new JsExpression('(Highcharts.theme && Highcharts.theme.textColor) || "black"'),
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                                'plotOptions'=> ['column'=>['stacking'=>'normal']],
                                                                'tooltip'=>['headerFormat'=>'<b>{point.x}</b><br/>','pointFormat'=>'{series.name}: {point.y}<br/>Total: {point.stackTotal}'],
                                                                'series' => [
													              ['name' => 'Actual Fees', 'data' => $actualfees],
													              ['name' => 'Discounts', 'data' => $discounts],
													          
													          ]
                                                            ]
                                                        ]);
                                                        ?>
       		</div>
		</div>
	</div>
	 <div class="col-md-12">
        <div class="box box-solid">
            
            <!-- /.box-header -->
            <div class="box-body">


                <div id="carousel-lab" class="carousel slide" data-ride="carousel" data-interval="false">
                    

                    <div class="carousel-inner">
                    	<div class="col-md-1 col-sm-4 col-xs-12">
                    		<a class="btn-openFigures" name="<?= $year.'-01_'.$labId?>">
                    			<div class="info-box bg-<?= $finalize[0]?>">
                    				<span class="info-box-icon" >
                    				Jan 
	                    			</span>
							        <!-- <span class="info-box-number"><a href="#" style="color:white;font-size:25px;" >12345</a></span> -->
	                    		</div>
	                    	</a>
	                    </div>

	                    <div class="col-md-1 col-sm-4 col-xs-12">
                    		<a class="btn-openFigures" name="<?= $year.'-02_'.$labId?>">
                    			<div class="info-box bg-<?= $finalize[1]?>">
	                    			<span class="info-box-icon">Feb</span>
						        	<div class="info-box-content"><span class="info-box-text"></span>
							    	</div>
	                    		</div>
	                    	</a>
	                    </div>

                    	<div class="col-md-1 col-sm-4 col-xs-12">
                    		<a class="btn-openFigures" name="<?= $year.'-03_'.$labId?>">
                    			<div class="info-box bg-<?= $finalize[2]?>">
	                    			<span class="info-box-icon">Mar</span>
						        	<div class="info-box-content"><span class="info-box-text"></span>
							    	</div>
	                    		</div>
	                    	</a>
	                    </div>
	                   
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-04_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[3]?>">
		                    		<span class="info-box-icon">Apr</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                    
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-05_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[4]?>">
		                    		<span class="info-box-icon">May</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                   
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-06_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[5]?>">
		                    		<span class="info-box-icon">Jun</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>

                    	<div class="col-md-1 col-sm-4 col-xs-12">
                    		<a class="btn-openFigures" name="<?= $year.'-07_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[6]?>">
		                    		<span class="info-box-icon">Jul</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                   
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-08_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[7]?>">
		                    		<span class="info-box-icon">Aug</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-09_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[8]?>">
		                    		<span class="info-box-icon">Sep</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                   
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-10_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[9]?>">
		                    		<span class="info-box-icon">Oct</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>

                    	<div class="col-md-1 col-sm-4 col-xs-12">
                    		<a class="btn-openFigures" name="<?= $year.'-11_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[10]?>">
		                    		<span class="info-box-icon">Nov</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                   
	                    <div class="col-md-1 col-sm-4 col-xs-12">
	                    	<a class="btn-openFigures" name="<?= $year.'-12_'.$labId?>">
	                    		<div class="info-box bg-<?= $finalize[11]?>">
		                    		<span class="info-box-icon">Dec</span>
							        <div class="info-box-content"><span class="info-box-text"></span>
								    </div>
		                    	</div>
		                    </a>
	                    </div>
	                </div>
	            </div>
	        </div>
    	</div>
	</div>
	<div class="col-md-12" id="monthlyContent">
	</div>
</div>


<script type="text/javascript">
	function OpenMonth(header,url,closebutton,width){
   
    $('#monthlyContent').html("<div style='text-align:center;'><img src='/images/img-loader64.gif' alt=''></div>");
    $('#monthlyContent').load(url);
}


jQuery(document).ready(function ($) {
    $('.btn-openFigures').click(function () {
    	// alert("haha");
        OpenMonth("Monthly Report", "/reports/finance/analytic/displaymonth?data="+this.name,true,'600px');
    });

});
</script>