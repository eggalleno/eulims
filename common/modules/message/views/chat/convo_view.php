<?php
use kartik\grid\GridView;
use yii\helpers\Html;


?>
<?php 
    $gridColumn = [
        ['class' => 'kartik\grid\SerialColumn'
        ],
       
        [
            'attribute'=>'sender_userid',
            'enableSorting' => false,
        ],            
        [
          'attribute'=>'chat_id',
          'enableSorting' => false,
        ],
		'message'
        
      
        
    ];
?>    
 <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
         'columns' =>$gridColumn,
 
    ]); ?>
