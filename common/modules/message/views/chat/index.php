<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\message\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-index">
<div class="panel panel-default col-xs-12">
        <!--<div class="panel-heading"><i class="fa fa-adn"></i> </div>-->
        <div class="panel-body">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
   <!-- = GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'chat_id',
            [
               'label'=>'Sender', 
               'format'=>'raw',
                
               'value'=>function($model){
                    $Obj=$model->getProfile($model->sender_userid);
                    return $Obj->fullname;
                   
                },   
                'hAlign'=>'left',
                'width' => '30%',  
                 'contentOptions' => [
                    'style'=>'max-width:150px; overflow: auto; white-space: normal; word-wrap: break-word;'
                ],
            ],
            [
                'label'=>'Receiver',
                'format'=>'raw',

                'value'=>function($model){
                    $Obj=$model->getProfile($model->reciever_userid);
                    return $Obj->fullname;

                },
                'hAlign'=>'left',
                'width' => '30%',
                'contentOptions' => [
                    'style'=>'max-width:150px; overflow: auto; white-space: normal; word-wrap: break-word;'
                ],
            ],
            'message:ntext',
            'timestamp',
            // 'status_id',
            // 'group_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); -->
            <div class="user-panel" style="height: 700px; width: 370px; background-color: #222d32">
                <p>
                    <?= Html::a('Create Message', ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                <aside class="main-header">
                    <section class="sidebar">
                        <div class="user-panel" style="height: 700px; width: 340px">
                            <ul class="sidebar-menu tree" data-widget="tree">
                                <li class="treeview">
                                    <a>
                                        <i class="fa fa-" style="display:none; width:0px"></i>
                                        <span>
                                            <img src="/images/icons/reports.png" style="width: 20px">
                                            <span>Inbox</span>
                                        </span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>

                                    <ul class='treeview-menu'>
                                        <div class="pre-scrollable" style="height: 200px">
                                                    <?= \yii\widgets\ListView::widget([
                                                        'dataProvider' => $dataProvider,
                                                        'summary' => '',
                                                        'itemView' => 'mess_view'
                                                    ]);
                                                    ?>
                                        </div>
                                    </ul>



                                </li>
                                <li class="treeview">
                                    <a>
                                        <i class="fa fa-" style="display:none; width:0px"></i>
                                        <span>
                                                <img src="/images/icons/customer.png" style="width: 20px">
                                                <span>Contact List</span>
                                            </span>
                                        <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                    </a>
                                </li>
                                <li class="treeview">
                                    <a>
                                        <i class="fa fa-" style="display:none; width:0px"></i>
                                        <span>
                                            <img src="/images/icons/customerpool.png" style="width: 20px">
                                            <span>Group Chat</span>
                                        </span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </section>
                </aside>
            </div>

        </div>
</div>
</div>
