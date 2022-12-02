<?php

namespace schmauch\newsletter\models\recipients;

use yii\base\Model;
use yii\data\ArrayDataProvider;

use schmauch\newsletter\models\RecipientsInterface;


class TestRecipients extends Model implements RecipientsInterface
{
    public $recipients = [];
    
    public function rules()
    {
        return [
            [['recipients'], 'string'],
        ];
    }
    
    
    
    public function getDataProvider()
    {
        return new ArrayDataProvider([
            'allModels' => $this->recipients,
            'key' => 'email'
        ]);
    }
    
    
    public function getColumns()
    {
        if(!is_array($this->recipients)) {
            return [];
        }
        
        $recipient = reset($this->recipients);
        
        if(!is_array($recipient)) {
            return [];
        }
        
        return array_keys($recipient);
    }
}
