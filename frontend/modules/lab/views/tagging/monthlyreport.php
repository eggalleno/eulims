<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\lab\Customer;
use common\models\lab\Discount;
use common\models\lab\Lab;
use common\models\lab\Sample;
use common\models\lab\Analysis;
use common\models\lab\Testcategory;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;



/* @var $this yii\web\View */
/* @var $searchModel common\models\lab\LabsampletypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo "<h1>Monthly Report for <b>".$month." ".$year."</b></h1>";

?>
    <?php $this->registerJsFile("/js/services/services.js"); ?>

    <?= GridView::widget([
        'dataProvider' => $requestdataprovider,
        'id'=>'analysis-grid',
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-analysis']],
        'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => '<span class="glyphicon glyphicon-book"></span>  Monthly Report' ,
               // 'footer'=>Html::button('<i class="glyphicon glyphicon-ok"></i> Start Analysis', ['disabled'=>false,'value' => Url::to(['tagging/startanalysis','id'=>1]), 'onclick'=>'startanalysis()','title'=>'Start Analysis', 'class' => 'btn btn-success','id' => 'btn_start_analysis'])." ".
                Html::button('<i class="glyphicon glyphicon-ok"></i> Completed', ['disabled'=>false,'value' => Url::to(['tagging/completedanalysis','id'=>1]),'title'=>'Completed', 'onclick'=>'completedanalysis()', 'class' => 'btn btn-success','id' => 'btn_complete_analysis']),
            ],
        'pjaxSettings' => [
            'options' => [
                'enablePushState' => false,
            ]
        ],
        'floatHeaderOptions' => ['scrollingTop' => true],
        'columns' => [
            'request_ref_num',
            'customer.customer_name',
            'customer.Completeaddress',
            [
                'header'=>'Setup',
                'format' => 'raw',
                'width' => '80px',
                'enableSorting' => false,
                'value' => function($model) {
                    if ($model->customer->customer_type_id==1){
                        return "Yes";
                    }else{
                        return "No";
                    }
                    
                },
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],                 
            ],
             [
                'header'=>'Sample Name',
                'format' => 'html',
                'width' => '400px',
                'headerOptions' => ['style' => 'width:400px'],
                'enableSorting' => false,
                'value' => function($model) {
                    $value="";
                    foreach($model->samples as $sample){
                        $value .= $sample->samplename;
                         foreach ($sample->analyses as $analysis) {
                            $value .= "<br/>";
                        }
                    }
                    return $value;
                },
                'contentOptions' => ['style' => 'width:400px; white-space: normal;'],                 
            ],
            [
                'header'=>'Sample Code',
                'format' => 'html',
                'width' => '200px',
                'headerOptions' => ['style' => 'width:200px'],
                'enableSorting' => false,
                'value' => function($model) {
                    $value="";
                    foreach($model->samples as $sample){
                        $value .= $sample->sample_code;
                         foreach ($sample->analyses as $analysis) {
                            $value .= "<br/>";
                        }
                    }
                    return $value;
                },
                'contentOptions' => ['style' => 'width:200px; white-space: normal;'],                 
            ],
            [
                'header'=>'Test Name',
                'format' => 'raw',
                'width' => '200px',
                'headerOptions' => ['style' => 'width:200px'],
                'enableSorting' => false,
                'value' => function($model) {
                    $value="";
                    foreach($model->samples as $sample){
                        foreach ($sample->analyses as $analysis) {
                            $value .= $analysis->testname."<br/>";
                        }
                    }
                    return $value;
                },
                'contentOptions' => ['style' => 'width:200px; white-space: normal;'],                 
            ],
            [
                'header'=>'SubTotal',
                'format' => 'raw',
                'width' => '100px',
                'enableSorting' => false,
                'value' => function($model) {
                    $ids="";
                    $amount =0;
                    $samplesquery = Sample::find()->where(['request_id' => $model->request_id])->all();
                    foreach($samplesquery as $samples){
                        $ids .= $samples['sample_id'].",";
                        if($samples->analyses){
                            foreach ($samples->analyses as $analysis) {
                               $amount += $analysis->fee; 
                            }
                        }
                    }
                    return number_format((float)$amount, 2, '.', '');
                },
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],                 
            ],
            [
                'header'=>'Discount',
                'format' => 'raw',
                'width' => '100px',
                'enableSorting' => false,
                'value' => function($model) {
                    $discountquery = Discount::find()->where(['discount_id' => $model->discount_id])->one();
                    $rate =  $discountquery->rate;
                    return $rate.' %';
                },
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],                 
            ],
            'total'
        ],
    ]); 
    ?>



   

