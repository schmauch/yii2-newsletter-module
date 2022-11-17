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
    public $params = [];
    public $recipient;
    public $subject;
    
    /**
     *
     */
    public function execute($queue)
    {
        $module = \yii::$app->controller->module;
        if(!is_a($module, '\yii\base\Module')) {
            die('Kein Modul!');
        }
        
        $mailer = \Yii::$app->mailer;
        if(!is_a($mailer, '\yii\mail\MailerInterface')) {
            die('Kein Mailer!');
        }
        
        $message = $mailer->compose($this->message, $this->params);
        if(!is_a($message, '\yii\mail\MessageInterface')) {
            die('Keine Message!');
        }
        
        $message->setTo($this->recipient);
        $message->setSubject($this->subject);
        //$message->setFrom($module->params['from']);
        $message->setFrom('mail@roger-schmutz.ch');
        die(var_export(get_class_methods($mailer), true));
        echo 'verarbeite '.$recipient;
        return $mailer->sendMessage($message);
    }
}