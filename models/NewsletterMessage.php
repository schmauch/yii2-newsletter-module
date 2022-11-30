<?php

namespace schmauch\newsletter\models;

use Yii;

/**
 * This is the model class for table "newsletter_messages".
 *
 * @property int $id
 * @property string $slug
 * @property string $subject
 * @property string|null $template
 * @property blob $recipients_object
 * @property string|null $send_date
 * @property string|null $send_time
 * @property int|null $mails_sent
 * @property int|null $blacklisted
 * @property string|null $completed_at
 *
 * @property NewsletterAttachment[] $newsletterAttachments
 */
class NewsletterMessage extends \yii\db\ActiveRecord
{
    
    public $html;
    public $text;
    
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
            [['slug'], 'string'],
            //[['subject'], 'required' => $this->isNewRecord],
            [['send_date', 'send_time', 'completed_at'], 'safe'],
            [['send_date'] , 'date', 'format' => 'php:Y-m-d'],
            [['send_time'] , 'time', 'format' => 'php:H:i:s'],
            [['mails_sent', 'blacklisted'], 'integer'],
            [['recipients_object'], 'string'],
            [['subject', 'template'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'subject' => 'Subject',
            'template' => 'Template',
            'recipients_object' => 'Recipients Object',
            'send_at' => 'Send At',
            'completed_at' => 'Completed At',
            'blacklisted' => 'Blacklisted',
        ];
    }
    
    
    
    /**
     * Gets the html content
     */
    public function getHtmlFile()
    {
        return \schmauch\newsletter\Module::getInstance()->params['files_path'] . $this->slug .'/message.html';
    }

    /**
     * Gets the plain text content
     */
    public function getTextFile()
    {
        return \schmauch\newsletter\Module::getInstance()->params['files_path'] . $this->slug .'/message.txt';
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
