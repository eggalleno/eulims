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
	<div class="col-xs-12 col-md-2">
		<div class="box-header with-border bg-bigpanel">
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
   	</div>
    <div class="col-xs-12 col-md-10">
    	<div class="box-header with-border bg-graphs">
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
                        'text' => 'Income Generated - '.$labtitle ,
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
                        ['type' => 'column','name' => 'Actual Fees', 'data' => $actualfees],
                        ['type' => 'column','name' => 'Discounts', 'data' => $discounts],
                        [
                            'type' => 'spline',
                            'name' => 'Income Trend',
                            'data' => $income,
                            'marker' => [
                                'lineWidth' => 10,
                                'lineColor' => new JsExpression('Highcharts.getOptions().colors[3]'),
                                'fillColor' => 'green',
                            ],
                        ],
                        [
                            'type' => 'spline',
                            'name' => 'Prediction',
                            'data' => $prediction,
                            'marker' => [
                                'lineWidth' => 10,
                                'lineColor' => new JsExpression('Highcharts.getOptions().colors[5]'),
                                'fillColor' => 'white',
                            ],
                        ],

                    ]
                ]
                ]);
                ?>
       		</div>
    	</div>
	</div>
	<div class="col-md-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <div>
                    <div class="carousel-inner">


                    	<?php
                    	$month = 0;
						while ( $month<= 11) {
							echo '<div class="col-md-1 col-sm-4 col-xs-12">';
								echo '<a class="btn-openFigures" name="'.$year.'-'.sprintf("%02d", ($month+1)).'_'.$labId.'">';
								echo '<div class="info-box bg-'.$finalize[0].'">';
									echo '<span class="info-box-icon bg-entities bg-hover">';
									echo date('M',strtotime($year."-".($month+1)."-01"));
									echo '</span>';
								echo '</div>';
								echo '</a>';
								thumbsupsummary($factor_up[$month]);
								thumbsdownsummary($factor_down[$month]);
							echo '</div>';
							$month++;
						}
                    	?>
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

<?php 

function thumbsupsummary($data){
	$i = 0;
	while ($i < $data) {
		echo '<i class="fa fa-thumbs-up" style="color:green"></i>';	
		$i++;
	}
	return;
}

function thumbsdownsummary($data){
	$i = 0;
	while ($i < $data) {
		echo '<i class="fa fa-thumbs-down" style="color:red"></i>';	
		$i++;
	}
	return;
}

?>