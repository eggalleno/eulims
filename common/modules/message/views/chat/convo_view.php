<?php
use kartik\grid\GridView;
use yii\helpers\Html;

/** @var $model common\modules\message\models\Chat */
/* @var $searchModel common\modules\message\models\ChatSearch */
?>
 <!-- end chat-header -->
            <?=
            \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'itemView' => 'view'
            ]);
            ?>

