<?php
namespace frontend\modules\reports\modules\finance\controllers;

use Yii;
use common\models\lab\Reportsummary;
use common\models\lab\Sample;
use common\models\lab\Reportform;
use frontend\modules\reports\modules\models\Requestextend;
use common\models\lab\Request;
use common\models\lab\Lab;
use common\models\lab\Businessnature;
use common\models\lab\Reportholder;
use common\models\lab\Sampletype;

class AnalyticController extends \yii\web\Controller
{

    public function actionDisplaymonth($data)
    {
    	$exploded = explode("_", $data);
    	$rstlId = Yii::$app->user->identity->profile->rstl_id;
        return $this->renderAjax('display-month',['yearmonth'=>$exploded[0],'lab_id'=>$exploded[1],'rstlId'=>$rstlId]);
    }

    public function actionIndex()
    {
    	$reportform = new Reportform();
    	$rstlId = Yii::$app->user->identity->profile->rstl_id;
    	if ($reportform->load(Yii::$app->request->post())) {
    		$labId = $reportform->lab_id;
			$year = $reportform->year;
		}else{
			$labId = 1;
			$year = date('Y'); //current year
		}

    	//get the rstlid of the user

    	//get all the sum of income generated per month
    	$summary = Reportsummary::find()->where(['year'=> $year,'lab_id'=>$labId,'rstl_id'=>$rstlId])->all();
    	$actualfees = [];
    	$discounts = [];
    	$finalize = [];
    	$monthlyname =[];
		$month = 0;
		
		while ( $month<= 11) {
			if(isset($summary[$month])){
				$actualfees[] = (int)$summary[$month]->actualfees;
				$discounts[] = (int)$summary[$month]->discount;
				$monthlyname[]  = $summary[$month]->year."-".$summary[$month]->month;
				$finalize[] = "green";
			}
			else{
				$actualfees[] =0;
				$discounts[] = 0;
				$finalize[] = "red";
			}
			$month ++;
		}

		return $this->render('index',['actualfees'=>$actualfees,'discounts'=>$discounts,'finalize'=>$finalize,'labId' => $labId,'year' => $year,'reportform'=>$reportform]);
    }


    public function actionGetsamples($yearmonth,$lab_id){
    	try {
    		//get all the samples in a month
    		$request = new Requestextend;
    		$samples = $request->getStats($yearmonth,$lab_id,1);


    	} catch (Exception $e) {
			return $e;
    	}
    	
     return $samples;
    }

    public function actionGetcustomers($yearmonth,$lab_id){
    	try {

    		$reqs =  Request::find()
    		->select(['total'=>'count(tbl_customer.customer_id)','customer_id'=>'business_nature_id'])
    		->where(['DATE_FORMAT(`request_datetime`, "%Y-%m")' => $yearmonth,'lab_id'=>$lab_id])
    		->andWhere(['>','status_id',0])
			->joinWith(['customer' => function($query){ return $query;}])
    		->groupBy(['business_nature_id'])
    		->orderBy('business_nature_id ASC')
    		->all();

    		$series = [];
    		foreach ($reqs as $req) {

    			$bn = Businessnature::findOne($req->customer_id);
    			$new = new Reportholder;
    			$new->name = $bn->nature;
    			$new->y = (int)$req->total;
    			$series[]=$new;
    		}


    	} catch (Exception $e) {
			return $e;
    	}

    	return $this->renderAjax('businessnature',['data'=>$series]);
    
    }

    public function actionGettestsperformed($yearmonth,$lab_id){
    	try {

    		$reqs =  Request::find()
    		->select(['total'=>'count(analysis_id)','conforme'=>'testname'])
    		->where(['DATE_FORMAT(`request_datetime`, "%Y-%m")' => $yearmonth,'lab_id'=>$lab_id])
    		->andWhere(['>','status_id',0])
			->joinWith(['analyses'])
    		->groupBy(['testname'])
    		->orderBy('testname ASC')
    		->all();

    		$series = [];
    		foreach ($reqs as $req) {
    			 // $st= Sampletype::findOne($req->conforme);
    			// $series[]=['name'=>$st->type,'value'=>(int)$req->total];

    			$new = new Reportholder;
    			$new->name = $req->conforme;
    			$new->y = (int)$req->total;

    			$series[]=$new;
    		}


    	} catch (Exception $e) {
			return $e;
    	}
    	// echo var_dump($series); exit;
    	// return json_encode($reqs);
    	return $this->renderAjax('testperformed',['reqs'=>$series,'reportholder'=>new Reportholder]);
    
    }

}
