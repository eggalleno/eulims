<?php

namespace common\models\lab;

use Yii;

/**
 * This is the model class for table "tbl_methodreference".
 *
 * @property int $method_reference_id
 * @property int $test_id
 * @property string $method
 * @property string $reference
 * @property double $fee
 * @property int $created_time
 * @property int $updated_time
 *
 * @property Test $test
 */
class Methodreference extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_methodreference';
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
            [['test_id', 'method', 'reference', 'fee'], 'required'],
            [['test_id', 'created_time', 'updated_time'], 'integer'],
            [['fee'], 'number'],
            [['method', 'reference'], 'string', 'max' => 200],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'test_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'method_reference_id' => 'Method Reference ID',
            'test_id' => 'Test ID',
            'method' => 'Method',
            'reference' => 'Reference',
            'fee' => 'Fee',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['test_id' => 'test_id']);
    }
}
