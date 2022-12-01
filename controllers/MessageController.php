<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\models\NewsletterMessage;
use schmauch\newsletter\models\NewsletterMessageSearch;
use schmauch\newsletter\models\RecipientsInterface;
use schmauch\newsletter\jobs\SendMailJob;

use gri3li\yii2csvdataprovider\CsvDataProvider;

use yii\filters\VerbFilter;
use yii\helpers\Url;
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
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'content-tools-image-upload' => \bizley\contenttools\actions\UploadAction::className(),
            'content-tools-image-insert' => \bizley\contenttools\actions\InsertAction::className(),
            'content-tools-image-rotate' => \bizley\contenttools\actions\RotateAction::className(),
        ];
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
        Url::remember();
        $searchModel = new NewsletterMessageSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        

        if ($this->request->isPost) {
            
            $model->slug = uniqid();
            
            if ($model->load($this->request->post()) && $model->save()) {

                $this->initFileStructure($model->slug);
                
                \Yii::$app->session->setFlash('Newsletter erstellt.');              
                
                return $this->redirect(['update', 'id' => $model->id]);
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
            \Yii::$app->session->setFlash('success', 'Newsletter gespeichert.');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    
    /**
     * Edit the html message
     */
    public function actionEditHtml($id)
    {
        $model = $this->findModel($id);
        
        $htmlFile = $model->getHtmlFile();
        
        if($this->request->isPost) {
            $html = $this->request->post('contentTools0');
            if (false === file_put_contents($htmlFile, $html)) {
                throw new Exception('Fehler beim Schreiben des HTML-Inhalts.');
                return $this->asJson(['errors' => ['write' => 'Fehler beim Schreiben des HTML-Inhalts']]);
            }
            return $this->asJson(true);
        }
        
        $model->html = file_get_contents($htmlFile);
        return $this->render('edit-html', ['model' => $model]);
    }
    
    
    
    /**
     * Edit the plain text message
     */
    public function actionEditText($id, $loadFromHtml = false)
    {
        $model = $this->findModel($id);
        
        $htmlFile = $model->getHtmlFile();
        $textFile = substr($htmlFile, 0, -4) . 'txt';
        
        if($this->request->isPost) {
            $text = $this->request->post('NewsletterMessage')['text'];
            if (false === file_put_contents($textFile, $text)) {
                throw new Exception('Fehler beim Schreiben des Text-Inhalts.');
                return $this->render('edit-text', ['model' => $model]);
            }
            return $this->redirect(['edit-text', 'id' => $id]);
        }
        
        if ($loadFromHtml) {
            $model->text = preg_replace('/\t+|\n\ +/', '', strip_tags(file_get_contents($htmlFile)));
        } else {
            $model->text = file_get_contents($textFile);
        }
        
        return $this->render('edit-text', ['model' => $model]);
    }
    
    
    
    /**
     *
     */
    public function actionChooseRecipients($id)
    {
        $model = $this->findModel($id);
        
        if ($this->request->isPost) {
            
            if ($model->load($this->request->post()) && $model->save()) {
                if(!empty($model->recipients_config)) {
                    return $this->redirect(['view-recipients', 'id' => $model->id]);
                } else {
                    return $this->redirect(['choose-recipients', 'id' => $model->id]);
                }
            } else {
                \Yii::$app->session->setFlash('error', 'Fehler beim Speichern.' . 
                    implode("<br>", $model->errors)
                );
            }
        }
        
        // Scan dir for possible options
        $dir = $this->module->getBasePath().'/models/recipients/';
        $objects = scandir($dir);
        foreach($objects as $object) {
            if (substr($object, 0, 1) == '.' || substr($object, -1) == '~') {
                continue;
            }
            $options[substr($object, 0, -4)] = substr($object, 0, -14);
        }
        
        return $this->render('choose-recipients', [
            'model' => $model, 
            'options' => $options]);
    }
    
    
    public function actionViewRecipients($id) 
    {
        $model = $this->findModel($id);
        
        $namespace = preg_replace('/controllers$/', 'models\\recipients\\', __NAMESPACE__);
        $class = $namespace . $model->recipients_object;
        $config = !empty($model->recipients_config) ? unserialize($model->recipients_config) : [];
        
        if(class_exists($class)) {
            $recipients_object = new $class($config);
        }
        
        $dataProvider = $recipients_object->getDataProvider();
        $dataProvider->pagination->pagesize = 1;
        
        $columns = $recipients_object->getColumns() ?? [];
        
        return $this->render('recipients', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
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
    
    /**
     * Initializes the file structure
     */
    protected function initFileStructure($slug)
    {
        $path = $this->module->params['files_path'];
        
        if (!is_dir($path)) {
            throw new \yii\base\InvalidConfigException('Parameter `files_path` leads to an  non existing directory.');
        }
        
        $path .= $slug . '/';
        
        if(!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new Exception('Verzeichnis konnte nicht erstellt werden.'); 
            }
            if (!is_writable($path) && !chmod($path, 0777)) {
                throw new Exception('Verzeichnis ist nicht beschreibbar.');
            }
        }
                
        $htmlFile = $path . 'message.html';
        $textFile = $path . 'message.txt';
        $attachmentsDir = $path . 'attachments';
        
        // create an empty html file
        if (!is_file($htmlFile)) {
            if (!touch($htmlFile) || !chmod($htmlFile, 0666)) {
                throw new Exception('Html File konnte nicht erstellt werden.');
            }
        }
        
        // create an empty text file
        if (!is_file($textFile)) {
            if (!touch($textFile) || !chmod($textFile, 0666)) {
                throw new Exception('Text File konnte nicht erstellt werden.');
            }
        }
        
        // create the directory for attachments
        if (!is_dir($attachmentsDir)) {
            if (!mkdir($attachmentsDir, 0777, true)) {
                throw new Exception('Attachmens-Verzeichnis konnte nicht erstellt werden.');
            }
            if (!is_writable($attachmentsDir) && !chmod($attachmentsDir, 0777)) {
                throw new Exception('Verzeichnis ist nicht beschreibbar.');
            }
        }
        
    }    
}
