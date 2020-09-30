<?php

namespace frontend\modules\api\controllers;

use Yii;

use common\models\system\LoginForm;
use common\models\system\Profile;
use common\models\system\User;
use common\models\auth\AuthAssignment;
use common\models\system\Rstl;

use common\models\message\Chat;
use common\models\message\Contacts;
use common\models\message\GroupMember;
use common\models\message\ChatGroup;
use yii\web\UploadedFile;

class MessageController extends \yii\rest\Controller
{
	 public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            'except' => ['login', 'server'],
            'user'=> [\Yii::$app->referralaccount]
        ];

        return $behaviors;
    }

    protected function verbs(){
        return [
            'login' => ['POST'],
            'logout' => ['POST'],
            'user' => ['GET'],
             'setmessage' => ['POST'],
             'data' => ['GET'],
        ];
    }
	
	 public function actionLogin()
    {
            $model = new LoginForm();
            $my_var = \Yii::$app->request->post();
            $model->email = $my_var['email'];
            $model->password = $my_var['password'];
           
            if ($model->login()) {      
                $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
                /** @var Jwt $jwt */
                $jwt = \Yii::$app->jwt;
                $token = $jwt->getBuilder()
                    ->setIssuer('http://example.com')// Configures the issuer (iss claim)
                    ->setAudience('http://example.org')// Configures the audience (aud claim)
                    ->setId('4f1g23a12aa', true)// Configures the id (jti claim), replicating as a header item
                    ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
                    ->setExpiration(time() + 3600 * 2400000)// Configures the expiration time of the token (exp claim)
                    ->set('uid', \Yii::$app->user->identity->user_id)// Configures a new claim, called "uid"
                    //->set('username', \Yii::$app->user->identity->username)// Configures a new claim, called "uid"
                    ->sign($signer, $jwt->key)// creates a signature using [[Jwt::$key]]
                    ->getToken(); // Retrieves the generated token
    
                    $users = User::find()->where(['LIKE', 'email', $my_var['email']])->one();
                    $profile = Profile::find()->where(['user_id'=>$users->user_id])->one();
                    $role = AuthAssignment::find()->where(['user_id'=>$users->user_id])->one();
        
                    return $this->asJson([
                        'token' => (string)$token,
                        'user'=> (['email'=>$users->email,
                                    'firstName'=>$profile->firstname,
                                    'middleInitial' => $profile->middleinitial,
                                    'lastname' => $profile->lastname,
                                    'type' => $role->item_name,]),
                    ]);
                } else {
                    return $this->asJson([
                        'success' => false,
                        'message' => 'Email and Password didn\'t match',
                    ]);
                }
    }

    public function actionUser()
    {  
        $user_id =\Yii::$app->user->identity->profile->user_id;
        $users = User::find()->where(['LIKE', 'user_id', $user_id])->one();
        $profile = Profile::find()->where(['user_id'=>$user_id])->one();
        $role = AuthAssignment::find()->where(['user_id'=>$users->user_id])->one();
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        /** @var Jwt $jwt */
        $jwt = \Yii::$app->jwt;
        $token = $jwt->getBuilder()
            ->setIssuer('http://example.com')// Configures the issuer (iss claim)
            ->setAudience('http://example.org')// Configures the audience (aud claim)
            ->setId('4f1g23a12aa', true)// Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
            ->setExpiration(time() + 3600 * 24)// Configures the expiration time of the token (exp claim)
            ->set('uid', \Yii::$app->user->identity->user_id)// Configures a new claim, called "uid"
            //->set('username', \Yii::$app->user->identity->username)// Configures a new claim, called "uid"
            ->sign($signer, $jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken(); // Retrieves the generated token
        return $this->asJson([
                'token' => (string)$token,
                'user'=> (['email'=>$users->email,
                'firstName'=>$profile->firstname,
                'middleInitial' => $profile->middleinitial,
                'lastname' => $profile->lastname,
                'type' => $role->item_name]),
                'user_id'=> $users->user_id
            ]);               
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogout()
    {
        return $this->render('index');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionData()
    {
        return $this->getuserid();
    }

    function getuserid(){
        $myvar = \Yii::$app->request->headers->get('Authorization');

        $rawToken = explode("Bearer ", $myvar);
        $rawToken = $rawToken[1];
        $token = \Yii::$app->jwt->getParser()->parse((string) $rawToken);
        return $token->getClaim('uid');
    }

     //************************************************
     public function actionServer(){

        $server = $_SERVER['SERVER_NAME'];
        if(!$sock = @fsockopen($server, 80))
            {
                $data = array("status" => "offline");
            }
            else
            {
                $data = array("status" => "online");
            }

           
        return $this->asJson($data);   
    }
	
    public function actionSetmessage(){ //send message
        $my_var = \Yii::$app->request->post();


       if(!$my_var){
            return $this->asJson([
                'success' => false,
                'message' => 'POST empty',
            ]); 
       }else{
			//attributes Purpose, Sample Quantity, Sample type, Sample Name and Description, schedule date and datecreated
			$chat = new Chat();
			$chat->sender_userid = $my_var['sender_userid'];
			$chat->chat_data= $my_var['message'];
			$type=$my_var['type'];
			$id=$my_var['id'];
			$chat->status_id=1;//sent
			$chat->chat_data_type=1; //message text
			$chat->message_type=$type; //personnel message
			//tbl_contact
			if($type == 1){
				$chat->contact_id=$id;
			}else{
				$chat->group_id=$id;
				
			}
			///
			/*$sds = UploadedFile::getInstance($file, 'filename');
			//for file attachment
			if (!empty($sds) && $sds !== 0) {                
				$sds->saveAs('uploads/message/' . $file->chat_data.'.'.$sds->extension);
				$file->filename ='uploads/message/'.$file->chat_data.'.'.$sds->extension;
				
				//$this->Saveattachment($file->filename,$chat->contact_id);
			} */
			///////////////////////
			if($chat->save()){
				return $this->asJson([
					'success' => true,
					'message' => 'Message Sent',
				]); 
			}
			else{
				return $this->asJson([
					'success' => false,
					'message' => 'Message Failed',
				]); 
			}
	   }
        
    }
	
	public function actionGetuser(){
        $my_var = Profile::find()->all();
        return $this->asJson(
            $my_var
        );
    }
	
	public function actionGetcontact(){
       $my_var = \Yii::$app->request->post();
	   if(!$my_var){
		return $this->asJson([
			'success' => false,
			'message' => 'POST empty',
		]); 
	   }
	   
		$userid=$my_var['userid'];
		$recipientid=$my_var['recipientid'];
        $type=$my_var['type'];
		$id="";  
		if ($type == 1) { //Personnal messages
			$arr = [$userid,$recipientid];
			sort($arr);
			$str = implode(",", $arr); 
			
			$contact = Contacts::find()->where(['user_id'=>$str])->one();
			
			 
			if (!$contact){
			
				$convo= new Contacts();
				$convo->user_id=$str;
				$convo->save(false);
				$id=$convo->contact_id;
			}else{
				$id=$contact->contact_id;
			}
			
			$chat=$this->Getpersonalchat($id);
			$profile=$this->GetProfile($recipientid);	
		}
		if ($type == 2) { //Group Messages
		    $id=$recipientid;
			$chat=$this->Getgroupchat($id);
			$profile=$this->Getgrouprofile($recipientid);
		}
		
		return $this->asJson(
           [
			   'chat'=> $chat,
			   'profile'=> $profile,
			   'id'=> $id
		   ]
        );
    }
	public function Getgroupchat($id){
		
	  $my_var = \Yii::$app->request->post();
	  $chat = Chat::find()->where(['group_id'=>$id])->all();
	  return $chat;
	}
	
	public function Getgrouprofile($id){
        $profile = ChatGroup::find()->where(['chat_group_id'=>$id])->one();
        return $profile;
    }
	
	public function Getpersonalchat($contactid){
		
	  $my_var = \Yii::$app->request->post();
	  $chat = Chat::find()->where(['contact_id'=>$contactid])->all();
	  return $chat;
	}
	
	public function GetProfile($user_id){
        $profile = Profile::find()->where(['user_id'=>$user_id])->one();
        return $profile;
    }
	
	public function actionSavefile(){
       /*
	   $file=$my_var['sender_userid'];
	   $sds = UploadedFile::getInstance($file);
			//for file attachment
			$filename="Sample";
		if (!empty($sds) && $sds !== 0) {                
			$sds->saveAs('uploads/message/'.$filename.'.'.$sds->extension);
			$file->filename ='uploads/message/'.$filename.'.'.$sds->extension;
			
			
			//$this->Saveattachment($file->filename,$chat->contact_id);
		} */
        return $this->asJson(
           ['message' => 'ok']
        );
    }
	
	public function actionGetgroup($userid){
        $group = GroupMember::find()
		//->select('tbl_chat_group.group_name')
		->joinWith('chatGroup')
		->where('tbl_chat_group.chat_group_id =tbl_group_member.chat_group_id')
		->andWhere(['user_id'=>$userid])
		->asArray()->all();
        return $this->asJson(
            $group
        );
    }
	

}
