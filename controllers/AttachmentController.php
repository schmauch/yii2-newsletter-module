<?php

namespace schmauch\newsletter\controllers;

use schmauch\newsletter\models\NewsletterAttachment;
use schmauch\newsletter\models\NewsletterMessage;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;

class AttachmentController extends Controller
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
        $message = NewsletterMessage::findOne($id);
        $attachment = new NewsletterAttachment();
        $attachment->file = UploadedFile::getInstanceByName('image');

        //return Json::encode(['size' => [], 'url' => var_export($attachment, true)]);
        $path = $message->getMessageDir() . '/attachments/';

        $infix = '';
        $i = 0;
        
        do {
            $fileName = $path . $attachment->file->baseName . $infix . '.' . $attachment->file->extension;
            $i++;
            $infix = '_' . $i;
        } while(file_exists($fileName));
        
        $attachment->file->name = str_replace($path, '', $fileName);
        $attachment->link('message', $message);
        
        if (!$attachment->validate() || !$attachment->file->saveAs($fileName, false)) {
            return Json::encode(['error', 'Fehler beim Speichern der Datei' . var_export($attachment->errors)]);
        }
                
        $attachment->mode = 1;
        $attachment->save();
        
        $imageSizeInfo = @getimagesize($fileName);
        
        $url = \Yii::getAlias('@web/newsletter/attachment/image/') . '?slug=' . $message->slug . '&img=' . $attachment->file->name;
        return Json::encode([
            'size' => $imageSizeInfo,
            'url'  => $url,
        ]);
    }
    
    
    
    /**
     * //...
     */
    public function actionImage($slug, $img)
    {
        $img = preg_replace('|\?_ignore=[0-9]+|', '', $img);
        $file = \schmauch\newsletter\Module::getInstance()->files_path . $slug . '/attachments/' . $img;
        $type = mime_content_type($file);
                
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        
        
        $response->headers->add('content-type', $type);
        
        $response->data = file_get_contents($file);
        
        return $response;        
    }    
}
