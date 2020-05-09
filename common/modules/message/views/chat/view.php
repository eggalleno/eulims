<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\message\models\Chat */

$this->title = $model->chat_id;
$this->params['breadcrumbs'][] = ['label' => 'Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div class="chat-header clearfix">
    <div class="chat-about">
        <div class="chat-with"></div>
    </div>

</div>-->
<!--<div class="chat-history">
    <ul>-->
        <?php

            if($model->sender_userid == Yii::$app->user->id){
                echo '<li class="clearfix">';
                echo "<div class='message-data align-right'>";

                echo "<span class='message-data-time' >";
                echo Yii::$app->formatter->asRelativeTime($model->timestamp);
                echo"</span> &nbsp &nbsp";

                echo "<span class='message-data-name' >";
                echo 'Me';

                echo "</div>";

                echo "<div class='message other-message float-right'>";
                echo Html::encode($model->message);
                echo "</div>";
                echo '</li>';
            }
            else{
                echo '<li>';
                echo "<div class='message-data'>";

                echo "<span class='message-data-name' >";
                echo Html::encode($model->getProfile($model->sender_userid)->firstname);
                echo "</span> <i class='fa fa-circle online'></i>";

                echo "<span class='message-data-time' >";
                echo Yii::$app->formatter->asRelativeTime($model->timestamp);
                echo"</span> &nbsp &nbsp";

                echo "</div>";

                echo "<div class='message my-message'>";
                echo Html::encode($model->message);
                echo "</div>";
                echo '</li>';
                }

        ?>
<!--    </ul>
</div>-->






