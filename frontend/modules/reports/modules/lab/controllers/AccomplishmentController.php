<?php

namespace frontend\modules\reports\modules\lab\controllers;

use Yii;
use yii\web\Controller;
use common\models\lab\Sample;
use common\models\lab\SampleSearch;
use common\models\lab\Request;
use frontend\modules\reports\modules\models\Requestextend;
use common\models\lab\Lab;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;

class AccomplishmentController extends \yii\web\Controller
{
	/*
	Created By: Bergel T. Cutara
	Contacts:

	Email: b.cutara@gmail.com
	Tel. Phone: (062) 991-1024
	Mobile Phone: (639) 956200353

	Description: looks up all the request made in a certain time
	**/

    public function actionIndex()
    {
    	$model = new Requestextend;
    	$rstlId = Yii::$app->user->identity->profile->rstl_id;
    	
		if (Yii::$app->request->get())
		{
			$labId = (int) Yii::$app->request->get('lab_id');
			$year = (int) Yii::$app->request->get('year');
			
		} else {
			$labId = 1;
			$year = date('Y'); //current year
		}

		$modelRequest = Requestextend::find()
		->select([
			'monthnum'=>'DATE_FORMAT(`request_datetime`, "%m")',
			'month'=>'DATE_FORMAT(`request_datetime`, "%M")',
			'totalrequests' => 'count(request_id)',
			'total'=>'SUM(total)',
			'request_datetime'
		])
		->where('rstl_id =:rstlId AND status_id > :statusId AND lab_id = :labId AND DATE_FORMAT(`request_datetime`, "%Y") = :year', [':rstlId'=>$rstlId,':statusId'=>0,':labId'=>$labId,':year'=>$year])
		->groupBy(['DATE_FORMAT(request_datetime, "%Y-%m")'])
		->orderBy('request_datetime ASC');


	
		// var_dump($modelRequest); exit;
		$dataProvider = new ActiveDataProvider([
            'query' => $modelRequest,
            'pagination' => false,
        ]);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index', [
                'dataProvider' => $dataProvider,
                'lab_id' => $labId,
                'year' => $year,
                'model'=>$modelRequest,
	            'laboratories' => $this->listLaboratory(),
            ]);
        } else {
			return $this->render('index', [
	            'dataProvider' => $dataProvider,
	            'lab_id' => $labId,
	            'model'=>$modelRequest,
                'year' => $year,
	            'laboratories' => $this->listLaboratory(),
	        ]);
		}

        //return $this->render('index');
	}
	
	public function actionMontly($id)
    {
        // $taggingmodel = Tagging::find()->where(['analysis_id'=>$id])->one();
        // $analysis = Analysis::find()->where(['analysis_id'=>$id])->one();      
        // $model = new Tagging();
        
        // if ($taggingmodel){
            return $this->renderAjax('monthly');
        // }else{
        //     return $this->renderAjax('monthly', [
        //         'taggingmodel' => $taggingmodel,
        //     ]);
        // }

      
    }

    protected function listLaboratory()
    {
        $laboratory = ArrayHelper::map(Lab::find()->all(), 'lab_id', 
            function($laboratory, $defaultValue) {
                return $laboratory->labname;
        });

        return $laboratory;
    }

	function checkValidDate($date){
		$tempdate = explode('-', $date);

		if(count($tempdate) < 3 || count($tempdate) > 3)
		{
			return false;
		} else {
			$month = (int) $tempdate[1];
			$year = (int) $tempdate[0];
			$day = (int) $tempdate[2];
			// checkdate(month, day, year)
			if(checkdate($month,$day,$year) == true){
				return true;
			} else {
				return false;
			}
		}
	}

}
