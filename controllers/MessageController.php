<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\models\NewsletterMessage;
use schmauch\newsletter\models\NewsletterMessageSearch;
use schmauch\newsletter\models\NewsletterAttachment;
use schmauch\newsletter\models\RecipientsInterface;
use schmauch\newsletter\jobs\SendMailJob;

use gri3li\yii2csvdataprovider\CsvDataProvider;

use yii\filters\VerbFilter;
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
            ]
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            //'content-tools-image-upload' => \bizley\contenttools\actions\UploadAction::className(),
            'content-tools-image-insert' => \schmauch\newsletter\actions\ImageInsertAction::className(),
            'content-tools-image-rotate' => \bizley\contenttools\actions\RotateAction::className(),
        ];
    }
    
    public function actionContentToolsImageUpload($id)
    {
        $model = $this->findModel($id);
        $attachment = new NewsletterAttachment();
        $attachment->file = UploadedFile::getInstanceByName('image');

        //return Json::encode(['size' => [], 'url' => var_export($attachment, true)]);
        $path = $model->getMessageDir() . '/attachments/';

        $infix = '';
        $i = 0;
        
        do {
            $fileName = $path . $attachment->file->baseName . $infix . '.' . $attachment->file->extension;
            $i++;
            $infix = '_' . $i;
        } while(file_exists($fileName));
        
        $attachment->link('message', $model);
        
        if (!$attachment->validate() || !$attachment->file->saveAs($fileName, false)) {
            return Json::encode(['error', 'Fehler beim Speichern der Datei' . var_export($attachment->errors)]);
        }
                
        $attachment->file->name = str_replace($path, '', $fileName);
        $attachment->save();
        
        $imageSizeInfo = @getimagesize($fileName);
        
        $url = \Yii::getAlias('@web/newsletter/message/image/') . '?slug=' . $model->slug . '&img=' . $attachment->file->name;
        return Json::encode([
            'size' => $imageSizeInfo,
            'url'  => $url,
        ]);
    }
    
    
        
    /**
     * //... evtl. auslagern in Action oder Behavior
     */
    /*public function actionContentToolsImageInsert()
    {
        $file = '/var/www/html/duck.jpg';
        $imageSizeInfo = @getimagesize($file);
        //$url = 'data:image/png;base64, ' . base64_encode(file_get_contents($file));
        $url = \Yii::getAlias('@web/newsletter/message/image/') . '?img=duck.jpg';
        return Json::encode([
            'size' => $imageSizeInfo,
            'url'  => $url,
            'alt' => 'alt',
        ]);
    }*/
    


    public function actionImage($slug, $img)
    {
        $img = preg_replace('|\?_ignore=[0-9]+|', '', $img);
        $file = \schmauch\newsletter\Module::getInstance()->params['files_path'] . $slug . '/attachments/' . $img;
        $type = mime_content_type($file);
                
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        
        
        $response->headers->add('content-type', $type);
        
        $response->data = file_get_contents($file);
        
        return $response;        
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
            $html = preg_replace('/\?_ignore=[0-9]{13}/', '', $html);
            
            $search = '|/newsletter/message/image/\?slug=' . $model->slug . '&amp;img=(.+?)"|';
            $replace = '<?= $message->embed(\'' . $model->getMessageDir() . '/attachments/$1\'); ?>";';
            $html = preg_replace($search, $replace, $html);
            if (false === file_put_contents($htmlFile, $html)) {
                throw new Exception('Fehler beim Schreiben des HTML-Inhalts.');
                return $this->asJson(['errors' => ['write' => 'Fehler beim Schreiben des HTML-Inhalts']]);
            }
            return $this->asJson(true);
        }
        
        $model->html = file_get_contents($htmlFile);
        $model->html = preg_replace('/\?_ignore=[0-9]{13}/', '', $model->html);
        $model->html = preg_replace('|<\?= \$message->embed\(\'(.+?)\'\); \?>"|', '/newsletter/message/image/\?slug=' . $model->slug . '&amp;img=$1"', $model->html);
        return $this->render('edit-html', [
            'model' => $model,
            'placeholders' => $model->getPlaceholders(),
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
            if (false === file_put_contents($textFile, $text)) {
                throw new Exception('Fehler beim Schreiben des Text-Inhalts.');
                return $this->render('edit-text', ['model' => $model]);
            }
            return $this->redirect(['edit-text', 'id' => $id]);
        }
        
        if ($loadFromHtml) {
            $htmlFile = $model->getHtmlFile();
            $model->text = preg_replace('/\t+|\n\ +/', '', strip_tags(file_get_contents($htmlFile)));
        } else {
            $model->text = file_get_contents($textFile);
        }
        
        return $this->render('edit-text', [
            'model' => $model,
            'placeholders' => $model->getPlaceholders(),
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
        //print_r($_POST);
        //die();

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
            
            $model->save();
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
     * Check if newsletter is ready to send and queue it up
     */
    public function actionReadyToSend($id)
    {
        $model = $this->findModel($id);
        
        $checks = $this->isReadyToSend($model);
              
        return $this->render('ready-to-send', [
            'checks' => $checks,
            'model' => $model,
        ]);
    }
    
    
    
    /**
     * Adds all newsletter messages to queue
     */
     public function actionQueue($id)
     {
        $model = $this->findModel($id);
        $checks = $this->isReadyToSend($model);
        
        // show errors if newsletter isn't ready to send
        if (!array_product($checks)) {
            return $this->render('ready-to-send', [
                'model' => $model,
                'checks' => $checks,
            ]);
        }
        
        $dataProvider = $model->recipientsObject->getDataProvider();
        $dataProvider->getPagination()->setPageSize($this->module->params['messages_limit']);
        
        // prepare mailer
        $mailer = \Yii::$app->mailer;
        
        $mailer->viewPath = $model->getMessageDir();
        $mailer->htmlLayout = '@schmauch/newsletter/' . 
            $this->module->params['template_path'] . $model->template . '/html';
            
        // Add attachments
        $embed = [];
        $attach = [];
        foreach($model->newsletterAttachments as $attachment) {
            $file = $model->getMessageDir() . 'attachments/' . $attachment->file;
            echo $file;
            if (!is_readable($file)) {
                echo 'nicht lesbar!';
                die();
            }
            if ($attachment->mode) {
                $embed[$attachment->file] = $file;
            } else {
                $attach['$attachment->file'] = $file;
            }
        }
        
        $message = $mailer->compose([
                    'html' => 'message.html',
                    'txt' => 'message.txt',
                    'embed-email' => $embed,
                    'attach' => $attach,
                ]);
        
        $message->setFrom('r.schmutz@girardi.ch');
        $message->setSubject($model->subject);
        
        
        $pages = ceil($dataProvider->getTotalCount() / 
            $dataProvider->getPagination()->getPageSize());
            
        for($i=0;$i<$pages;$i++) {
            
            $dataProvider->getPagination()->setPage($i);
            $dataProvider->refresh();
            
            foreach($dataProvider->getModels() as $recipient) {
                    
                
                if(is_object($recipient)) {
                    $message->setTo($recipient->email);
                } else {
                    $message->setTo($recipient['email']);
                }
                
                $message->send();
                echo "Nachricht verschickt";
                
            }
        }
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
    
    
    
    /**
     * //...
     */
    protected function isReadyToSend($model)
    {
        $checks['recipients'] = $model->recipientsObject->dataProvider->getTotalCount() > 0;
        
        $checks['html'] = !empty(file_get_contents($model->getHtmlFile()));
        $checks['text'] = !empty(file_get_contents($model->getTextFile()));
        
        $columnNames = [];
        $columns = $model->recipientsObject->getColumns();
        
        if (count($columns) === count($columns, true)) {
            $columnNames = $columns;
        } else {
            foreach($columns as $index => $column) {
                $columnNames[$index] = $column['header'] ?? '';
            }
        }
        
        $checks['placeholders'] = empty(
            array_diff($model->getPlaceholders(),
            $columnNames
        ));
        
        $checks['attachments'] = true;
        
        foreach($model->newsletterAttachments as $attachment)
        {
            $checks['attachments'] *= is_readable($model->getMessageDir() . 'attachments/' . $attachment->file);
        }
         
        
        return $checks;
    }
}
