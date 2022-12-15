<?php

namespace schmauch\newsletter\controllers;

use gri3li\yii2csvdataprovider\CsvDataProvider;

use schmauch\newsletter\models\NewsletterAttachment;
use schmauch\newsletter\models\NewsletterBlacklist;
use schmauch\newsletter\models\NewsletterMessage;
use schmauch\newsletter\models\NewsletterMessageSearch;
use schmauch\newsletter\models\RecipientsInterface;

use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\helpers\Json;
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
    
    public function actionFoo()
    {
        $html = '@schmauch/newsletter/mail/638efefea9cd9/message.html';
        $text = '@schmauch/newsletter/mail/638efefea9cd9/message.txt';
        
        $this->layout = '@schmauch/newsletter/views/layouts/default/html';
        
        return $this->render($html);
    }
    
    /**
     * Lists all NewsletterMessage models.
     *
     * @return string
     */
    public function actionIndex($archive = false)
    {
        Url::remember();
         
        $searchModel = new NewsletterMessageSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        if (!$archive) {
            $dataProvider->query->andWhere(['completed_at' => null]);
        }

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
            'templates' => $this->getTemplates(),
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
            'templates' => $this->getTemplates(),
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
            
            // remove ?_ignore=<timestamp> from img url
            $html = preg_replace('/\?_ignore=[0-9]{13}/', '', $html);
            
            // replace img src with correct embed string for swift_mailer
            $search = '|/newsletter/attachment/image/\?slug=' . $model->slug . '&amp;img=(.+?)"|';
            $replace = '<?= $message->embed(\'' . $model->getMessageDir() . '/attachments/$1\'); ?>";';
            $html = preg_replace($search, $replace, $html);
            
            // replace placeholders with their variables
            $html = preg_replace('|\[\[(.+?)\]\]|', '<?= \$$1 ?>', $html);
            
            if (false === file_put_contents($htmlFile, $html)) {
                throw new Exception('Fehler beim Schreiben des HTML-Inhalts.');
                return $this->asJson(['errors' => ['write' => 'Fehler beim Schreiben des HTML-Inhalts']]);
            }
            return $this->asJson(true);
        }
        
        $model->html = file_get_contents($htmlFile);
        
        // replace embed string with img src
        $model->html = preg_replace('|<\?= \$message->embed\(\'/.+/(.+?)\'\); \?>"|', '/newsletter/attachment/image/\?slug=' . $model->slug . '&amp;img=$1"', $model->html);
        
        // replace variables with their placeholders
        $model->html = preg_replace('|<\?= \$(.+?) \?>|', '[[$1]]', $model->html);
        
        $placeholders = !empty($model->recipientsObject) ? $model->recipientsObject->getColumns() : [];
        
        return $this->render('edit-html', [
            'model' => $model,
            'placeholders' => $placeholders,
        ]);
    }
    
    
    
    /**
     * Edit the plain text message
     */
    public function actionEditText($id, $loadFromHtml = false)
    {
        $model = $this->findModel($id);
        
        $textFile = $model->getTextFile();
        
        if($this->request->isPost) {
            $text = $this->request->post('NewsletterMessage')['text'];
            $text = preg_replace('|\[\[(.+?)\]\]|', '<?= \$$1 ?>', $text);
            if (false === file_put_contents($textFile, $text)) {
                throw new Exception('Fehler beim Schreiben des Text-Inhalts.');
                return $this->render('edit-text', ['model' => $model]);
            }
            return $this->redirect(['edit-text', 'id' => $id]);
        }
        
        if ($loadFromHtml) {
            $htmlFile = $model->getHtmlFile();
            $model->text = file_get_contents($htmlFile);
        } else {
            $model->text = file_get_contents($textFile);
        }
        
        $model->text = preg_replace('|<\?= \$(.+?) \?>|', '[[$1]]', $model->text);
        $model->text = preg_replace('/\t+|\n\ +/', '', strip_tags($model->text));
        
        //... Das müsste nicht hier passieren, weil auch schon beim HTML!
        $placeholders = !empty($model->recipientsObject) ? $model->recipientsObject->getColumns() : [];
        
        return $this->render('edit-text', [
            'model' => $model,
            'placeholders' => $placeholders,
        ]);
    }
    
    
    
    /**
     *
     */
    public function actionAttachments($id)
    {
        $model = $this->findModel($id);
        
        $newAttachment = new NewsletterAttachment();
        
        if ($this->request->isPost) {
            if ($newAttachment->load($this->request->post())) {
                $newAttachment->link('message', $model);
                $newAttachment->file = UploadedFile::getInstance($newAttachment, 'file');
                if (!$newAttachment->upload()) {
                    \Yii::$app->session->addFlash('error', 'Datei konnte nicht hochgeladen werden');
                }
                //$newAttachment->file = $newAttachment->file->basename . $newAttachment->file->extension;
                $newAttachment->save();
            }
            
            return $this->redirect(['attachments', 'id' => $id]);
        }
        
        return $this->render('attachments', [
            'model' => $model,
            'newAttachment' =>$newAttachment,
        ]);
    }
    
    
    
    /**
     * Configure recipients object
     */
    public function actionChooseRecipients($id)
    {
        $model = $this->findModel($id);
        
        if ($this->request->isPost) {

            $newClass = $this->request->post('NewsletterMessage')['recipients_class'] ?? false;
            
            
            
            if ($newClass && $newClass != $model->recipients_class) {
                $model->recipients_class = $newClass;
                $model->recipients_config = null;
            } else {
                $configData = $this->request->post('NewsletterMessage')['recipients_config'] ?? false;
                if ($configData) {
                    $recipientsObject = $model->getRecipientsObject($configData);
                    $model->recipients_config = serialize($recipientsObject->attributes);
                }
            }
            if(!$model->save()) {
                foreach($model->getErrors() as $attribute => $error) {
                    \Yii::$app->session->addFlash('error', implode('<br>', $error));
                }
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
        
        return $this->render('recipients', [
            'options' => $options,
            'model' => $model, 
        ]);
    }
    
    
    /**
     * Check if newsletter is ready to send
     */
    public function actionReadyToSend($id)
    {
        $model = $this->findModel($id);
        
        $checks = $model->isReadyToSend($model);
              
        return $this->render('ready-to-send', [
            'checks' => $checks,
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
    
    /**
     * Initializes the file structure
     */
    protected function initFileStructure($slug)
    {
        $path = $this->module->files_path;
        
        if (!is_dir($path)) {
            throw new \yii\base\InvalidConfigException(
                'Parameter `files_path` leads to an  non existing directory.'
            );
        }
        
        $path .= $slug . '/';
        
        if(!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new Exception('Verzeichnis konnte nicht erstellt werden.'); 
            }
        }
        
        if (!is_writable($path) && !chmod($path, 0777)) {
            throw new Exception('Verzeichnis ist nicht beschreibbar.');
        }
                
        $htmlFile = $path . 'message.html';
        $textFile = $path . 'message.txt';
        $logFile = $path . 'queue.log';
        
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
        
        // create an empty log file
        if (!is_file($logFile)) {
            if (!touch($logFile) || !chmod($logFile, 0666)) {
                throw new Exception('Log File konnte nicht erstellt werden.');
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
    
        
    
    protected function getTemplates()
    {
        $templatePath = realpath(\Yii::getAlias('@schmauch/newsletter/') . 
            $this->module->template_path) . '/';
            
        $templates = scandir($templatePath);
        
        $available = [];
        
        foreach($templates as $key => $template) {
            if (substr($template, 0, 1) != '.' && is_dir($templatePath . $template)) {
                $available[$template] = $template;
            }
        }
        
        return $available;
    }
}
