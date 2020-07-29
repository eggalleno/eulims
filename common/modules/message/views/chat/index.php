<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use kartik\file\FileInput;
use yii\helpers\Url;
use common\components\Functions;

$func= new Functions();

use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/** @var $model common\modules\message\models\Chat */
/* @var $searchModel common\modules\message\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    $(document).ready(

        function() {
            setInterval(function() {
                $.pjax.reload('#kv-pjax-container-inbox', {timeout : false})
            }, 3000);
        });
    function SearchMess(name) {
        const id = name;
        $.ajax({
            url: '/message/chat/GetSearchMessage',
            //dataType: 'json',
            method: 'GET',
            data: {id:ido},
            success: function (data, textStatus, jqXHR) {
                $('#inbox').html(data);
            }
        });
    }
    function openForm() {
        document.getElementById("myForm").style.display = "block";
    }

    function closeForm() {
        document.getElementById("myForm").style.display = "none";
    }
</script>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>OneLab Chat</title>

</head>
<div class="container clearfix">
    <div class="people-list" id="people-list">
        <!-- <label style="color:white"> <h4>Chats </h4></label> -->
        <div class="search">
            <input type="text" placeholder="search" onkeydown="SearchMess('jamestorres')"/>
            <i class="fa fa-search"></i>
        </div>
        <div class="inbox-history">


            <ul class="sidebar-menu tree" data-widget="tree">
                <li class="treeview">
                    <a href="/message/chat"><i class="fa fa-inbox" style="display:none;width:0px" "=""></i>
                        <span>
                            <span>Inbox</span>
                        </span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu" id="inbox">
                        <!--<ul class="list" >-->
                            <?php \yii\widgets\Pjax::begin(['timeout' => 1, 'id'=>"kv-pjax-container-inbox", 'clientOptions' => ['container' => 'pjax-container']]); ?>
                            <?= \yii\widgets\ListView::widget([
                                'dataProvider' => $dataProvider,
                                'summary' => '',
                                'itemView' => 'mess_view'
                            ]);
                            ?>
                            <?php \yii\widgets\Pjax::end(); ?>
                        <!--</ul>-->
                    </ul>
                </li>

                <li class="treeview">
                    <a href="/message/chat"><i class="fa fa-inbox" style="display:none;width:0px" "=""></i>
                        <span>
                            <span>Group Message</span>
                        </span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu" id="inbox">
                        <!--<ul class="list" >-->
                        
                        <!--</ul>-->
                    </ul>
                </li>
            </ul>

        </div>
    </div>

    <div class="chat" >
        <div class="chat-header clearfix">
            <div class="chat-about">
                <div class="chat-with" id="receiver"></div>
                <div class="chat-num-messages"></div>
            </div>
            <!--<i class="fa fa-star"></i>-->
            <!--<a href="/message/chat/group"><i class="fa fa-group" onclick="LoadModal(this.title, this.value);"></i></a>-->
            <a onclick="LoadModal('Create Group', '/message/chat/group');" href="#"><i class="fa fa-group"></i></a>
            <a href="/message/chat/create"><i class="fa fa-plus-circle"></i></a>
        </div> <!-- end chat-header -->

        <div class="chat-history" id="chatHistory">
            <ul id="idconvo">

            </ul>
        </div> <!-- end chat-history -->

        <div class="chat-message clearfix">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($chat, 'sender_userid')->hiddenInput()->label(false) ?>
            <?= $form->field($chat, 'message')->textarea(['rows' => 2]) ?>



            <?= $form->field($file, 'filename')->widget(FileInput::classname(),
                [
                    'options' => ['multiple' => true],
                    'pluginOptions' => [
                        'showPreview' => true,
                        'showCaption' => true,
                        'showUpload' => false,
                        'showRemove'=>true
                    ]

                ]);
            ?>
            <div class="form-group">
                <?= Html::submitButton($chat->isNewRecord ? 'Send' : 'Update', ['class' => $chat->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div> <!-- end chat-message -->

    </div> <!-- end chat -->
    <div class="inbox">
        <div class="inbox-header">
            <h3></h3>
        </div>
    </div>
</div> <!-- end container -->

<!--<script id="message-template" type="text/x-handlebars-template">
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
</script>-->
<button class="open-button" onclick="openForm()">Chat</button>
<div class="chat-popup" id="myForm">
    <form action="/action_page.php" class="form-container">
        <h1>Chat</h1>
        <label for="msg"><b>Message</b></label>
        <textarea placeholder="Type message.." name="msg" required></textarea>
        <button type="submit" class="btn">Send</button>
        <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
    </form>
</div>
<!-- partial -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.0/handlebars.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js'></script><script  src="./script.js"></script>

</body>
</html>
