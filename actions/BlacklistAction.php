<?php

namespace schmauch\newsletter\actions;

use yii\base\Action;

use schmauch\newsletter\models\NewsletterBlacklist;

class BlacklistAction extends Action
{
    public $queue;


    /**
     * Info about queue status.
     */
    public function run($email = null)
    {
        $this->controller->viewPath = '@vendor/schmauch/yii2-newsletter-module/views/blacklist/';
        
        $email = $this->controller->request->post('NewsletterBlacklist')['email'] ?? $email;
        
        // if the address is already blacklisted, show success page
        if ($email && $model = NewsletterBlacklist::findOne(['email' => $email])) {
            return $this->controller->render('success', ['model' => $model]);
        }
        
        // 
        $model = new NewsletterBlacklist();
        $model->email = $email;
        
        if ($this->controller->request->isPost) {
            $model->added_at = date('Y-m-d H:i:s');
            if(!$model->validate()) {
                $message = 'Die E-Mail-Adresse ' . $model->email . ' konnte nicht aus der Liste entfernt werden.';
                $message .= $model->getErrorSummary();
                \Yii::$app->session->setFlash('error',  $message);
            }

            if($model->save()) {
                return $this->controller->redirect(['sign-off', 'email' => $email]);
            }
        }        

        return $this->controller->render('form', [
            'model' => $model,
        ]);
    }

}
