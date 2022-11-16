<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\models\NewsletterMessage;
use schmauch\newsletter\models\NewsletterMessageSearch;
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
        $recipients = [
            'Roger Schmutz' => 'mail@roger-schmutz.ch',
            'Rotscher Schmutz' => 'info@freihand.ch',
            'irgendwas' => 'keine@gültige-adresse',
            'Roger' => 'info@schmutzkampagne.ch',
        ];
        
        $queuemailer = \Yii::$app->queuemailer;
        
        foreach($recipients as $recipient) {
            $message = $queuemailer->compose()
                ->setFrom('roger@schmau.ch')
                ->setTo($recipient)
                ->setSubject('das ist ein erster Test')
                ->setTextBody('Hier kommt ein erster Test!');
            $queuemailer->send($message);
        }
        echo count($recipients) . ' Mails wurden der Queue (Job ID: ' . $queuemailer->getLastJobId() . ') hinzugefügt.';
    }
    
    public function actionBar()
    {
        if(\Yii::$app->mailqueue->process()) {
            echo "Mails erfolgreich verschickt";
            return;
        }
        
        echo "Fehler! Mails konnten nicht verschickt werden";
        return;
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
