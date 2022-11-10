<?php

namespace schmauch\newsletter\models;

use Yii;

/**
 * This is the model class for table "newsletter_messages".
 *
 * @property int $id
 * @property string $subject
 * @property string|null $html_file
 * @property string|null $text_file
 * @property string|null $template
 * @property string|null $recipients_file
 * @property string|null $send_at
 * @property string|null $completed_at
 * @property int|null $blacklisted
 *
 * @property NewsletterAttachment[] $newsletterAttachments
 */
class NewsletterMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newsletter_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject'], 'required'],
            [['send_at', 'completed_at'], 'safe'],
            [['send_at', 'completed_at'], 'datetime'],
            [['blacklisted'], 'integer'],
            [['subject', 'html_file', 'text_file', 'template', 'recipients_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'html_file' => 'Html File',
            'text_file' => 'Text File',
            'template' => 'Template',
            'recipients_file' => 'Recipients File',
            'send_at' => 'Send At',
            'completed_at' => 'Completed At',
            'blacklisted' => 'Blacklisted',
        ];
    }

    /**
     * Gets query for [[NewsletterAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewsletterAttachments()
    {
        return $this->hasMany(NewsletterAttachment::class, ['message_id' => 'id']);
    }
}
