<?php

namespace schmauch\newsletter\models\recipients;

use schmauch\newsletter\models\RecipientsInterface;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class ActiveRecordRecipients extends Model implements RecipientsInterface
{
    public $activeRecord = false;
    public $queryOptions = false;    
    
    public function getDataProvider()
    {
        if (!$this->activeRecord || !class_exists($this->activeRecord)) {
            return new ArrayDataProvider(['allModels' => []]);
            throw new \Exception('ActiveRecord ' . $this->activeRecord . ' existiert nicht');
        }
        
        $model = new $this->activeRecord();
        $query = $model->find();
                
        return new ActiveDataProvider(['query' => $query]);
        
    }
    
    public function getColumns()
    {
        return [];
    }
    
}
