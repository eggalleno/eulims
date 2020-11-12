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
        $post= \Yii::$app->request->post();
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
        $agencyId = rtrim($_GET['agency_id']);
        $data = Agency::find()
                    ->where([
                        'agency_id' => explode(',', $agencyId),
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
}