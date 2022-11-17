<?php

namespace schmauch\newsletter\jobs;

use yii\base\BaseObject;


class SendMailJob extends BaseObject implements \yii\queue\JobInterface
{
    public $message;
    public $recipient;
    public $params;
    
    public function execute($queue)
    {
        $mailer = \Yii::$app->mailer;
        $message = $mailer->compose($this->message, $this->params);
        $message->setTo($recipient);
        $mailer->send($message);
    }
}