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
 * @property string|null $send_at
 * @property string|null $completed_at
 * @property int|null $mails_sent
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
            [['slug'], 'string'],
            [['subject'], 'required'],
            [['send_at', 'completed_at'], 'safe'],
            [['send_at', 'completed_at'], 'datetime'],
            [['blacklisted'], 'integer'],
            [['recipients_object'], 'string'],
            [['subject', 'template'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'content-tools-image-upload' => \bizley\contenttools\actions\UploadAction::className(),
            'content-tools-image-insert' => \bizley\contenttools\actions\InsertAction::className(),
            'content-tools-image-rotate' => \bizley\contenttools\actions\RotateAction::className(),
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
     * Gets query for [[NewsletterAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewsletterAttachments()
    {
        return $this->hasMany(NewsletterAttachment::class, ['message_id' => 'id']);
    }
    
        
    
    /**
     * //...
     */
    protected function getSlug()
    {
        if(!isset($this->slug)) {
            $this->slug = uniqid();
        }
        
        return $this->slug;
    }
    
    /**
     * //...
     */
    protected function getNewsletterFilesPath()
    {
        $path = \schmauch\newsletter\Module::getInstance()->params['files_path'] . 
            '/' . $this->getSlug() . '/';
        
        if(!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        return $path;
    }
    
}
