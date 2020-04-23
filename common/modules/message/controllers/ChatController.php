<?php

namespace common\modules\message\controllers;

use Yii;
use common\modules\message\models\Chat;
use common\modules\message\models\ChatSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ChatController implements the CRUD actions for Chat model.
 */
class ChatController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Chat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChatSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $query = $this->Getallmessage();
		
        $dataProvider = New ActiveDataProvider(['query'=>$query]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

/*    Public function actionView($sendId){
        $searchmodel = new ChatSearch();
        $query = Chat::find()->where(['reciever_userid' => Yii::$app->user->id, 'sender_userid' => $sendId])
                            ->orderBy('timestamp');
        
		$dataProvider = New ActiveDataProvider(['query' => $query]);
        return $this->render('index',[
            'searchModel' => $searchmodel,
            'dataProvider' => $dataProvider,
        ]);
    }*/

    /**
     * Displays a single Chat model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($sendId)
    {
        return $this->render('view', [
            'model' => $this->findModel($sendId),
        ]);
    }

    /**
     * Creates a new Chat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Chat();
       
        $possible_recipients = Chat::getPossibleRecipients();
		$recipients=ArrayHelper::map($possible_recipients, 'id', 'username');
        if ($model->load(Yii::$app->request->post())) {
		
			try{
			$model->sender_userid= Yii::$app->user->id;
			$model->reciever_userid= Yii::$app->user->id; //For testing only
			$model->status_id=1;//sent
			$model->save(false);
			
			}
			catch (Exception $e) {
                   print_r($e);
				   exit;
             }
			 Yii::$app->session->setFlash('success', 'Message Sent!');
            return $this->redirect(['/message/chat/index']);
        } else {
            return $this->render('create', [
                'model' => $model,
				'possible_recipients' => $recipients,
            ]);
        }
    }

    /**
     * Updates an existing Chat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->chat_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Chat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Chat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Chat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Chat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function Getallmessage(){
		$query = Chat::find()->where(['reciever_userid' => Yii::$app->user->id])
							->groupBy('sender_userid')
                            ->orderBy(['timestamp'=>SORT_DESC]);
		return $query;					
	}
	
	public function actionGetsendermessage($id)
    {
		$query = Chat::find()->where(['reciever_userid' => Yii::$app->user->id, 'sender_userid' => $id])
                            ->orderBy('timestamp');

		$dataProvider = New ActiveDataProvider(['query' => $query]);
     
        if(Yii::$app->request->isAjax){
			//return $id;
			return $this->renderAjax('convo_view', ['dataProvider'=>$dataProvider]);
        }
       
		
	}

}
