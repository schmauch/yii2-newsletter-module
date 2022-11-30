<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\NewsletterMessage $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="newsletter-message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'template')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_date')->input('date') ?>
    
    <?= $form->field($model, 'send_time')->input('time', ['value' => '00:00:00']) ?>

    

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success mr-3']) ?>
        <?= Html::a('Abbrechen', Url::previous(), ['class' => 'btn btn-dark mr-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
