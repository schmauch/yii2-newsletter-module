<?php

namespace schmauch\newsletter\models\recipients;

use schmauch\newsletter\models\RecipientsInterface;

use yii\base\Model;

class ActiveRecordRecipients extends Model implements RecipientsInterface
{
    public $query = false;
    
    public function configure(array $params)
    {
        
    }
    
    
    public function getDataProvider()
    {
        
    }
    
}