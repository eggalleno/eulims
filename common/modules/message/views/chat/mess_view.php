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
?>
<script type="text/javascript">
    function showUser(str, strr) {
        const id= str;
        const idno = strr;
        document.getElementById("senderid").value=id;
        $.ajax({
            url: '/message/chat/getsendermessage',
            //dataType: 'json',
            method: 'GET',
            data: {id:id},
            success: function (data, textStatus, jqXHR) {
                $('#idconvo').html(data);
            }
        });
        /*document.getElementById("sendername").innerHTML = strr;*/
        $.ajax({
            url: '/message/chat/getsendermess',
            //dataType: 'json',
            method: 'GET',
            data: {id:idno},
            success: function (data, textStatus, jqXHR) {
                $('#sendername').html(data);
            }
        });

        $.ajax({
            url: '/message/chat/UpdateNewMess',
            //dataType: 'json',
            method: 'GET',
            data: {id:id},
            success: function (data, textStatus, jqXHR) {
                alert('sdsfs');
            }
        });

        /*if (str == "") {
            document.getElementById("sendername").innerHTML = "";
            return;
        } else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("sendername").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET","getuser.php?q="+str,true);
            xmlhttp.send();
        }*/
    }
</script>

<a class="thismessage" href="#" onclick="showUser(<?= $model->contact_id?>, <?= $model->sender_userid?>)">
<li class="clearfix">
<img src="/images/icons/customer.png" alt="avatar" style="width: 30px"/>
    <div class="about">
<?php
                    echo "<div class='name'>";
                    echo Html::encode($model->getProfile($model->sender_userid)->fullname);
                    echo "</div>";
                    echo "<div class='status'>";
                            if ($model->status_id == 1) {
                                echo "<img src='/images/icons/red.png' style='width: 5px'>";
                               /* echo "<i class='fa fa-circle offline'></i>";*/
                            }
                    echo StringHelper::truncateWords(($model->message), 2);
                    echo "</div>";
?>
    </div>
</li>
</a>

