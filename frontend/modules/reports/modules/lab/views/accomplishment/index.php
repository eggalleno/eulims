<?php

use yii\helpers\Html;
//use yii\grid\GridView;
//use yii\bootstrap\Modal;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\lab\Lab;
use kartik\grid\Module;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\export\ExportMenu;
use kartik\grid\DataColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Accomplishment Report';
$this->params['breadcrumbs'][] = $this->title;

$pdfHeader="OneLab-Enhanced ULIMS";
$pdfFooter="{PAGENO}";
?>


<?php $this->registerJsFile("/js/services/services.js"); ?>
<div class="accomplishment-index">
<div class="panel panel-default col-xs-12">
        <div class="panel-body">
    	<?php
    		$form = ActiveForm::begin([
			    'id' => 'accomplishment-form',
			    'options' => [
					'class' => 'form-horizontal',
					//'data-pjax' => true,
				],
				'method' => 'get',
			])
    	?>
    
    		<div class="row">
		        <div id="lab-name" style="width:25%;position: relative; float:left;margin-right: 20px;">
		            <?php
		            	echo '<label class="control-label">Laboratory </label>';
						echo Select2::widget([
						    'name' => 'lab_id',
						    'id' => 'lab_id',
						    'value' => $lab_id,
						    'data' => $laboratories,
						    'theme' => Select2::THEME_KRAJEE,
						    'options' => ['placeholder' => 'Select Laboratory '],
						    'pluginOptions' => [
						        'allowClear' => true,
						    ],
						]);
		            ?>
		            <span class="error-lab text-danger" style="position:fixed;"></span>
		         </div>

		         <div id="date_range" style="position: relative; float: left;margin-left: 20px;">
    				<?php
				        echo '<label class="control-label">Year </label>';
		    		?>
		    		<?= Html::textinput('year',$year, $options=['class'=>'form-control','maxlength'=>10, 'style'=>'width:350px','type'=>'number', 'id'=>'the-year']) ?>

		    		<span class="error-date text-danger" style="position:fixed;"></span>
		    	</div>

		    	 <div style="width:15%;position: relative; float:left;margin: 27px 0 0 10px;">
		    		<button type="button" id="btn-filter" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search</button>
		    	</div>
		    </div>
		    <?php ActiveForm::end(); ?>

       </div>
       <br>
	    <div class="row">
        	<?php
			    $gridColumns= [
			    	['class' => 'kartik\grid\ActionColumn',
						'contentOptions' => ['style' => 'width: 8.7%'],
						'template' => '{view}',
						'buttons'=>[
							'view'=>function ($url, $model) {
								return Html::button('<span class="glyphicon glyphicon-print"></span>', ['value'=>Url::to(['/lab/tagging/monthlyreport','month'=>Yii::$app->formatter->asDate($model->request_datetime, 'php:M'), 'year'=>Yii::$app->formatter->asDate($model->request_datetime, 'php:Y')]), 'class' => 'btn btn-primary modal_method','onclick'=>'LoadModal(this.title, this.value ,true, 1850);','title' => Yii::t('app', "Monthly Report")]);
							},
						   
						],
					],
			    	'month',
			    	[
			    		'header'=> 'No. of Requests',
			    		'contentOptions' => ['class' => 'text-right'],
			    		'attribute' => 'totalrequests',
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],
			    	[
			    		'header'=> 'No. of Samples',
			    		'contentOptions' => ['class' => 'text-right'],
			    		'value'=> function( $model ) use($year,$lab_id){
			    			$monthyear = $year."-".$model->monthnum;
			    			return $model->getStats($monthyear,$lab_id,1);

			    		},
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],

			    	[
			    		'header'=> 'No. of Analyses',
			    		'contentOptions' => ['class' => 'text-right'],
			    		'value'=> function( $model ) use($year,$lab_id){
			    			$monthyear = $year."-".$model->monthnum;
			    			return $model->getStats($monthyear,$lab_id,2);

			    		},
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],
			    	[
			    		'header'=> 'Income Generated (Actual Fees Collected)',
			    		'contentOptions' => ['class' => 'text-right'],
			            'format'=>['decimal', 2],
			    		'value'=> function( $model ){
			    			return $model->total;

			    		},
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],
			    	[ //logically it wll return 0, but we will get back to this if there's anything needed
			    		'header'=> 'Gratis',
			    		'contentOptions' => ['class' => 'text-right'],
			    		'value' =>function($data){
			    			return '0.00';
			    		},
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],
			    	[
			    		'header'=> 'Discount',
			    		'contentOptions' => ['class' => 'text-right'],
			    		//'format'=>['decimal', 2],
			    		'value'=> function( $model ) use($year,$lab_id){
			    			$monthyear = $year."-".$model->monthnum;
			    			return $model->getStats($monthyear,$lab_id,3);
			    		},
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],
			    	[
			    		'header'=> 'Gross',
			    		'contentOptions' => ['class' => 'text-right'],
			    		//'format'=>['decimal', 2],
			    		'value'=> function( $model ) use($year,$lab_id){
			    			$monthyear = $year."-".$model->monthnum;
			    			$subtotal = $model->total;
			    			$discount = $model->getStats($monthyear,$lab_id,3);
			    			return ($subtotal + $discount);



			    		},
			    		'pageSummary'=>true,
        				'pageSummaryFunc'=>GridView::F_SUM,
        				'pageSummaryOptions'=>['class'=>'text-right text-primary'],
			    	],

			    	// 'samplescount'
			    ];

			    echo GridView::widget([
			    	'id' => 'accomplishment-report',
			        'dataProvider'=>$dataProvider,
			        //'filterModel'=>$searchModel,
			        'showPageSummary'=>true,
			        'summary' => false,
			        'pjax'=>true,
	                'pjaxSettings' => [
	                    'options' => [
	                        'enablePushState' => false,
	                    ]
	                ],
	                'responsive'=>true,
			        'striped'=>false,
			        'hover'=>true,
			        'panel' => [
	                    'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Accomplishment Report</h3>',
	                    'type'=>'primary',
	                    'after'=>false,
	                    //'before'=>$exportMenu,
	                    //'headerOptions' => ['class' => 'text-center'],
	                ],
			        'exportConfig' => [
				    	GridView::PDF => [
			                'filename' => 'Accomplishment_Report('.$year.')',
			                'alertMsg'        => 'The PDF export file will be generated for download.',
			                'config' => [
			                    'methods' => [
			                        'SetHeader' => [$pdfHeader],
			                        'SetFooter' => [$pdfFooter]
			                    ],
			                    'options' => [
			                        'title' => 'Accomplishment Report',
			                        'subject' => 'Accomplishment_Report',
			                        'keywords' => 'pdf, preceptors, export, other, keywords, here'
			                    ],
			                ]
			            ],
			            GridView::EXCEL => [
			                'label'           => 'Excel',
			                //'icon'            => 'file-excel-o',
			                'methods' => [
			                    'SetHeader' => [$pdfHeader],
			                    'SetFooter' => [$pdfFooter]
			                ],
			                'iconOptions'     => ['class' => 'text-success'],
			                'showHeader'      => TRUE,
			                'showPageSummary' => TRUE,
			                'showFooter'      => TRUE,
			                'showCaption'     => TRUE,
			                'filename'        =>  'Accomplishment_Report('.$year.')',
			                'alertMsg'        => 'The EXCEL export file will be generated for download.',
			                'options'         => ['title' => 'Department of Science OneLab'],
			                'mime'            => 'application/vnd.ms-excel',
			                'config'          => [
			                    'worksheet' => 'Accomplishment',
			                    'cssFile'   => ''
			                ]
			            ],
				    ],
			        'columns'=> $gridColumns,
			        'toolbar' => [
	                	'{export}',
	                ],
                'autoXlFormat'=>true,
                'export'=>[
                	'label' => 'Export',
			        'fontAwesome'=>true,
			        'showConfirmAlert'=>false,
			        'target'=>GridView::TARGET_SELF,
			    ],
			    'tableOptions'=>['id'=>'myTable'],
			    ]);
        	?>
    	</div>
</div>
</div>

<script type="text/javascript">
	$('#btn-filter').on('click',function(event){
	    event.preventDefault();
	    event.stopImmediatePropagation();
	    if($('#lab_id').val() == ''){
			$('#lab-name').addClass('has-error');
			$('.error-lab').html('Please select laboratory.').fadeIn('fast').fadeOut(3000);
		} else if ($('#the-year').val() == ''){
			$('#the-year').addClass('has-error');
			$('.error-date').html('Please specify year.').fadeIn('fast').fadeOut(3000);
		} else {
			$('#lab-name').removeClass('has-error');
			$('#the-year').removeClass('has-error');
			$('.error-lab').html('');
			$('.error-date').html('');
			$.pjax.reload({container:"#accomplishment-report-pjax",url: '/reports/lab/accomplishment?lab_id='+$('#lab_id').val()+'&year='+$('#the-year').val(),replace:false,timeout: false});
		}
	});
</script>
