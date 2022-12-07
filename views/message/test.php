<?php

use yii\widgets\ActiveForm;

$form = ActiveForm::begin(['action' => '/newsletter/message/content-tools-image-upload?id=1', 'options' => ['enctype' => 'multipart/form-data']]); 

echo'
    <input type="file" name="image" />
    <button type="submit">Los</button>
';

$form::end();