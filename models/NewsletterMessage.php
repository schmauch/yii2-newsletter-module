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
 * @property date|null $send_date
 * @property time|null $send_time
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
    /*public $send_date;
    public $send_time;*/
    
    protected $recipientsObject;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newsletter_messages';
    }
    
    
    
    public function beforeValidate()
    {
        if(!empty($this->send_time) && substr_count($this->send_time, ':') < 2) {
            $this->send_time .= ':00';
        }
        return true;
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
            [['send_date'], 'date', 'format' => 'php:Y-m-d'],
            [['send_time'], 'time', 'format' => 'php:H:i:s'],
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
            'send_date' => 'Datum',
            'send_time' => 'Uhrzeit',
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
            //throw new \Exception('Klasse ' . $class . ' gibt es nicht');
            return false;
        }
        
        $this->recipientsObject = new $class($config);
        return $this->recipientsObject;
    }
    
    
    
    /**
     * Gets the html content
     */
    public function getHtmlFile()
    {
        return $this->getMessageDir() .'/message.html';
    }
    
    

    /**
     * Gets the plain text content
     */
    public function getTextFile()
    {
        return $this->getMessageDir() .'/message.txt';
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
    public function getPlaceholders()
    {
        $pattern = '/<\?= \$([0-9A-Za-z_]+?) \?>/';
        $html = file_get_contents($this->getHtmlFile()) ?? '';
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
    
    
    /**
     * //...
     */
    public function getMessageDir()
    {
        return \schmauch\newsletter\Module::getInstance()->params['files_path'] . $this->slug . '/';
    }
    /**
     * //...
     */
    public function isReadyToSend()
    {
        if(!$this->getRecipientsObject() || empty($this->getRecipientsObject()->dataProvider)) {
            $checks['recipients_object'] = false;
            return $checks;
        }
        
        
        $checks['recipients'] = $this->getRecipientsObject()->dataProvider->getTotalCount() > 0;
        
        $checks['html'] = !empty(file_get_contents($this->getHtmlFile()));
        $checks['text'] = !empty(file_get_contents($this->getTextFile()));
        
        $columnNames = [];
        $columns = $this->getRecipientsObject()->getColumns();
        
        if (count($columns) === count($columns, true)) {
            $columnNames = $columns;
        } else {
            foreach($columns as $index => $column) {
                $columnNames[$index] = $column['header'] ?? '';
            }
        }
        
        $checks['placeholders'] = empty(
            array_diff($this->getPlaceholders(),
            $columnNames
        ));
        
        $checks['attachments'] = true;
        
        foreach($this->newsletterAttachments as $attachment)
        {
            $checks['attachments'] *= is_readable($this->getMessageDir() . 'attachments/' . $attachment->file);
        }
         
        
        return $checks;
    }
    
}
