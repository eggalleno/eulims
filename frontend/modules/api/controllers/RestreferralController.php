<?php

namespace frontend\modules\api\controllers;

class RestreferralController extends \yii\rest\Controller
{
	public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            // 'except' => ['index'],
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

}
