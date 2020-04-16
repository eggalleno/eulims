<?php

namespace common\modules\message\models;

use Yii;

/**
 * This is the model class for table "tbl_chat".
 *
 * @property int $chat_id
 * @property int $sender_userid
 * @property int $reciever_userid
 * @property string $message
 * @property string $timestamp
 * @property int $status_id
 * @property int $group_id
 *
 * @property GroupMember $group
 * @property ChatStatus $status
 * @property ChatActivityDetails[] $chatActivityDetails
 * @property ChatAttachment[] $chatAttachments
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_chat';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('messagedb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_userid', 'reciever_userid', 'message', 'status_id'], 'required'],
            [['sender_userid', 'reciever_userid', 'status_id', 'group_id'], 'integer'],
            [['message'], 'string'],
            [['timestamp'], 'safe'],
            [['chat_id'], 'unique'],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'chat_id' => 'Chat ID',
            'sender_userid' => 'Sender Userid',
            'reciever_userid' => 'To:',
            'message' => 'Message',
            'timestamp' => 'Timestamp',
            'status_id' => 'Status ID',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(GroupMember::className(), ['group_id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(ChatStatus::className(), ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatActivityDetails()
    {
        return $this->hasMany(ChatActivityDetails::className(), ['userid' => 'sender_userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatAttachments()
    {
        return $this->hasMany(ChatAttachment::className(), ['uploadedby_userid' => 'sender_userid']);
    }
	
	public static function getPossibleRecipients()
    {
        $user = new Yii::$app->controller->module->userModelClass;
		$users = $user::find();
        $users->where(['!=', 'user_id', Yii::$app->user->id]);
        
        $users = $users->all();

        return $users;
    }
}
