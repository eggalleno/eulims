<?php

namespace frontend\modules\pstc\controllers;

use Yii;
use common\models\referral\Pstcrequest;
use common\models\referral\Pstcsample;
use common\models\lab\Request;
use common\models\lab\Sample;
use common\models\lab\Analysis;
use common\models\lab\Customer;
use common\models\lab\Requestcode;
use common\models\lab\Discount;
use common\models\lab\Labsampletype;
use common\models\lab\Sampletype;
use common\models\lab\SampleName;
use common\models\lab\Lab;
use common\models\lab\Referralrequest;
use common\models\lab\Testcategory;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
//use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\components\PstcComponent;
use yii\db\Query;
/*** for saving pstc request details ***/
use DateTime;
use common\models\system\Profile;
use common\components\Functions;
use common\components\ReferralComponent;
use frontend\modules\lab\components\eRequest;
use common\models\lab\exRequestreferral;
use linslin\yii2\curl;
use yii\helpers\Json;
/*** end for saving pstc request details ***/

/**
 * PstcrequestController implements the CRUD actions for Pstcrequest model.
 */
class PstcrequestController extends Controller
{
  
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
  
    public function actionIndex()
    {
        $rstlId = (int) Yii::$app->user->identity->profile->rstl_id;

        if($rstlId > 0)
        {
            $pstcComponent = new PstcComponent();
            $referrals = json_decode($pstcComponent->getAll($rstlId),true);

            if((int) $referrals == 0){
                $referralDataprovider = new ArrayDataProvider([
                    'allModels' => [],
                    'pagination'=> ['pageSize' => 10],
                ]);
            } else {
                $referralDataprovider = new ArrayDataProvider([
                    'allModels' => $referrals,
                    'pagination'=> ['pageSize' => 10],
                ]);
            }

            return $this->render('index', [
                //'searchModel' => $searchModel,
                'dataProvider' => $referralDataprovider,
            ]);
        } else {
            return $this->redirect(['/site/login']);
        }
    } 

    public function actionView()
    {
        if(isset(Yii::$app->user->identity->profile->rstl_id)){
            $rstlId = (int) Yii::$app->user->identity->profile->rstl_id;
        } else {
            //return 'Session time out!';
            return $this->redirect(['/site/login']);
        }

        $requestId = (int) Yii::$app->request->get('request_id');
        $pstcId = (int) Yii::$app->request->get('pstc_id');

        if($rstlId > 0 && $requestId > 0 && $pstcId > 0)
        {
            $function = new PstcComponent();

            $details = json_decode($function->getViewRequest($requestId,$rstlId,$pstcId),true);
            // var_dump($details); exit();
            $request = $details['request_data'];
            $samples = $details['sample_data'];
            $analysis = $details['analysis_data'];
            $respond = $details['respond_data'];
            $pstc = $details['pstc_data'];
            $customer = $details['customer_data'];
            $attachment = $details['attachment_data'];
            $subtotal = $details['subtotal'];
            $discount = $details['discount'];
            $total = $details['total'];

            $sampleDataProvider = new ArrayDataProvider([
                'allModels' => $samples,
                'pagination'=>false,
            ]);

            $analysisDataprovider = new ArrayDataProvider([
                'allModels' => $analysis,
                'pagination'=>false,
            ]);

            $attachmentDataprovider = new ArrayDataProvider([
                'allModels' => $attachment,
                'pagination' => false,
            ]);

            // var_dump($request); exit();

            return $this->render('view', [
                'model' => new Request(), //just to initialize detail view
                'request' => $request,
                'customer' => $customer,
                'sample' => $samples,
                'sampleDataProvider' => $sampleDataProvider,
                'analysisDataprovider'=> $analysisDataprovider,
                'attachmentDataprovider'=> $attachmentDataprovider,
                'respond' => $respond,
                'pstc' => $pstc,
                'subtotal' => $subtotal,
                'discounted' => $discount,
                'total' => $total,
                'countSample' => count($samples),
                'countAnalysis' => count($analysis),
            ]);
        }else{
            Yii::$app->session->setFlash('error', "Invalid request!");
            return $this->redirect(['/pstc/pstcrequest/accepted']);
        }
    }

