<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\NewsletterMessage $model */

$this->title = 'Bearbeite Newsletter: ' . $model->subject;


?>



<div class="newsletter-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_nav', ['id' => $model->id]) ?>
    
    <?= $this->render('_form', [
        'model' => $model,
        'templates' => $templates,
    ]) ?>

</div>
