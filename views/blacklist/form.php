<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\NewsletterBlacklist $model */

$this->title = 'Vom Newsletter abmelden | Arthur Girardi AG';
?>
<div class="newsletter-blacklist-create">

    <h1>Vom Newsletter abmelden</h1>

    <div class="newsletter-blacklist-form">
    
        <?php $form = ActiveForm::begin(); ?>
    
        <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

</div>
