<?php

namespace common\models\lab;

use Yii;

/**
 * This is the model class for table "tbl_reportfactors".
 *
 * @property int $accompfactor_id
 * @property int $yearmonth
 * @property string $name
 * @property string $remarks
 */
class Reportfactors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_reportfactors';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('labdb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['yearmonth','name'], 'required'],
            [['yearmonth'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'accompfactor_id' => 'Accompfactor ID',
            'yearmonth' => 'Year and Month',
            'name' => 'Name',
            'remarks' => 'Remarks',
        ];
    }
}
