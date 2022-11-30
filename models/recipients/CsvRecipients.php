<?php

namespace schmauch\newsletter\models\recipients;

use yii\base\Model;

use gri3li\yii2csvdataprovider\CsvDataProvider;

use schmauch\newsletter\models\RecipientsInterface;


class CsvRecipients extends Model implements RecipientsInterface
{
    public $file = false;
    
    public function rules()
    {
        return [
            [['file'], 'file',
                'skipOnEmpty' => false, 
                'extensions' => 'csv',
                'maxSize' => ini_get('upload_max_filesize'),
            ],
        ];
    }
    
    public function getDataProvider()
    {
        if ($fileName && file_exists($fileName)) {
            return new CsvDataProvider([
                'filename' => $this->file,
            ]);
        }
        
        return false;
    }


    public function getColumns()
    {
        
    }
    


}
