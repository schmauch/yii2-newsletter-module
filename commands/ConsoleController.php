<?php

namespace schmauch\newsletter\commands;

class ConsoleController extends \yii\console\Controller
{
    public $defaultAction = 'info';
    
    protected $queue;
    
    public function init()
    {
        parent::init();
        
        $this->queue = $this->module->queue;
    }
    
    public function actionInfo()
    {
        
        $command = new $this->queue->commandClass('myCommand', $this);
        $info = $command->actions()['info'];
        $action = new $info('myInfo', $this, ['queue' => $this->queue]);
        
        $action->run();
    }
    
    public function actionRun()
    {
        $this->queue->run(false);
    }
}
