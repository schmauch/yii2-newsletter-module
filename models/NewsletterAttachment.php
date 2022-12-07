<?php

namespace schmauch\newsletter\models;

use Yii;

/**
 * This is the model class for table "newsletter_attachments".
 *
 * @property int $id
 * @property int $message_id
 * @property string $file
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
            [['message_id', 'file'], 'required'],
            [['message_id', 'mode'], 'integer'],
            [['file'], 'file', 
                'skipOnEmpty' => false, 
                'extensions' => \schmauch\newsletter\Module::getInstance()->params['allowed_attachment_extensions'],
                'maxSize' => 2097152, //ini_get('upload_max_filesize'),
            ],
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
            'file' => 'File Name',
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

    /**
     * Upload file
     */
    public function upload()
    {
        $path = $this->message->getMessageDir() . '/attachments/';

        $infix = '';
        $i = 0;
        
        do {
            $fileName = $path . $this->file->baseName . $infix . '.' . $this->file->extension;
            $i++;
            $infix = '_' . $i;
        } while(file_exists($fileName));
        
        
        if (!$this->validate() || !$this->file->saveAs($fileName, false)) {
            \Yii::$app->addFlash('error', 'Fehler beim Speichern der Datei');
            return false;
        }
                
        $this->file->name = str_replace($path, '', $fileName);
        
        return true;
    }
}
