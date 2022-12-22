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
    
    private $model;
    private $query;
       
    public function getDataProvider()
    {
        if (!$this->activeRecord || !class_exists($this->activeRecord)) {
            return new ArrayDataProvider(['allModels' => []]);
            //throw new \Exception('ActiveRecord ' . $this->activeRecord . ' existiert nicht');
        }
        
        $this->model = new $this->activeRecord();
        $this->query = $this->model->find();
                
        return new ActiveDataProvider(['query' => $this->query]);
        
    }
    
    public function getColumns()
    {
        if(!$this->activeRecord || !class_exists($this->activeRecord)) {
            return [];
        }
        
        $this->model = new $this->activeRecord();
        return array_keys($this->model->attributes);
    }
    
}
