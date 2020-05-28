<?php

namespace common\modules\message\controllers;

use common\modules\message\models\Contacts;
use Yii;
use common\modules\message\models\Chat;
use common\modules\message\models\ChatSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\modules\message\models\ChatAttachment;
use yii\web\UploadedFile;

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
		
		$file= new ChatAttachment();
		$chat=new Chat();
		if ($chat->load(Yii::$app->request->post()) and $file->load(Yii::$app->request->post())) {
			$sds = UploadedFile::getInstance($file, 'filename');

			//save message
			$contact = Contacts::find()->where(['contact_id'=>$chat->sender_userid])->one();
			$contact_id=$contact->user_id;
			$str_total = explode(',', $contact_id);
            $arr_length = count($str_total); 
			$receiverid="";
            for($i=0;$i<$arr_length;$i++){
                 if(Yii::$app->user->id != $str_total[$i]){
					$receiverid= $str_total[$i];
				 }
            }
			$chat->reciever_userid=$receiverid;
			/////////////////////////////////////////////////////////
			$chat->contact_id=$chat->sender_userid;
			$chat->sender_userid= Yii::$app->user->id;
			$chat->status_id=1;//sent
			$chat->message=$chat->message;
			$chat->save();
			//end of save message-----
			
			//for file attachment
			if (!empty($sds) && $sds !== 0) {                
                $sds->saveAs('uploads/message/' . $chat->chat_id.'.'.$sds->extension);
                $file->filename ='uploads/message/'.$chat->chat_id.'.'.$sds->extension;
				
				$this->Saveattachment($file->filename,$chat->contact_id);
            }
			
			//end
			
			Yii::$app->session->setFlash('success', 'Message Sent!'); 
			return $this->redirect(['/message/chat/index']);
			
		}else{
			return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'file'=>$file,
			'chat'=>$chat
			]);
		}
        
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
	
			$userid=Yii::$app->user->id;
			$recipientid=$model->reciever_userid;
		
			$arr = [$userid,$recipientid];
			sort($arr);
			$str = implode(",", $arr); 
			
			$contact = Contacts::find()->where(['user_id'=>$str])->one();
			
			$id="";   
			if (!$contact){
			
				$convo= new Contacts();
				$convo->user_id=$str;
				$convo->save(false);
				$id=$convo->contact_id;
			}else{
				$id=$contact->contact_id;
			}
			//Send message
			$model->sender_userid= Yii::$app->user->id;
			$model->status_id=1;//sent
			$model->contact_id=$id;
			$model->save(false); 
			
			//////
			
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
		$query = Chat::find()
							->andWhere(['or',
								   ['reciever_userid'=>Yii::$app->user->id],
								   ['sender_userid'=>Yii::$app->user->id]
							   ])
							->groupBy('contact_id')
                            ->orderBy(['timestamp'=>SORT_DESC]);
		return $query;					
	}
	
	public function actionGetsendermessage($id)
    {
		$query = Chat::find()
		//->where(['reciever_userid' => Yii::$app->user->id, 'sender_userid' => $id])
							->andWhere(['or',
								   ['contact_id'=>$id]
							   ])
                            ->orderBy('timestamp');

		$dataProvider = New ActiveDataProvider(['query' => $query]);
     
        if(Yii::$app->request->isAjax){
			//return $id;
			return $this->renderAjax('convo_view', ['dataProvider'=>$dataProvider]);
        }
       
		
	}
	
	public function Sendmessage($senderid,$message,$contactid)
    {
		$model = new Chat();
		$model->sender_userid= Yii::$app->profile->id;
		$model->reciever_userid= $senderid; //
		$model->status_id=1;//sent
        $arr = [Yii::$app->user->id,$senderid];
        sort($arr);
        $str = implode(",", $arr);
        $contact = Contacts::find()->where(['user_id'=>$str])->one();
        if($contact !=null) {
            $model->contact_id = $contact->user_id;
        }
        $model->message=$message;
		$model->save();
		return;
	}
	
	public function Saveattachment($filename,$id)
    {
		$model = new ChatAttachment();
		$model->uploadedby_userid= Yii::$app->user->id;
		$model->filename= $filename; 
		$model->contact_group_id=$id;
		$model->save(); 
		return ;
		
	}
	

}
