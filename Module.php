<?php

namespace schmauch\newsletter;

class Module extends \yii\base\Module
{
    public $files_path;
    
    public function init()
    {
        parent::init();
        
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require __DIR__ . '/config.php');
        
        if(empty($this->files_path)) {
            $this->files_path = $this->params['files_path'];
        }
    }
}
