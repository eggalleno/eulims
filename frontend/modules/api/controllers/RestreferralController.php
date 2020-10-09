<?php

namespace frontend\modules\api\controllers;

use common\models\referral\Lab;
use common\models\referral\Discount;
use common\models\referral\Purpose;
use common\models\referral\Modeofrelease;
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

}
