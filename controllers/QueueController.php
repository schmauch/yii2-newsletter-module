<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\actions\QueueInfoAction;
use schmauch\newsletter\jobs\SendMailJob;
use schmauch\newsletter\models\NewsletterBlacklist;
use schmauch\newsletter\models\NewsletterMessage;


use yii\web\Controller;

class QueueController extends Controller
{
    protected $message;

    /**
     * //...
     */
    public function beforeAction($action)
    {
        if (isset($_GET['id'])) {
            $this->message = NewsletterMessage::findOne($_GET['id']);
        }
        
        if (empty($this->message)) {
            throw new \Exception('Keine Nachricht gefunden.');
        }
        
        return true;
    }
    
    
    /**
     * Adds all newsletter messages to queue
     */
     public function actionQueue($id)
     {
        $checks = $this->message->isReadyToSend($this->message);
        
        $messages_limit = $this->module->messages_limit ?? 30;
        $messages_delay = $this->module->messages_delay ?? 360;
        
        // show errors if newsletter isn't ready to send
        if (!array_product($checks)) {
            return $this->redirect(['message/ready-to-send', 'id' => $id]);
        }
        
        $dataProvider = $this->message->recipientsObject->getDataProvider();
        $dataProvider->getPagination()->setPageSize($messages_limit);

        $pages = ceil($dataProvider->getTotalCount() / 
            $dataProvider->getPagination()->getPageSize());
            
        for($i=0;$i<$pages;$i++) {
            
            $dataProvider->getPagination()->setPage($i);
            $dataProvider->refresh();
            
            foreach($dataProvider->getModels() as $recipient) {
                
                if (NewsletterBlacklist::find()->where(['email' => $recipient['email']])->count()) {
                    $this->message->blacklisted++;
                    $this->message->save();
                    continue;
                }
                
                
                $now = time();

                $atom = $this->message->send_date . 'T' . 
                    $this->message->send_time . date('P');
                $sendAt = new \DateTime($atom);
                
                $delay = $sendAt->getTimestamp() - $now;
                
                if($delay < 0) {
                    $delay = 0;
                }
                
                $delay += $i * $messages_delay;
                $delay += rand(1, $messages_delay / $messages_limit);
                
                $job = new SendMailJob([
                    'message_id' => $id,
                    'recipient' => $recipient,
                    'channel' => $this->message->id,
                ]);
                
                $queue = $this->module->queue;
                $queue->channel = $this->message->slug;
                $queue->delay($delay)->push($job);
            }
        }
        
        return $this->redirect(['run', 'id' => $id]);
     }



    /**
     * //...
     */
    public function actionStatus($id)
    {
        //$this->message = $this->findModel($id);
        
        $queue = $this->module->queue;
        $queue->channel = $this->message->slug;
        
        $action = new QueueInfoAction('QueueInfo', $this, ['queue' => $queue]);
        $jobs = $action->run();
        
        return $this->render('status', [
            'model' => $this->message,
            'jobs' => $jobs,
        ]);
    }
    
    
    
    /**
     * //...
     */
    public function actionRun($id)
    {
        $logFile = $this->message->getMessageDir() . 'queue.log';
        $command = realpath(\Yii::getAlias('@app/../yii')) . ' newsletter/console/run --id=' . $id . ' >> ' . $logFile . ' 2>&1 & echo $!';
        $this->message->pid = exec($command);
        $this->message->save();
        return $this->redirect(['status', 'id' => $id]);
        
    }
    
    
    
}
