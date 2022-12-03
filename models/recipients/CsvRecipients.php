<?php

namespace schmauch\newsletter\models\recipients;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;

use gri3li\yii2csvdataprovider\CsvDataProvider;

use schmauch\newsletter\models\RecipientsInterface;


class CsvRecipients extends Model implements RecipientsInterface
{
    public $file = false;
    protected $uploadToSlug;
    public $columns = [];
    
    public function rules()
    {
        return [
            [['file'], 'file',
                'skipOnEmpty' => true, 
                'extensions' => 'csv',
                'maxSize' => ini_get('upload_max_filesize'),
            ],
        ];
    }
    
    public function getDataProvider()
    {
        if ($this->file && file_exists($this->file)) {
            return new CsvDataProvider([
                'filename' => $this->file,
            ]);
        }
        
        return new ArrayDataProvider(['allModels' => []]);
    }


    public function getColumns()
    {
        return array_unique(array_merge($this->columns, [['header' => 'email', 'attribute' => 0]]));
    }
    
    public function setUploadToSlug($slug)
    {
        $file = UploadedFile::getInstanceByName('NewsletterMessage[recipients_config][fileUpload]');
        
        if(!$file) {
            return false;
        }
        
        $path = \schmauch\newsletter\Module::getInstance()->params['files_path'] . $slug . '/';
        
        $infix = '';
        $i = 0;
        
        do {
            $fileName = $path . $file->baseName . $infix . '.' . $file->extension;
            $i++;
            $infix = '_' . $i;
        } while(file_exists($fileName));
        
        if ($file && !$file->saveAs($fileName)) {
            \Yii::$app->addFlash('error', 'Fehler beim Speichern der Datei');
        } else {
            $this->file = $fileName;
        }
    }
    
}
