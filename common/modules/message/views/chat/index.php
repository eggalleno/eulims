<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\message\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-index">
<div class="panel panel-default col-xs-12">
        <div class="panel-heading"><i class="fa fa-adn"></i> </div>
        <div class="panel-body">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Chat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'chat_id',
            'sender_userid',
            'reciever_userid',
            'message:ntext',
            'timestamp',
            // 'status_id',
            // 'group_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
        </div>
</div>
</div>
