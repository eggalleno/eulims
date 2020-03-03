<?php

namespace frontend\modules\reports\modules\models;

use Yii;
use common\models\system\Rstl;
use common\components\Functions;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\lab\Request;
use common\models\lab\Sample;
/**
 * Extended model from lab Request
 *
**/

class Requestextend extends Request
{
    public $totalrequests ,$month, $monthnum;
    public $from_date, $to_date, $lab_id;

    public function countSummary($labId,$requestDate,$startDate,$endDate,$summaryType,$requestType)
    {
        $function = new Functions();
        $connection = Yii::$app->labdb;
        $rstlId = Yii::$app->user->identity->profile->rstl_id;

        $query = $function->ExecuteStoredProcedureOne("spSummaryforSamples(:rstlId,:labId,:requestDate,:startDate,:endDate,:summaryType,:requestType)", 
            [':rstlId'=>$rstlId,':labId'=>$labId,':requestDate'=>$requestDate,':startDate'=>$startDate,':endDate'=>$endDate,':summaryType'=>$summaryType,':requestType'=>$requestType], $connection);
        return $query['Counter'];
    }

    public function computeAccomplishment($labId,$requestDate,$startDate,$endDate,$generateType,$requestType)
    {
        $function = new Functions();
        $connection = Yii::$app->labdb;
        $rstlId = Yii::$app->user->identity->profile->rstl_id;

        $query = $function->ExecuteStoredProcedureOne("spAccomplishmentReport(:rstlId,:labId,:requestDate,:startDate,:endDate,:generateType,:requestType)", 
            [':rstlId'=>$rstlId,':labId'=>$labId,':requestDate'=>$requestDate,':startDate'=>$startDate,':endDate'=>$endDate,':generateType'=>$generateType,':requestType'=>$requestType], $connection);
        return $query['Counter'];
    }


    //Bergel - get the count of the relation
      public function getSamplescount()
    {
        return Sample::find()->where(['request_id' => $this->request_id])->count();
    }

    public function getStats($yearmonth,$lab_id,$type){
        // return Sample::find()->with(['Request' => function($query){
        //     $query->where(['DATE_FORMAT(`request_datetime`, "%Y-%m")' => $yearmonth]);
        // }])->count();   

        $total = 0;
        if($type==1){
            //total number of samples
            $reqs =  Requestextend::find()->select(['request_id'])->where(['DATE_FORMAT(`request_datetime`, "%Y-%m")' => $yearmonth,'lab_id'=>$lab_id])->andWhere(['>','status_id',0])->with(['samples' => function($query){
                $query->andWhere(['active'=>'1']);
            }])->all();

            foreach ($reqs as $req) {
                $total += count($req->samples);
            }
        }elseif($type==2){
            //total number of analysis
            $reqs =  Requestextend::find()->select(['request_id'])->where(['DATE_FORMAT(`request_datetime`, "%Y-%m")' => $yearmonth,'lab_id'=>$lab_id])->andWhere(['>','status_id',0])->with(['analyses' => function($query){
                $query->andWhere(['<>','references','-'])->andWhere(['cancelled'=>'0']);
            }])->all();

            foreach ($reqs as $req) {
                $total += count($req->analyses);
            }
        }elseif($type==3){
            //total number of discount
            //this fomula is derived from nolan's SP but still bugs out me how this works
            $reqs =  Requestextend::find()
            ->select(['total'=>'SUM((`total`/(1-(`discount`/100)))-`total`)'])
            ->where(['DATE_FORMAT(`request_datetime`, "%Y-%m")' => $yearmonth,'lab_id'=>$lab_id,'payment_type_id'=>1])
            ->andWhere(['>','status_id',0])
            ->andWhere(['>','discount_id',0])
            ->one();

            // var_dump($reqs); exit;
            $total = $reqs->total;
        }


        return $total;
    }

}
