<?php
/**
 * Created by PhpStorm.
 * User: OneLab
 * Date: 21/04/2020
 * Time: 15:29
 */
use yii\helpers\Html;
use \yii\helpers\StringHelper;
use \yii\helpers\Url;
use yii\widgets\DetailView;
/** @var $model common\modules\message\models\Chat */
/* @var $searchModel common\modules\message\models\ChatSearch */
?>

<?php

            echo "<i class='fa fa-' style='display:none;width: 0px; height: 15px'></i>";
            echo "<span>";
            echo "<span><b>";
            echo Html::encode($model->message);
            echo "</span></b><br>";

            echo "</span><br><br>";


?>
