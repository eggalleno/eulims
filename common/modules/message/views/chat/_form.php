<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\message\models\Chat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chat-form">

    <?php $form = ActiveForm::begin(); ?>

	
    <?php 

		echo $form->field($model, 'reciever_userid')->widget(Select2::classname(), [
		'data' => $possible_recipients,
		'theme' => Select2::THEME_BOOTSTRAP,
		'options' => ['placeholder' => 'Choose the recipient'],
		'pluginOptions' => [
		  'allowClear' => true
		],
		]);
    ?> 
    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
