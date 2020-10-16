<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group pull-right">
					<?= Html::submitButton('Login', ['class' =>'btn btn-primary','id'=>'btnlogin']) ?>
					<?php if(Yii::$app->request->isAjax){ ?>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<?php } ?>
				</div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
