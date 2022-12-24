<?php

namespace schmauch\newsletter\controllers;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

class DefaultController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],            
            ],
        );
    }
    
    
    
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
