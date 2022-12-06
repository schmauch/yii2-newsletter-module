<?php

namespace schmauch\newsletter\actions;

class UploadAction extends \bizley\contenttools\actions\UploadAction::className()
{

    public $uploadDir = \schmauch\newsletter\Module::class->parameters['files_path']
    
    public function run()
    {
        $json = parent::run();
        
    }
}
