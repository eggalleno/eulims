<?php

namespace frontend\modules\api\controllers;

use common\models\referral\Lab;
use common\models\referral\Discount;
use common\models\referral\Purpose;
use common\models\referral\Modeofrelease;
use common\models\referral\Sampletype;
use common\models\referral\Sampletypetestname;
use common\models\referral\Testnamemethod;
use common\models\referral\Testname;
use common\models\referral\Methodreference;
use common\models\referral\Packagelist;
use common\models\referral\Agency;
use common\models\referral\Notification;
use common\models\referral\Bidnotification;
use common\models\referral\Referral;
use common\models\referral\Sample;
use common\models\referral\Analysis;
use yii\db\Query;

class RestreferralController extends \yii\rest\Controller
{
	public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            'except' => ['*'],
            'user'=> \Yii::$app->referralaccount
        ];

        return $behaviors;
    }

    protected function verbs(){
        return [
            // 'login' => ['POST'],
            // 'user' => ['GET'],
        ];
    }

    public function actionIndex(){
        return "Index";
    }

    public function actionLabs(){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Lab::find()->all();
        return $data;
    }

    public function actionDiscounts(){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Discount::find()->all();
        return $data;
    }

    public function actionPurposes(){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Purpose::find()->all();
        return $data;
    }

    public function actionModesrelease(){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Modeofrelease::find()->all();
        return $data;
    }

    public function actionGetdiscount($discountid){
        $discount= Discount::find()->where(['discount_id'=>$discountid])->one();
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        return $discount;
    }

    public function actionGetcustomer($customer_id){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $customer = Customer::findOne($customer_id);
        return $customer;
    }

    //returns a list of rstl ids of the agencies     
    public function actionListmatchagency($rstl_id,$lab_id,$methodref_id,$package_id){ 
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        //search the testnamemethod record
        $model = Methodreference::find()->select(['sync_id'])->where(['methodreference_id'=>explode(',',$methodref_id)])->all();
        //returns list of sync ids example 11-5 where 11 is an rstl_id 

        if(count($model)==1)
            return $model->sync_id.","; //returns the only rstl id appended comma in the last

        $agency_ids = implode(',', array_map(function ($data) {
            //explodes the restl id from the pk of the record
            $rstl_id = explode('-', $data['sync_id']);
            return $rstl_id[0];
        }, $model));

        return $agency_ids;
    }

    public function actionListagency($agency_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        $data = Agency::find()
                    ->where([
                        'agency_id' =>array_map('intval', explode(',', $agency_id)),
                    ])
                    ->orderBy('agency_id')
                    ->asArray()
                    ->all();

        return $data;
    }

    public function actionShowupload($referral_id,$rstl_id,$type){
        return null;
    }

    public function actionReferred_agency($referral_id,$rstl_id){
        return null;
    }

    public function actionBidderagency($request_id,$rstl_id){
        return null;
    }

    public function actionBidnotice($request_id,$rstl_id){
        return null;
    }

    //gets the sampletype via lab id
    public function actionSampletypebylab($lab_id){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Sampletype::find()
            ->joinWith('labsampletypes')
            ->where(['tbl_labsampletype.lab_id' => $lab_id])
            ->asArray()
            ->all();

        return $data;
    }

    /**
     * Lists data sampletype_testname by sampletype_id
     * @return mixed
     */

    public function actionTestnamebysampletypeids($sampletype_ids){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $sampletypeId = rtrim($sampletype_ids);

        $data = Sampletypetestname::find()
            ->select(['tbl_sampletypetestname.testname_id','test_name'=>'tbl_testname.test_name'])
            ->joinWith('testname')
            ->where(['sampletype_id' => explode(',', $sampletypeId)])
            ->groupBy('tbl_testname.testname_id')
            ->orderBy('tbl_sampletypetestname.testname_id')
            ->asArray()
            ->all();

        return $data;
    }

    public function actionTestnamemethodref($testname_id){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data= Testnamemethod::find()
            ->joinWith(['testname','methodreference'])
            ->where('tbl_testname_method.testname_id = :testnameId', [':testnameId' => $testname_id])
            ->asArray()
            ->all();
        return $data;
    }

    public function actionTestnameone($testname_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Testname::find()
            ->where('testname_id =:testnameId', [':testnameId'=>$testname_id])
            ->asArray()
            ->one();
        return $data;
    }

    public function actionMethodreferenceone($methodref_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Methodreference::find()
            ->where('methodreference_id =:method_ref_id', [':method_ref_id'=>$methodref_id])
            ->asArray()
            ->one();

    return $data;   
    }

     public function actionAgencymethodreferenceone($methodref_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Methodreference::find()
            ->where('methodreference_id =:method_ref_id', [':method_ref_id'=>$methodref_id])
            ->one();
        if(!$data) //if no reference returns null array
            return [];

        $agency = explode('-', $data->sync_id);

        if(!$agency[0]) //if no value returns null
            return [];

        $agen= Agency::findOne($agency[0]);

        return $agen;   
    }

    public function actionDiscountbyid($discount_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Discount::find()->where(['discount_id'=>$discount_id,'status'=>1])->one();        
        return $data;
    }

    public function actionListpackage($lab_id,$sampletype_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $data = Packagelist::find()
            ->where(['lab_id'=>$lab_id,'sampletype_id'=> explode(',', $sampletype_id) ])
            ->all();
        return $data;
    }

    public function actionPackage_detail($package_id){

        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        $package = Packagelist::find()->where(['package_id'=>$package_id])->one();

        if(!empty($package->test_methods)){
            $testmethods = explode(",",$package->test_methods);
            $testmethod_data = [];
            foreach ($testmethods as $testmethodId){
                $testmethodrefs = Testnamemethod::findOne($testmethodId);
                $listTestmethods = [
                    'testname_method_id'=>$testmethodrefs->testname_method_id,
                    'testname_id'=>$testmethodrefs->testname_id,
                    'testname'=>$testmethodrefs->testname->test_name,
                    'methodreference_id'=>$testmethodrefs->methodreference_id,
                    'method'=>$testmethodrefs->methodreference->method,
                    'reference'=>$testmethodrefs->methodreference->reference,
                ];
                array_push($testmethod_data, $listTestmethods);
            }
            return ['testmethod_data'=>$testmethod_data,'package'=>$package];
        } else {
            return false;
        }
    }
    //expecting to return a boolean value
    public function actionCheckactivelab($lab_id,$agency_id){
         \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
         return 1; //returns boolean better return it true but the client checks if 1 or not 1
    }

    //expecting to return 0 = request_id invalid request id, 1 = already notified, 2 = not yet modified
    public function actionchecknotify($request_id,$agency_id){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        //query the notification table join with referral, use the agencyid as recipient id and request id as local request id, and notify value to 1

        //if there is a record found return 1 else return 2, 0 if the request is not valid or 0

        return 2;
    }

    //expecting to return a boolean value
    public function actionCheckConfirm($request_id,$receiving_id,$testing_id){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        $model = Notification::find()
                        ->select('tbl_notification.*')
                        //->where('notification_id =:notifiedId', [':notifiedId'=>$notifiedId])
                        ->joinWith('referral')
                        ->where('local_request_id =:localrequestId', [':localrequestId'=>$requestId])
                        ->andWhere('sender_id =:senderId', [':senderId'=>$receivingId])
                        ->andWhere('recipient_id =:recipientId', [':recipientId'=>$testingId])
                        ->andWhere('notification_type_id =:notify',[':notify'=>1])
                        ->andWhere('responded =:responded',[':responded'=>1])
                        ->count();

        $confirmed = $model::find()
                        ->select('tbl_notification.*')
                        ->joinWith('referral')
                        ->where('local_request_id =:localrequestId', [':localrequestId'=>$requestId])
                        ->andWhere('sender_id =:senderId', [':senderId'=>$testingId])
                        ->andWhere('recipient_id =:recipientId', [':recipientId'=>$receivingId])
                        ->andWhere('notification_type_id =:confirm',[':confirm'=>2])
                        ->count();

        if($notified > 0 && $confirmed > 0){
            return 1;
        } else {
            return 0;
        }
    }

    //return estimated due date
    public function actionShowdue($request_id,$rstl_id,$sender_id)
    {
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        try {
            $showdue = Notification::find()
                ->select('notification_id, tbl_notification.remarks as estimated_due')
                ->joinWith(['referral'],false)
                ->where('local_request_id =:localrequestId AND receiving_agency_id=:receivingAgency', [':localrequestId'=>$request_id,':receivingAgency'=>$rstl_id])
                ->andWhere('sender_id =:sender AND notification_type_id =:notificationType', [':sender'=>$sender_id,':notificationType'=>2])
                ->asArray()->one();
            
            if($showdue){
                return $showdue['estimated_due'];
            } else {
                return 0;
            }
            
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Internal server error');
        }

    }

    //returns 2 responses , response and referral_id
    public function actionInsertreferraldata()
    {
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        $return = 0;
        $referralId = 0;
        $referralSave = 0;
        $sampleSave = 0;
        $analysisSave = 0;

        if(count(\Yii::$app->request->post()) > 0){
            //be safe patricia!!, its a life saver
            $connection= \Yii::$app->referraldb;
            $connection->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            $transaction = $connection->beginTransaction();

            $request = \Yii::$app->request->post('request_data');
            $samples = \Yii::$app->request->post('sample_data');
            $analyses = \Yii::$app->request->post('analysis_data');
            //checks if the referral in the centralized server is already existing
            $checkReferral = $this->checkReferral($request['request_id'],$request['rstl_id']);

            if ($checkReferral>0) {
                #referral is already existing
                //therefore passing it to the referralID variable
                $referralId = $checkReferral;
                $return = 0;
                return ['response'=>$return,'referral_id'=>$referralId];
            } else{
                if(count($request)>0){
                    $modelReferral = new Referral;
                    $modelReferral->referral_date_time = $request['request_datetime'];
                    $modelReferral->referralDate = date('Y-m-d',strtotime($request['request_datetime']));
                    $modelReferral->referralTime = date("h:i:sa",strtotime($request['request_datetime']));
                    $modelReferral->local_request_id = $request['request_id'];
                    $modelReferral->receiving_agency_id = $request['rstl_id'];
                    $modelReferral->testing_agency_id = 0;
                    $modelReferral->lab_id = $request['lab_id'];
                    $modelReferral->sample_received_date = $request['sample_received_date'];
                    $modelReferral->customer_id = $request['customer_id'];
                    $modelReferral->payment_type_id = $request['payment_type_id'];
                    $modelReferral->modeofrelease_id = $request['modeofrelease_ids'];
                    $modelReferral->purpose_id = $request['purpose_id'];
                    $modelReferral->discount_id = $request['discount_id'];
                    $modelReferral->discount_rate = $request['discount'];
                    $modelReferral->total_fee = $request['total'];
                    $modelReferral->report_due = $request['report_due'];
                    $modelReferral->conforme = $request['conforme'];
                    $modelReferral->receiving_user_id = $request['user_id_receiving'];
                    $modelReferral->cro_receiving = $request['receivedBy'];
                    $modelReferral->created_at_local = date('Y-m-d H:i:s',$request['created_at']);
                    $modelReferral->create_time = date('Y-m-d H:i:s');
                    $modelReferral->update_time = date('Y-m-d H:i:s');
                    $modelReferral->bid = $request['bid'];
                    if($modelReferral->save(false)){
                        $referralId = $modelReferral->referral_id;
                        $referralSave = 1; //flags that the request has been save in this transaction
                        //iterates the sample and save them
                        if(count($samples) > 0){
                            foreach ($samples as $sample) {
                                $modelSample = new Sample;
                                $modelSample->referral_id = $modelReferral->referral_id;
                                $modelSample->local_sample_id = $sample['sample_id'];
                                $modelSample->local_request_id = $sample['request_id'];
                                $modelSample->receiving_agency_id = $sample['rstl_id'];
                                //$modelSample->package_id = $sample['package_id'];
                                $modelSample->sample_type_id = $sample['sampletype_id'];
                                $modelSample->sample_code = $sample['sample_code'];
                                $modelSample->sample_name = $sample['samplename'];
                                $modelSample->description = $sample['description'];
                                $modelSample->customer_description = $sample['customer_description'];
                                $modelSample->sampling_date = $sample['sampling_date'];
                                $modelSample->remarks = $sample['remarks'];
                                $modelSample->sample_month = $sample['sample_month'];
                                $modelSample->sample_year = $sample['sample_year'];
                                $modelSample->active = $sample['active'];
                                $modelSample->created_at = date('Y-m-d H:i:s');
                                $modelSample->updated_at = date('Y-m-d H:i:s');
                                if($modelSample->save(false)){
                                    $sampleSave = 1; //flags that 
                                    //now iterates the analyses

                                    foreach ($analyses as $analysis) {
                                        $modelAnalysis = new Analysis;
                                        $modelAnalysis->sample_id = $modelSample->sample_id;
                                        $modelAnalysis->local_analysis_id = $analysis['analysis_id'];
                                        $modelAnalysis->local_sample_id = $analysis['sample_id'];
                                        $modelAnalysis->date_analysis = $analysis['date_analysis'];
                                        $modelAnalysis->agency_id = $analysis['rstl_id'];
                                        $modelAnalysis->package_id = $analysis['package_id'];
                                        $modelAnalysis->testname_id = $analysis['test_id'];
                                        $modelAnalysis->methodreference_id = $analysis['methodref_id'];
                                        $modelAnalysis->analysis_fee = $analysis['fee'];
                                        $modelAnalysis->cancelled = $analysis['cancelled'];
                                        $modelAnalysis->status = 1;
                                        $modelAnalysis->is_package = $analysis['is_package'];
                                        $modelAnalysis->type_fee_id = $analysis['type_fee_id'];
                                        $modelAnalysis->created_at = date('Y-m-d H:i:s');
                                        $modelAnalysis->updated_at = date('Y-m-d H:i:s');

                                        if($modelAnalysis->save()){
                                            $analysisSave = 1;
                                        }else{
                                            $analysisSave = 0;
                                            $transaction->rollBack();
                                            break;
                                        }
                                    }
                                    //checks if the analysis was not save therefore breaks this loop also
                                    // if($analysisSave = 0)
                                    //     break;

                                }else{
                                    $sampleSave = 0; //flags the a sample was not save therefore rollsback
                                    //else for the if sample was save
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        //esle for "if request was saved"
                        $transaction->rollBack();
                    }
                }
                
            }

            // return ['referral'=>$referralSave,'sample'=>$sampleSave,'analysis'=>$analysisSave];

            //after all of the procedure above, determines waether to send notification or not
            if($referralSave == 1 && $sampleSave == 1 && $analysisSave == 1){
                $transaction->commit();
                $return = 1;

            } else {
                $transaction->rollBack();
                $return = 0;
                $referralId =0;
            }
        }
        

        return ['response'=>$return,'referral_id'=>$referralId];
    }

    //returns boolean, notifies the referred agency about the referral    
    public function actionNotify(){
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        if(count(\Yii::$app->request->post('notice_details')) > 0){
            $details = \Yii::$app->request->post('notice_details');

            $notification = new Notification;
            $notification->referral_id = (int) $details['referral_id'];
            $notification->notification_type_id = 1;
            $notification->sender_id = (int) $details['sender_id'];
            $notification->recipient_id = (int) $details['recipient_id'];
            $notification->sender_user_id = (int) $details['sender_user_id'];
            $notification->sender_name = $details['sender_name'];
            $notification->remarks = $details['remarks'];
            $notification->notification_date = date('Y-m-d H:i:s');
            if($notification->save(false)){
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    //returns number of notification
    public function actionCountnotification($rstl_id)
    {
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;

        if($rstl_id > 0){
            $model = new Notification;
            try {
                $query = $model::find()
                    ->where('recipient_id =:recipientId', [':recipientId'=>$rstl_id])
                    ->andWhere('responded =:responded',[':responded'=>0]);

                $notificationCount = $query->count();
                $notification = $query->orderBy('notification_date DESC')->all();

                if($notificationCount > 0){
                    return ['notification'=>$notification,'count_notification'=>$notificationCount];
                } else {
                    return ['notification'=>null,'count_notification'=>0];
                }
                
            } catch (Exception $e) {
                throw new \yii\web\HttpException(500, 'Internal server error');
            }
        } else {
            throw new \yii\web\HttpException(400, 'No records found');
        }
    }

    public function actionCountbidnotification($rstl_id)
    {
        \Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
        if($rstl_id > 0){
            $model = new Bidnotification;
            try {
                $query = $model::find()
                    ->where('recipient_agency_id =:recipientId', [':recipientId'=>$rstl_id])
                    ->andWhere('seen =:seen',[':seen'=>0]);
                
                $bidnotificationCount = $query->count();
                $bidnotification = $query->orderBy('posted_at DESC')->all();
                
                if($bidnotificationCount > 0){
                    return ['bidnotification'=>$bidnotification,'count_bidnotification'=>$bidnotificationCount];
                } else {
                    return ['bidnotification'=>null,'count_bidnotification'=>0];
                }
                
            } catch (Exception $e) {
                throw new \yii\web\HttpException(500, 'Internal server error');
            }
        } else {
            throw new \yii\web\HttpException(400, 'No records found');
        }
    }

    //protecetd function was salvaged from STG
    protected function checkReferral($requestId,$rstlId)
    {
        $model = new Referral;
        
        if($requestId > 0 && $rstlId > 0){
            $referral = $model::find()
            ->select('referral_id')
            ->where('local_request_id =:localrequestId', [':localrequestId'=>$requestId])
            ->andWhere('receiving_agency_id =:rstlId', [':rstlId'=>$rstlId])
            ->one();
            
            if(count($referral)>0){
                $referralId = $referral->referral_id;
            } else {
                $referralId = 0;
            }
        } else {
            $referralId = 0;
        }
        
        return $referralId;
    }
}