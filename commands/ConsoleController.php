<?php

namespace schmauch\newsletter\commands;

use schmauch\newsletter\models\NewsletterMessage;

class ConsoleController extends \yii\console\Controller
{
    public $defaultAction = 'info';
    
    public $id;
    
    protected $queue;
    
    /*public function init()
    {
        parent::init();
        
        $this->queue = $this->module->queue;        
     }
    */
    
    
    public function options($actionId)
    {
        return ['id'];
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
        $message = NewsletterMessage::findOne($this->id);
        $this->queue = $this->module->queue;
        $this->queue->channel = $message->slug;
                
        echo '[' . date('Y-m-d H:i:s') . '] Warte auf Queue: ' . $this->queue->channel . "\n";
        
        if (\Yii::$app instanceof Yii\console\Application) {
            $message->pid = getmypid();
            $message->save();
        }
        
        return $this->queue->run(true);
    }
}