    public function actionCreate()
    {
        $model = new Pstcrequest;
        $rstl_id = (int) Yii::$app->user->identity->profile->rstl_id;
        if($rstl_id > 0){
            //$model->rstl_id = $rstlId;
            //$model->user_id = (int) Yii::$app->user->identity->profile->user_id;
            $mi = !empty(Yii::$app->user->identity->profile->middleinitial) ? " ".substr(Yii::$app->user->identity->profile->middleinitial, 0, 1).". " : " ";
            $user_fullname = Yii::$app->user->identity->profile->firstname.$mi.Yii::$app->user->identity->profile->lastname;

            $customers = $this->listCustomers($rstl_id);
            
            if($user_fullname){
                $model->received_by = $user_fullname;
            } else {
                $model->received_by = "";
            }
        } else {
            return $this->redirect(['/site/login']);
        }

        if(Yii::$app->request->post()) 
        {
            $post = Yii::$app->request->post('Pstcrequest');

            $testarray = [
                'rstl_id' => $rstl_id,
                'customer_id' => (int) $post['customer_id'],
                'user_id' => (int) Yii::$app->user->identity->profile->user_id,
                'submitted' => $post['submitted_by'],
                'received' => $user_fullname,
            ];

            $function = new PstcComponent();
            $data = json_decode($function->getRequestcreate($testarray),true);
            // var_dump($data); exit();
            if($data){
                Yii::$app->session->setFlash('success', 'Request successfully saved!');
                return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$data['pstc_request_id'],'pstc_id'=>$data['pstc_id']]);
            }else{
                Yii::$app->session->setFlash('error', 'Failed!');
                return $this->redirect(['/lab/request/view','request_id'=>$data['pstc_request_id'],'pstc_id' => $data['pstc_id']]);
            }
        }else{
            if(\Yii::$app->request->isAjax){
                return $this->renderAjax('create', [
                    'model' => $model,
                    'customers' => $customers,
                ]);
            }else{
                return $this->renderAjax('create', [
                    'model' => $model,
                    'customers' => $customers,
                ]);
            }
        }
    }
    
    public function actionCreatesample()
    {
        $model = new Pstcsample;

        if(isset($_POST['qnty']))
        {
            $post = Yii::$app->request->post('Pstcsample');
            $testarray = [
                'qnty' => $_POST['qnty'],
                'pstc_request_id' => (int) $_POST['pstc_request_id'],
                'sample_name' => $post['sample_name'],
                'sample_description' => $post['sample_description'],
            ];
            // var_dump($testarray); exit();   
            $function = new PstcComponent();
            $data = json_decode($function->getSamplecreate($testarray),true);
            
            if($data){
                Yii::$app->session->setFlash('success', 'Request successfully saved!');
                return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$data['pstc_request_id'],'pstc_id'=>$data['pstc_id']]);
            }else{
                Yii::$app->session->setFlash('error', 'Failed!');
                return $this->redirect(['/lab/request/view','request_id'=>$data['pstc_request_id'],'pstc_id' => $data['pstc_id']]);
            }
        }

        return $this->renderAjax('createsample', [
            'model' => $model,
            'request_id' => (int) Yii::$app->request->get('request_id'),
            'sampletemplates' =>$this->listSampletemplate(),
        ]);
    }

    public function actionGetlisttemplate() {
        if(isset($_GET['template_id'])){
            $id = (int) $_GET['template_id'];
            $modelSampletemplate =  SampleName::findOne(['sample_name_id'=>$id]);
            if(count($modelSampletemplate)>0){
                $sampleName = $modelSampletemplate->sample_name;
                $sampleDescription = $modelSampletemplate->description;
            } else {
                $sampleName = "";
                $sampleDescription = "";
            }
        } else {
            $sampleName = "Error getting sample name";
            $sampleDescription = "Error getting description";
        }
        return Json::encode([
            'name'=>$sampleName,
            'description'=>$sampleDescription,
        ]);
    }


    public function actionSavetolocal()
    {
        $pstc = Pstcrequest::findOne(Yii::$app->request->post('request_id'));
        $post= Yii::$app->request->post('eRequest');
    
        $model = new Request;
        $model->request_datetime = $post['request_datetime'];
        $model->rstl_id = Yii::$app->user->identity->profile->rstl_id;
        $model->customer_id = $pstc->customer_id;
        $model->lab_id = $post['lab_id'];
        $model->payment_type_id = 1;
        $model->discount_id = $post['discount_id'];
        $model->purpose_id = $post['purpose_id'];
        $model->modeofrelease_ids =1;
        $model->total = $post['lab_id'];
        $model->conforme = $post['conforme'];
        $model->receivedBy= $post['receivedBy'];
        $model->request_type_id = 3;
        $model->report_due = $post['report_due'];
       
        if($model->save(false))
        {
            foreach($pstc->samples as $sampol) 
            {
                $sample = new Sample;
                $sample->request_id = $model->request_id;
                $sample->rstl_id = Yii::$app->user->identity->profile->rstl_id;
                $sample->sampletype_id = $sampol['sampletype_id'];
                $sample->samplename = $sampol['sample_name'];
                $sample->description = $sampol['sample_description'];
                $sample->sampling_date = date('Y-m-d H:i:s');
                $sample->sample_month = date_format(date_create($model->request_datetime),'m');
                $sample->sample_year = date_format(date_create($model->request_datetime),'Y');
                
                if($sample->save(false)){
                    $pstc->accepted = 1;
                    $pstc->save();

                    Yii::$app->session->setFlash('success', 'Request Saved to local!');
                    return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$pstc['pstc_request_id'],'pstc_id'=>$pstc['pstc_id']]);
                }else{

                }
            }
        }
    }


    public function actionRequest_local()
    {
        $model = new eRequest();
        $connection= Yii::$app->labdb;
        $connection->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
        $transaction = $connection->beginTransaction();
        
        if(isset(Yii::$app->user->identity->profile->rstl_id)){
            $rstlId = (int) Yii::$app->user->identity->profile->rstl_id;
        } else {
            //return 'Session time out!';
            return $this->redirect(['/site/login']);
        }

        $requestId = (int) Yii::$app->request->get('request_id');
        $pstcId = (int) Yii::$app->request->get('pstc_id');
        $function = new PstcComponent();

        if($rstlId > 0 && $pstcId > 0 && $requestId > 0) {
            $details = json_decode($function->getViewRequest($requestId,$rstlId,$pstcId),true);

            $request = $details['request_data'];
            $samples = $details['sample_data'];
            $analyses = $details['analysis_data'];
            $respond = $details['respond_data'];
            $pstc = $details['pstc_data'];
            $customer = $details['customer_data'];
            $subtotal = $details['subtotal'];
            $discount = $details['discount'];
            $total = $details['total'];

            $sampleDataProvider = new ArrayDataProvider([
                'allModels' => $samples,
                'pagination'=>false,
            ]);

            $analysisDataprovider = new ArrayDataProvider([
                'allModels' => $analyses,
                'pagination'=>false,
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Invalid request!');
            return $this->redirect(['/pstc/pstcrequest']);
        }

        //if (Yii::$app->request->post()) {
        //if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if ($model->load(Yii::$app->request->post()) && count($analyses) >= count($samples)) {
            $post = Yii::$app->request->post('eRequest');
            $total_fee = $total - ($subtotal * ($post['discount']/100));

            $model->modeofrelease_ids = implode(",", $post['modeofreleaseids']);
            $model->total = $total_fee;
            $model->pstc_request_id = $requestId;
            $model->request_datetime = date('Y-m-d H:i:s');
            $model->created_at = date('Y-m-d H:i:s');
            $model->rstl_id = $rstlId;
            $model->customer_id = $request['customer_id'];
            $model->pstc_id = $pstcId;

            $sampleSave = 0;
            $analysisSave = 0;
			$requestSave = 0;
            if($model->save(false)) {
                $local_requestId = $model->request_id;
                $psct_requestdetails = json_decode($function->getRequestDetails($requestId,$rstlId,$pstcId),true);

                $samples_analyses = $psct_requestdetails['sample_analysis_data'];
                $customer = $psct_requestdetails['customer_data'];

                foreach($samples_analyses as $sample) {
                    $modelSample = new Sample();

                    $modelSample->request_id = $local_requestId;
                    $modelSample->rstl_id = $rstlId;
                    $modelSample->sample_month = date_format(date_create($model->request_datetime),'m');
                    $modelSample->sample_year = date_format(date_create($model->request_datetime),'Y');
                    //$modelSample->testcategory_id = 0; //pstc request, test category id is in analysis
                    //$modelSample->sampletype_id = 0; //pstc request, sample type id is in analysis
                    $modelSample->samplename = $sample['sample_name'];
                    $modelSample->description = $sample['sample_description'];
                    $modelSample->customer_description = $sample['customer_description'];
                    // $modelSample->sampling_date = $sample['sampling_date']; //to be updated
                    $modelSample->sampling_date = empty($sample['sampling_date']) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime($sample['sampling_date'])); //to be updated
                    $modelSample->pstcsample_id = $sample['pstc_sample_id'];
                    //$modelSample->testcategory_id = $post['testcategory_id'];
                    //$modelSample->sampletype_id = $post['sampletype_id'];
                    $modelSample->testcategory_id = $sample['testcategory_id'];
                    $modelSample->sampletype_id = $sample['sampletype_id'];

                    if($modelSample->save(false)) {
                        foreach ($sample['analyses'] as $analysis) {
                            $modelAnalysis = new Analysis();
                            $modelAnalysis->rstl_id = $rstlId;
                            $modelAnalysis->date_analysis = date('Y-m-d');
                            $modelAnalysis->request_id = $local_requestId;
                            $modelAnalysis->pstcanalysis_id = $analysis['pstc_analysis_id'];
                            $modelAnalysis->package_id =  $analysis['package_id'];
                            $modelAnalysis->package_name =  $analysis['package_name'];
                            $modelAnalysis->sample_id = $modelSample->sample_id;
                            $modelAnalysis->test_id = $analysis['testname_id'];
                            $modelAnalysis->testname = $analysis['testname'];
                            $modelAnalysis->methodref_id = $analysis['method_id'];
                            $modelAnalysis->method = $analysis['method'];
                            $modelAnalysis->references = $analysis['reference'];
                            $modelAnalysis->fee = $analysis['fee'];
                            $modelAnalysis->testcategory_id = $analysis['testcategory_id'];
                            $modelAnalysis->sample_type_id = $analysis['sampletype_id'];
                            $modelAnalysis->is_package = $analysis['is_package'];
                            $modelAnalysis->is_package_name = $analysis['is_package_name'];
                            $modelAnalysis->quantity = 1;
                            if($modelAnalysis->save(false)) {
                                $analysisSave = 1;
                            } else {
                                $transaction->rollBack();
                                $analysisSave = 0;
                            }
                        }
                        $sampleSave = 1;
                    } else {
                        $transaction->rollBack();
                        $sampleSave = 0;
                    }
                }

                $requestSave = 1;

                if($sampleSave == 1 && $analysisSave == 1 && $requestSave == 1) {
                    //$transaction->commit();
                    //if($transaction->commit()) {
                    //if($generate_code == 'success') {
                        $generate_code = $this->saveRequest($local_requestId,$model->lab_id,$rstlId,date('Y',strtotime($model->request_datetime)));
                        //$func = new Functions();
                        //$samplecode = $func->GenerateSampleCode($local_requestId);
                        //print_r($generate_code);
                        //exit;
                    //if($generate_code == 'success') {
                        $sample_data = [];
                        $analysis_data = [];
                        if($generate_code == "success") {
                            $local_samples = Sample::find()->where(['request_id'=>$local_requestId])->asArray()->all();
                            $local_request = Request::findOne($local_requestId);
                            $local_analyses = Analysis::find()->where(['request_id'=>$local_requestId])->asArray()->all();

                            $requestData = [
                                'pstc_request_id' => $local_request->pstc_request_id,
                                'request_ref_num' => $local_request->request_ref_num,
                                'rstl_id' => $local_request->rstl_id,
                                'pstc_id' => $local_request->pstc_id,
                                'customer_id' => $local_request->customer_id,
                                'local_request_id' => $local_request->request_id,
                                'request_date_created' => $local_request->request_datetime,
                                'estimated_due_date' => $local_request->report_due,
                                'lab_id' => $local_request->lab_id,
                                'discount_id' => (int) $local_request->discount_id,
                                'discount_rate' => $local_request->discount,
                            ];

                            foreach ($local_samples as $s_data) {
                                $sampleData = [
                                    'pstc_sample_id' => $s_data['pstcsample_id'],
                                    'sample_code' => $s_data['sample_code'],
                                    'sample_month' => $s_data['sample_month'],
                                    'sample_year' => $s_data['sample_year'],
                                    'rstl_id' => $local_request->rstl_id,
                                    'pstc_id' => $pstcId,
                                    'sample_description' => $s_data['description'],
                                    'customer_description' => $s_data['customer_description'],
                                    'sample_name' => $s_data['samplename'],
                                    'local_sample_id' => $s_data['sample_id'],
                                    'local_request_id' => $local_requestId,
                                    'sampletype_id' => $s_data['sampletype_id'],
                                    'testcategory_id' => $s_data['testcategory_id'],
                                    'sampling_date' => $s_data['sampling_date'],
                                ];
                                array_push($sample_data, $sampleData);
                            }

                            foreach ($local_analyses as $a_data) {
                                $analysisData = [
                                    'pstc_analysis_id' => $a_data['pstcanalysis_id'],
                                    //'pstc_sample_id' => $a_data['pstc_sample_id'],
                                    'local_analysis_id' => $a_data['analysis_id'],
                                    'local_sample_id' => $a_data['sample_id'],
                                    'rstl_id' => $local_request->rstl_id,
                                    'testname_id' => $a_data['test_id'],
                                    'testname' => $a_data['testname'],
                                    'method_id' => $a_data['methodref_id'],
                                    'method' => $a_data['method'],
                                    'reference' => $a_data['references'],
                                    'package_id' => $a_data['package_id'],
                                    'package_name' => $a_data['package_name'],
                                    'quantity' => $a_data['quantity'],
                                    'fee' => $a_data['fee'],
                                    'pstc_id' => $pstcId,
                                    'date_analysis' => $a_data['date_analysis'],
                                    'is_package' => $a_data['is_package'],
                                    'is_package_name' => $a_data['is_package_name'],
                                    'sampletype_id' => $a_data['sample_type_id'],
                                    'testcategory_id' => $a_data['testcategory_id'],
                                    'type_fee_id' => $a_data['type_fee_id'],
                                    'local_user_id' => (int) Yii::$app->user->identity->profile->user_id,
                                    'local_request_id' => $a_data['request_id'],
                                ];
                                array_push($analysis_data, $analysisData);
                            }

                            $pstc_request_details = Json::encode(['request_data'=>$requestData,'sample_data'=>$sample_data,'analysis_data'=>$analysis_data,'pstc_id'=>$pstcId,'rstl_id'=>$rstlId],JSON_NUMERIC_CHECK);
                            //$pstcUrl='https://eulimsapi.onelab.ph/api/web/referral/pstcrequests/updaterequest_details';
                            $pstcUrl='http://localhost/eulimsapi.onelab.ph/api/web/referral/pstcrequests/updaterequest_details';
                       
                            $curl = new curl\Curl();
                            $pstc_return = $curl->setRequestBody($pstc_request_details)
                            ->setHeaders([
                                'Content-Type' => 'application/json',
                                'Content-Length' => strlen($pstc_request_details),
                            ])->post($pstcUrl);

                            if($pstc_return == 1) {
                                $transaction->commit();
                                Yii::$app->session->setFlash('success', 'Request successfully saved!');
                                return $this->redirect(['/lab/request/view','id'=>$local_requestId]);
                            } else {
                                $transaction->rollBack();
                                //return "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' style='font-size:18px;'></span>&nbsp;Request 1 failed to save!</div>";
                                Yii::$app->session->setFlash('error', 'Request failed to save!');
                                return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$requestId,'pstc_id'=>$pstcId]);
                            }
                        } else {
                            $transaction->rollBack();
                            //return "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' style='font-size:18px;'></span>&nbsp;Failed to generate samplecode!</div>";
                            Yii::$app->session->setFlash('error', 'Failed to generate samplecode!');
                            return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$requestId,'pstc_id'=>$pstcId]);
                        }
                    //} else {
                    //    $transaction->rollBack();
                        //return "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' style='font-size:18px;'></span>&nbsp;Request 2 failed to save!</div>";
                    //    Yii::$app->session->setFlash('error', 'Request failed to save!');
                    //    return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$requestId,'pstc_id'=>$pstcId]);
                   //}
                } else {
                    //$requestSave = 0;
                    $transaction->rollBack();
                    //return "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' style='font-size:18px;'></span>&nbsp;Request 2 failed to save!</div>";
                    Yii::$app->session->setFlash('error', 'Request failed to save!');
                    return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$requestId,'pstc_id'=>$pstcId]);
                }
            } else {
                //Yii::$app->session->setFlash('error', "Request failed to save!");
                //return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$requestId,'pstc_id'=>$pstcId]);
                $transaction->rollBack();
                //return "<div class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' style='font-size:18px;'></span>&nbsp;Request 3 failed to save!</div>";
                Yii::$app->session->setFlash('error', 'Request failed to save!');
                return $this->redirect(['/pstc/pstcrequest/view','request_id'=>$requestId,'pstc_id'=>$pstcId]);
            }
        } else {
            $date = new DateTime();
            $date2 = new DateTime();
            $profile= Profile::find()->where(['user_id'=> Yii::$app->user->id])->one();
            date_add($date2,date_interval_create_from_date_string("1 day"));
            $model->request_datetime = date("Y-m-d h:i:s");
            $model->report_due = date_format($date2,"Y-m-d");
            $model->created_at = date('U');
            $model->rstl_id = $rstlId;
            $model->payment_type_id = 1;
            $model->modeofrelease_ids = '1';
            $model->discount_id = 0;
            $model->discount = '0.00';
            $model->total = 0.00;
            $model->posted = 0;
            $model->status_id = 1;
            $model->request_type_id = 1;
            $model->modeofreleaseids = '1';
            $model->payment_status_id = 1;
            //$model->testcategory_id = ;
            //$model->sampletype_id = ;
            $model->request_date = date("Y-m-d");
            $model->customer_id = $request['customer_id'];
            $model->conforme = $request['submitted_by'];

            if($profile){
                $model->receivedBy=$profile->firstname.' '. strtoupper(substr($profile->middleinitial,0,1)).'. '.$profile->lastname;
            }else{
                $model->receivedBy="";
            }

            if(\Yii::$app->request->isAjax) {
                return $this->renderAjax('_formRequestDetails', [
                    'model' => $model,
                    'request' => $request,
                    'customer' => $customer,
                    'sampleDataProvider' => $sampleDataProvider,
                    'analysisDataprovider'=> $analysisDataprovider,
                    'respond' => $respond,
                    'pstc' => $pstc,
                    'subtotal' => $subtotal,
                    'discounted' => $discount,
                    'total' => $total,
                    'countSample' => count($samples),
                    'countAnalysis' => count($analyses),
                    //'testcategory' => null,
                    //'sampletype' => null,
                    'testcategory' => ArrayHelper::map(Testcategory::find()->all(),'testcategory_id','category'), //data should be in synched in ulims portal
                    'laboratory' => ArrayHelper::map(Lab::find()->all(),'lab_id','labname'), //data should be in synched in ulims portal
                    'sampletype' => ArrayHelper::map(Sampletype::find()->all(),'sampletype_id','type'), //data should be in synched in ulims portal
                ]);
            } else {
                return $this->renderAjax('_formRequestDetails', [
                    'model' => $model,
                    'request' => $request,
                    'customer' => $customer,
                    'sampleDataProvider' => $sampleDataProvider,
                    'analysisDataprovider'=> $analysisDataprovider,
                    'respond' => $respond,
                    'pstc' => $pstc,
                    'subtotal' => $subtotal,
                    'discounted' => $discount,
                    'total' => $total,
                    'countSample' => count($samples),
                    'countAnalysis' => count($analyses),
                    //'testcategory' => null,
                    //'sampletype' => null,
                    'testcategory' => ArrayHelper::map(Testcategory::find()->all(),'testcategory_id','category'), //data should be in synched in ulims portal
                    'laboratory' => ArrayHelper::map(Lab::find()->all(),'lab_id','labname'), //data should be in synched in ulims portal
                    'sampletype' => ArrayHelper::map(Sampletype::find()->all(),'sampletype_id','type'), //data should be in synched in ulims portal
                ]);
            }
        }
    }

    // ------------------------------------
    // Protected functions
    // ------------------------------------

    protected function listCustomers($rstlId)
    {
        $customer = ArrayHelper::map(Customer::find()->where('rstl_id =:rstlId',[':rstlId'=>$rstlId])->all(), 'customer_id',
            function($customer, $defaultValue) {
                return $customer->customer_name;
        });
        return $customer;
    }

    protected function listSampletemplate()
    {
        $sampleTemplate = ArrayHelper::map(SampleName::find()->all(), 'sample_name_id', 
            function($sampleTemplate, $defaultValue) {
                return $sampleTemplate->sample_name;
        });

        return $sampleTemplate;
    }

    public function actionGet_testcategory()
    {
        $labId = (int) Yii::$app->request->get('lab_id');
        if($labId > 0)
        {
            $testcategory = Labsampletype::find()
                //->select('tbl_sampletype.*')
                ->joinWith(['testcategory'],true)
                ->where('tbl_lab_sampletype.lab_id = :labId', [':labId' => $labId])
                //->asArray()
                ->groupBy('tbl_testcategory.testcategory_id')
                ->all();

                // var_dump($testcategory); exit();

                //print_r($testcategory);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if(count($testcategory) > 0)
            {
                foreach($testcategory as $list) {
                   
                    $data[] = ['id' => $list->testcategory->testcategory_id, 'text' => $list->testcategory->category];
                }
                //$data = ['id' => '', 'text' => 'No results found'];
            } else {
                $data = ['id' => '', 'text' => 'No results found'];
            }
        } else {
            $data = ['id' => '', 'text' => 'No results found'];
        }
        return ['data' => $data];
    }

}
