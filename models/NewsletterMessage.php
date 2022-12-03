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
 * @property blob $recipients_class
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
    
    protected $recipientsObject;
    
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
            [['recipients_class'], 'string'],
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
            'recipients_class' => 'Recipients Object',
            'send_at' => 'Send At',
            'completed_at' => 'Completed At',
            'blacklisted' => 'Blacklisted',
        ];
    }
    
    
    
    /**
     * Retrurns the recipients object
     */
    public function getRecipientsObject($params = null)
    {
        if (is_a($this->recipientsObject, 'schmauch\newsletter\models\RecipientsInterface')) {
            return $this->recipientsObject;
        }
                
        $namespace = 'schmauch\\newsletter\\models\\recipients\\';
        $class = $namespace . $this->recipients_class;
        
        if($params && is_array($params)) {
            $config = $params;
        } else {
            $config = !empty($this->recipients_config) ? unserialize($this->recipients_config) : [];
            if (!is_array($config)) {
                $config = [];
            }
        }
        
        if(!class_exists($class)) {
            throw new \Exception('Klasse ' . $class . ' gibt es nicht');
        }
        
        $this->recipientsObject = new $class($config);
        return $this->recipientsObject;
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
    
    
    /**
     *
     */
    public function getPlaceholders()
    {
        $pattern = '/\[\[([0-9A-Za-z_]+)\]\]/';
        $html = file_get_contents($this->getHtmlFile());
        $htmlCount = preg_match_all($pattern, $html, $htmlPlaceholders);

        $text  = file_get_contents($this->getTextFile());
        $textCount = preg_match_all($pattern, $text, $textPlaceholders);
        
        
        $diff = array_merge(
            array_diff($htmlPlaceholders[1], $textPlaceholders[1]), 
            array_diff($textPlaceholders[1], $htmlPlaceholders[1])
        );
        
        if (!empty($diff)) {
            \Yii::$app->session->addFlash('warning', 'Die verwendeten Platzhalter in der Html- und Text-Datei weichen voneinander ab.');
        }

        $placeholders = array_unique(array_merge([0 => 'email'], $htmlPlaceholders[1], $textPlaceholders[1]));
        
        return $placeholders;
    }
}
