<?php

namespace schmauch\newsletter\jobs;

use yii\base\BaseObject;

/*
 * Queue job to send out mails
 *
 * @property string|array $message the view file(s) used for yii\mail\MailerInterface::compose()
 * @property array|null $params the parameters used for yii\mail\MailerInterface::compose()
 * @property string|array $recipient the recipient used for yii\mail\MessageInterface::setTo()
 * @property string|array $recipient the recipient used for yii\mail\MessageInterface::setSubject()
 */
class SendMailJob extends BaseObject implements \yii\queue\JobInterface
{
    public $message;
    public $params;
    public $recipient;
    public $subject;
    
    /**
     *
     */
    public function execute($queue)
    {
        $mailer = \Yii::$app->mailer;
        
        $message = $mailer->compose($this->message, $this->params);
        $message->setTo($this->recipient);
        $message->setSubject($this->subject);
        
        print_r($message);
        
        return $mailer->send($message);
    }
}