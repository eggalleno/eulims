<?php

use yii\helpers\Html;
use \yii\helpers\StringHelper;
use \yii\helpers\Url;
use yii\widgets\DetailView;
/**
 * Created by PhpStorm.
 * User: OneLab
 * Date: 16/04/2020
 * Time: 21:52
 */
/** @var $model common\modules\message\models\Chat */
/* @var $searchModel common\modules\message\models\ChatSearch */
$id="";
$name="";
if($model->sender_userid == Yii::$app->user->id){
	$id=$model->reciever_userid;
	$name=($model->getProfile($model->reciever_userid)->fullname);
}
else{
	$id=$model->sender_userid;
    $name=($model->getProfile($model->sender_userid)->fullname);
}
?>
<script type="text/javascript">
   /* $(document).ready(function(){
        $("a.thismessage").on('click',function (ee) {
            const id=($(this).attr("id"));
            //alert(id);
            document.getElementById("chat-sender_userid").value=id;

            $.ajax({
                url: '/message/chat/getsendermessage',
                //dataType: 'json',
                method: 'GET',
                data: {id:id},
                success: function (data, textStatus, jqXHR) {
                    $('#idconvo').html(data);
                }
            });

        });
    });*/
function mes(id,mm) {
    const ido=id;
    document.getElementById("receiver").innerText=mm;
    $.ajax({
        url: '/message/chat/getsendermessage',
        //dataType: 'json',
        method: 'GET',
        data: {id:ido},
        success: function (data, textStatus, jqXHR) {
            $('#idconvo').html(data);
        }
    });
    /*const jam = ido;
        document.getElementById("receiver").innerText=jam;*/
}
</script>
<a class="thismessage" onclick="mes('<?= $model->contact_id?>', '<?php echo $name;?>')" href="#">
<li class="clearfix">
<img src="/images/icons/customer.png" alt="avatar" style="width: 30px"/>
    <div class="about">
<?php
                    echo "<div class='name'>";
                    echo Html::encode($model->getProfile($id)->fullname);
                    echo "</div>";
                    echo "<div class='status'>";
                            if ($model->status_id == 1) {
                                echo "<i class='fa fa-circle offline'></i>";
                            }
                    echo StringHelper::truncateWords(($model->message), 5);
                    echo "</div>";
?>
    </div>
</li>
</a>

