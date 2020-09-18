<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;


?>
<div class="box-header modal-white">
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->widget(Select2::classname(), [
                'data' => ArrayHelper::map($factors,'title','title'),
                'language' => 'en',
                'options' => ['placeholder' => 'Select Factor','readonly'=>'readonly'],
                'pluginOptions' => [
                    'allowClear' => false
                ]
            ])->label('Lab'); ?>

        <?= $form->field($model, 'remarks'); ?>    

    <div class="form-group">
        <?php if(Yii::$app->request->isAjax){ ?>
            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
        <?php } ?>

        <?= Html::submitButton('Link Factor', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    
	<?php ActiveForm::end(); ?>
</div>