<?php

namespace schmauch\newsletter\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionSettings()
    {
        $keys = array_keys($this->module->params);
        foreach($keys as $key) {
            $params[$key] = get_object_vars($this->module)[$key];
        }
        return $this->render('settings', ['params' => $params]); 
    }
}
