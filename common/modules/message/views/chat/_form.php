<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\message\models\Chat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'chat_id')->textInput() ?>

    <?= $form->field($model, 'sender_userid')->textInput() ?>

    <?= $form->field($model, 'reciever_userid')->textInput() ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'timestamp')->textInput() ?>

    <?= $form->field($model, 'status_id')->textInput() ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
