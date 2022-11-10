<?php

namespace schmauch\newsletter\models;

use Yii;

/**
 * This is the model class for table "newsletter_attachments".
 *
 * @property int $id
 * @property int $message_id
 * @property string $file_name
 * @property int|null $mode
 *
 * @property NewsletterMessage $message
 */
class NewsletterAttachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newsletter_attachments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message_id', 'file_name'], 'required'],
            [['message_id', 'mode'], 'integer'],
            [['file_name'], 'string', 'max' => 255],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewsletterMessage::class, 'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_id' => 'Message ID',
            'file_name' => 'File Name',
            'mode' => 'Mode',
        ];
    }

    /**
     * Gets query for [[Message]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(NewsletterMessage::class, ['id' => 'message_id']);
    }
}
