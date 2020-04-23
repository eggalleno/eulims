<?php
use kartik\grid\GridView;
use yii\helpers\Html;


?>
<?= \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'itemView' => 'view'
]);
?>
