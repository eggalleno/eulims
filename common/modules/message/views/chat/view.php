<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\message\models\Chat */

$this->title = $model->chat_id;
$this->params['breadcrumbs'][] = ['label' => 'Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-view">

    <h1><?= Html::encode($model->getProfile($model->sender_userid)->fullname)?></h1>

    <p>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'message:ntext'
        ],
    ]) ?>

</div>
