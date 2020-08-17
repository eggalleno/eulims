<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\lab\Booking */

$this->title = $model->booking_id;
$this->params['breadcrumbs'][] = ['label' => 'Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">


    <p>
        <?= Html::a('Save as Request', ['saverequest', 'id' => $model->booking_id], ['class' => 'btn btn-success
		']) ?>
       
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'booking_id',
            'scheduled_date',
            'booking_reference',
            'description',
            'rstl_id',
            'date_created',
            'qty_sample',
            'customer_id',
        ],
    ]) ?>
	
	  <?= DetailView::widget([
        'model'=>$model,
        'responsive'=>true,
        'hover'=>true,
        'mode'=>DetailView::MODE_VIEW,
        'panel'=>[
            'heading'=>'<i class="glyphicon glyphicon-book"></i> Booking Details: ',
            'type'=>DetailView::TYPE_PRIMARY,
        ],
        'buttons1' => '',
        'attributes' => [
            [
                    'group'=>true,
                    'label'=>'Customer Details ',
                    'rowOptions'=>['class'=>'info']
            ],
            [
                'columns' => [
                    [
                        'label'=>'Customer Name',
                        'format'=>'raw',
                        'value'=>$model->customer ? $model->customer->customer_name : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Contact Number',
                        'format'=>'raw',
                        'value'=>$model->customer ? $model->customer->tel : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                ],
                    
            ],
			  [
                'columns' => [
                    [
                        'label'=>'Email',
                        'format'=>'raw',
                        'value'=>$model->customer ? $model->customer->email : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Address',
                        'format'=>'raw',
                        'value'=>$model->customer ? $model->customer->address : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                ],
                    
            ],
        ],
    ]) ?>
	

</div>
