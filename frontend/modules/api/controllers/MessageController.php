<?php

namespace frontend\modules\api\controllers;

use Yii;

use common\models\system\LoginForm;
use common\models\system\Profile;
use common\models\system\User;
use common\models\auth\AuthAssignment;
use common\models\system\Rstl;

use common\models\lab\Customer;
use yii\helpers\Json;

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
            'except' => ['login', 'server','synccustomer'],
            //'user'=> [\Yii::$app->referralaccount]


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
            ->setExpiration(time() + 3600 * 2400000)// Configures the expiration time of the token (exp claim)
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
			$dataxtype=$my_var['dataxtype'];
			$chat->status_id=1;//sent
			$chat->chat_data_type=$type; //message text or file
			$chat->message_type=$dataxtype; //personnel message or group
			//tbl_contact
			if($type == 1){
				$chat->contact_id=$id;
			}else{
				$chat->group_id=$id;
				
			}
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
		
	 //$my_var = \Yii::$app->request->post();
	 // $chat = Chat::find()->where(['group_id'=>$id])->all();
	 // return $chat; 
	  //$chat=SELECT * FROM `eulims_message`.`tbl_chat` INNER JOIN `eulims`.`tbl_profile` ON `eulims_message`.`tbl_chat`.`sender_userid`=`eulims`.`tbl_profile`.`user_id` WHERE group_id=4
	 // $chat= Chat::find()->with('profile', true, 'INNER JOIN')->all();
	 $chat = Chat::find()
	->select(['tbl_profile.fullname','chat_id', 'sender_userid' => 'tbl_profile.fullname', 'timestamp', 'status_id', 'group_id', 'contact_id'])
	->where(['group_id'=>4])
	->leftJoin('eulims.tbl_profile', 'eulims.tbl_profile.user_id=eulims_message.tbl_chat.sender_userid')
	->all();
	 // $chat= Chat::find()->joinWith('profile')->where('group_id'=>4)->all();
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
		$valid_extensions = ['jpeg', 'jpg', 'png', 'gif', 'bmp' , 'pdf' , 'doc' , 'ppt','docx','xlsx', 'pptx']; 
		$path = 'uploads/message/'; // upload directory

		$img = $_FILES['filetoupload']['name'];
        $tmp = $_FILES['filetoupload']['tmp_name'];
	
		$ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
		// can upload same image using rand function
		$final_image = rand(1000,1000000).$img;
		// check's valid format
		if(in_array($ext, $valid_extensions)) 
		{ 
			$path = $path.strtolower($final_image); 
			if(move_uploaded_file($tmp,$path)) 
			{
				
			}
		} 
		
		return $this->asJson(
			['message' => 'success',
			'filename'=>$final_image
			]
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
    public function actionDownloadfile(){
		// Initialize a file URL to the variable 
			
		$file = 'uploads/message/123456.png';
		header("Content-Type: image/png");
		header("Content-Disposition: attachment; filename=file name here");
		header("Cache-Control: no-cache");
		fopen($file,"w+");

	}
	/*public function actionProfile(){
		$my_var = \Yii::$app->request->post();
		$id=$my_var['id'];
        $profile = Profile::find()->where(['user_id'=>$id])->one();
        return $profile;
    }*/

    public function actionSynccustomer(){ 
     
        
            // $post = Yii::$app->request->post();
            
            // if(isset($post)){
            //     //$myvar = Json::decode($post['data']);
            //     //use this email to track if the user record already exist in ulimsportal or in any other cloud storage
            //     //lets check the record if it exist

                $toreturn = false;
                $model = Customer::find()->where(['email'=>Yii::$app->request->post('email')])->one();

                if($model){
                    // email already exist proceed to confirmation
                    $toreturn=2; //this mean that the record's email already exist and that the client accessing the API will know what to do -> compare the records for confirmation
                }else{
                   
                    $newmodel = new Customer;
                    $newmodel->rstl_id = Yii::$app->request->post('rstl_id');
                    $newmodel->customer_name = Yii::$app->request->post('customer_name');
                    $newmodel->classification_id = Yii::$app->request->post('classification_id');
                    $newmodel->latitude = Yii::$app->request->post('latitude');
                    $newmodel->longitude = Yii::$app->request->post('longitude');
                    $newmodel->head = Yii::$app->request->post('head');
                    $newmodel->barangay_id = Yii::$app->request->post('barangay_id');
                    $newmodel->address = Yii::$app->request->post('address');
                    $newmodel->tel = Yii::$app->request->post('tel');
                    $newmodel->fax = Yii::$app->request->post('fax');
                    $newmodel->email = Yii::$app->request->post('email');
                    $newmodel->customer_type_id = Yii::$app->request->post('customer_type_id');
                    $newmodel->business_nature_id = Yii::$app->request->post('business_nature_id');
                    $newmodel->industrytype_id = Yii::$app->request->post('industrytype_id');
                    $newmodel->is_sync_up = Yii::$app->request->post('is_sync_up');
                    $newmodel->is_updated = Yii::$app->request->post('is_updated');
                    $newmodel->is_deleted = Yii::$app->request->post('is_deleted');
                    if($newmodel->save()){
                        $newmodel->customer_code = $newmodel->rstl_id."-".$newmodel->customer_id;
                        $newmodel->save(false);
                        $toreturn = $newmodel->customer_code;
                    }
                }
                
                return $this->asJson(
                    $toreturn
                );
        
    }
}
