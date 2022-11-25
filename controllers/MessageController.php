<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\models\NewsletterMessage;
use schmauch\newsletter\models\NewsletterMessageSearch;
use schmauch\newsletter\jobs\SendMailJob;


use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * MessageController implements the CRUD actions for NewsletterMessage model.
 */
class MessageController extends Controller
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
            ]
        );
    }
    
    public function actionFoo()
    {
        $subject = 'Das ist nur ein Test';
        
        $recipients = [
            'Roger Schmutz' => 'r.schmutz@girardi.ch',
            'Rotscher Schmutz' => 'info@freihand.ch',
            'irgendwas' => 'keine@gültige-adresse',
            'Roger' => 'info@schmutzkampagne.ch',
        ];
        
        $message = [
            'text' => 'test-text', 
            'html' => 'test-html'
        ];
        
        $params = [];
        
        foreach($recipients as $recipient) {
            
            $mailJob = new SendMailJob([
                'message' => [
                    'text' => 'test-text', 
                    'html' => 'test-html'
                ],
                'recipient' => $recipient,
                'subject' => $subject,
                'params' => $params,
            ]);
            
            $module = \Yii::$app->controller->module;
            $module->queue->push($mailJob);
            
        }
        
        echo 'fertig.';
    }
    
    public function actionBar()
    {
        $command = realpath(\Yii::getAlias('@app/../yii'));
        $command .= ' newsletter/console/run &';
        echo $command;
        
        exec($command, $output, $exit);
        
        //$this->module->queue->run(false);
        
        echo var_export($exit, true);
        echo var_export($output, true);
    }
    
    
    /**
     * Lists all NewsletterMessage models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new NewsletterMessageSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewsletterMessage model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NewsletterMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new NewsletterMessage();
        

        if($this->request->isPost && $model->load($this->request->post())) {
            
            $model->slug = uniqid();
            
            $model->recipients_file = UploadedFile::getInstance($model, 'recipients_file');
            
            if($model->uploadRecipientFile() && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        $model->loadDefaultValues();
        
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing NewsletterMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NewsletterMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NewsletterMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return NewsletterMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewsletterMessage::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
