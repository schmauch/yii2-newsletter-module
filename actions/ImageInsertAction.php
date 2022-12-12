<?php

namespace schmauch\newsletter\actions;

use Exception;
use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;

class ImageInsertAction extends Action
{
    public $uploadDir;
    public $viewPath;
    
    public function run()
    {
        if (!Yii::$app->request->isPost) {
            return Json::encode(['errors' => ['POST parameters are missing!']]);
        }
        
        $slug = $_GET['slug'];
        
        if (empty($slug)) {
            throw new InvalidParamException('Parameter "slug" is missing!');
        }
        
        $this->uploadDir = \schmauch\newsletter\Module::getInstance()->params['files_path'] . $slug . '/attachments/';
        
        $this->viewPath = \Yii::getAlias('@web/newsletter/attachment/image/?slug=' . $slug . '&img=');

        try {
            $data = Yii::$app->request->post();

            if (empty($data['url'])) {
                throw new InvalidParamException('Parameter "url" is missing!');
            }

            $url = trim($data['url']);

            $imageName = substr($url, strrpos($url, '/') + 1);
            
            if (strpos($imageName, '?_ignore=') !== false) {
                $imageName = substr($imageName, 0, strpos($imageName, '?_ignore='));
            }
            
            $imageName = str_replace('?slug=' . $slug . '&img=', '', $imageName);
            
            $imagePath = FileHelper::normalizePath(
                Yii::getAlias(FileHelper::normalizePath($this->uploadDir, '/'))
                . DIRECTORY_SEPARATOR
                . $imageName
            );

            $imageSizeInfo = @getimagesize($imagePath);

            if ($imageSizeInfo === false) {
                throw new InvalidParamException('Parameter "url" seems to be invalid!' . $imageName);
            }

            if (!empty($data['crop'])) {
                $crop = explode(',', $data['crop']);

                if (count($crop) !== 4) {
                    throw new InvalidParamException('Parameter "crop" is invalid!');
                }

                $positions = [];

                foreach ($crop as $position) {
                    $position = trim($position);

                    if (!is_numeric($position) || $position < 0 || $position > 1) {
                        throw new InvalidParamException('Parameter "crop" contains invalid value!');
                    }

                    $positions[] = $position;
                }

                list($width, $height) = $imageSizeInfo;

                Image::crop(
                    $imagePath,
                    floor($width * $positions[3] - $width * $positions[1]),
                    floor($height * $positions[2] - $height * $positions[0]),
                    [
                        floor($width * $positions[1]),
                        floor($height * $positions[0])
                    ]
                )->save($imagePath);
            }

            return Json::encode([
                'size' => @getimagesize($imagePath), // not using $imageSizeInfo because it's new size
                'url' => Yii::getAlias(FileHelper::normalizePath($this->viewPath, '/') . $imageName),
                'alt' => $imageName
            ]);

        } catch (Exception $e) {
            return Json::encode(['errors' => [$e->getMessage()]]);
        }
        

    }
}