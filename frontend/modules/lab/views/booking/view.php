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
        <?php
		if ($model->booking_status <> 1){
			echo Html::a('Save as Request', ['saverequest', 'id' => $model->booking_id], ['class' => 'btn btn-success']);
			echo "&nbsp;&nbsp;";
			echo Html::a('Cancel Booking', ['cancelbooking', 'id' => $model->booking_id], ['class' => 'btn btn-danger']) ;
			echo "&nbsp;&nbsp;";
			echo Html::a('Save as new Customer', ['savecustomer', 'id' => $model->booking_id], ['class' => 'btn btn-success']) ;
		}
		
		?>
        <?php
	//if ($model->customer ? $model->customer->status : 1 != 1) {
		//	echo Html::a('Save as new Customer', ['savecustomer', 'id' => $model->booking_id], ['class' => 'btn btn-success']) ;
	//	}
		
		?>
    </p>
	
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
                        'value'=>$customer ? $customer->customer_name : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Contact Number',
                        'format'=>'raw',
                        'value'=>$customer ? $customer->tel : "",
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
                        'value'=>$customer ? $customer->email : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Address',
                        'format'=>'raw',
                        'value'=>$customer ? $customer->address : "",
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                ],
                    
            ],
			[
                    'group'=>true,
                    'label'=>'Sample booking details ',
                    'rowOptions'=>['class'=>'info']
            ],
			[
                'columns' => [
                    [
                        'label'=>'Scheduled Date',
                        'format'=>'raw',
                        'value'=>$model->scheduled_date,
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Booking Reference',
                        'format'=>'raw',
                        'value'=>$model->booking_reference,
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                ],
                    
            ],
			[
                'columns' => [
                    [
                        'label'=>'Date Created',
                        'format'=>'raw',
                        'value'=>$model->date_created,
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Quantity Sample',
                        'format'=>'raw',
                        'value'=>$model->qty_sample,
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                ],
                    
            ],
			[
                'columns' => [
                    [
                        'label'=>'Sample name',
                        'format'=>'raw',
                        'value'=>$model->samplename,
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                    [
                        'label'=>'Decription',
                        'format'=>'raw',
                        'value'=>$model->description,
                        'valueColOptions'=>['style'=>'width:30%'], 
                        'displayOnly'=>true
                    ],
                ],
                    
            ],
        ],
    ]) ?>
	

</div>
