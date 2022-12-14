<?php

namespace schmauch\newsletter;

class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $allowed_attachment_extensions;
    public $files_path;
    public $messages_limit;
    public $messages_delay;
    public $senderEmail;
    public $senderName;
    public $template_path;
    
    public function init()
    {
        
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require __DIR__ . '/config.php');
        
        parent::init();
        
        \Yii::setAlias('@schmauch/newsletter', __DIR__);
        
        foreach(get_object_vars($this) as $key => $value) {
            if (empty($value)) {
                $this->$key = $this->params[$key] ?? null;
            } 
        }
        
        /*if(empty($this->files_path)) {
            $this->files_path = $this->params['files_path'];
        }*/
        
        
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'schmauch\newsletter\commands';
        }
    }
}
