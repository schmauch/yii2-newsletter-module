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
    protected $firstLineToColumns = false;
    
    public function rules()
    {
        return [
            [['file'], 'file',
                'skipOnEmpty' => false, 
                'extensions' => 'csv',
                'maxSize' => ini_get('upload_max_filesize'),
            ],
            [['slug', 'firstLineToColumns'], 'string'],
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
    
    
    
    /**
     * 
     */
    public function getColumns()
    {
        return $this->columns;
    }
    
    
    
    /**
     * Upload csv file
     */
    public function setUploadToSlug($slug)
    {
        $file = UploadedFile::getInstanceByName('NewsletterMessage[recipients_config][fileUpload]');
        
        if(empty($file)) {
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
        
        
        // read the columns from the first line
        if ($this->firstLineToColumns) {
            $this->setFirstLineToColumns($this->firstLineToColumns);
        } else {
            $f = fopen($this->file, 'r');
            $line = fgets($f);
            fclose($f);
            
            for($i=0;$i<=substr_count($line, ',');$i++) {
                $this->columns[$i] = ['header' => $i, 'attribute' => $i];
            }
        }
    }
    
    
    /**
     *
     */
    public function setFirstLineToColumns($firstLineToColumns)
    {
        if (!$firstLineToColumns && !$this->firstLineToColumns) {
            return;
        }
        
        $firstLine = false;
        
        if($f = fopen($this->file, 'c+')){
            if (!flock($f,LOCK_EX)) {
                fclose($f);
            }
            
            $offset = 0;
            
            $len = filesize($this->file);
            
            while(($line = fgets($f, 4096)) !== false){
                if (!$firstLine) {
                    $firstLine = $line;
                    $offset = strlen($firstLine);
                    continue;
                }
                $pos = ftell($f);
                fseek($f, $pos - strlen($line) - $offset);
                fputs($f, $line);
                fseek($f, $pos);
            }
            fflush($f);
            ftruncate($f, ($len-$offset));
            flock($f, LOCK_UN);
            fclose($f);
        }
        
        $columns = str_getcsv($firstLine);
        
        foreach($columns as $index => $column) {
            $this->columns[$index] = [ 
                'header' => $column, 
                'attribute' => $index,
            ];
        }
    }   
    
}
