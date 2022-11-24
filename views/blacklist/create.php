<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\NewsletterBlacklist $model */

$this->title = 'Create Newsletter Blacklist';
$this->params['breadcrumbs'][] = ['label' => 'Newsletter Blacklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newsletter-blacklist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="newsletter-blacklist-form">
    
        <?php $form = ActiveForm::begin(); ?>
    
        <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

</div>
