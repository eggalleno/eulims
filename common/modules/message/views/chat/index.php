<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use kartik\file\FileInput;


/* @var $this yii\web\View */
/** @var $model common\modules\message\models\Chat */
/* @var $searchModel common\modules\message\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
    function sendfunc() {
        var senderid=document.getElementById("senderid").value;
        var message=document.getElementById("messagetosend").value;
        //alert(message);


        $.ajax({
            url: '/message/chat/sendmessage',
            //dataType: 'json',
            method: 'GET',
            data: {senderid:senderid,message:message},
            success: function (data, textStatus, jqXHR) {
                // $('#idconvo').html(data);
                alert('sdsfs');
            }
        });

        $.ajax({
            url: '/message/chat/saveattachment',

            method: 'GET',
            data: {senderid:senderid},
            success: function (data, textStatus, jqXHR) {
                alert(data);
            }
        });
    }
    function contName(){
        if (event.keyCode === 13) {
            document.getElementById("contactName").value='James';
        }
    }
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5eb52bb681d25c0e5849fc61/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>


<html lang="en" >
<div class="container clearfix">
    <div class="people-list" id="people-list">
        <div class="search">
            <input type="text" id="contName" placeholder="search" onkeypress="contName()"/>
            <i class="fa fa-search"></i>
        </div>
        <ul class="list">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => '',
                        'itemView' => 'mess_view'
                    ]);
                    ?>
        </ul>
    </div>

    <div class="chat" >
        <div class="chat-header clearfix">
                <div class="chat-about">
                    <div class="chat-with" id="sendername"></div>
                    <div class="chat-num-messages"></div>
                </div>
            <i class="fa fa-phone"></i>

        </div><!-- end chat-header -->

        <div class="chat-history" >
            <ul id="idconvo">

            </ul>
        </div><!-- end chat-history -->

        <input type="text" id="senderid" value="" hidden>
        <div class="chat-message clearfix">
            <textarea name="message-to-send" id="messagetosend" placeholder ="Type your message" rows="3"></textarea>
			<div class="file-loading">
				<?php
				echo '<label class="control-label">Upload Document</label>';
				echo FileInput::widget([
				    'model' => $file,
                    'attribute' => 'filename',
					'options' => ['multiple' => true],
					'pluginOptions' => [
						'showPreview' => true,
						'showCaption' => true,
						'showUpload' => false,
						'showRemove'=>true
					]
				]);
				?>
			</div><br><br>
            <button id="send" onclick="sendfunc()">Send</button>

        </div> <!-- end chat-message -->

    </div> <!-- end chat -->

</div> <!-- end container -->

<script id="message-template" type="text/x-handlebars-template">
    <li class="clearfix">
        <div class="message-data align-right">
            <span class="message-data-time" >{{time}}, Today</span> &nbsp; &nbsp;
            <span class="message-data-name" >Olia</span> <i class="fa fa-circle me"></i>
        </div>
        <div class="message other-message float-right">
            {{messageOutput}}
        </div>
    </li>
</script>

<script id="message-response-template" type="text/x-handlebars-template">
    <li>
        <div class="message-data">
            <span class="message-data-name"><i class="fa fa-circle online"></i> Vincent</span>
            <span class="message-data-time">{{time}}, Today</span>
        </div>
        <div class="message my-message">
            {{response}}
        </div>
    </li>
</script>
<!-- partial -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.0/handlebars.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js'></script><script  src="./script.js"></script>

</body>
</html>

