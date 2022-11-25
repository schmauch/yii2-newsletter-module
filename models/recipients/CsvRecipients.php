<?php

namespace schmauch\newsletter\models\recipients;

use gri3li\yii2csvdataprovider\CsvDataProvider;

class CsvRecipients
{
    public $fileName;
    
    public function getDataProvider()
    {
        $dataProvider = new CsvDataProvider([
            'filename' => $this->fileName,
        ]);
    }
}
