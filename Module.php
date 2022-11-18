<?php

namespace schmauch\newsletter;

class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
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

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'schmauch\newsletter\commands';
        }
    }
}
