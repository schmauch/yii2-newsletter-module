<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\NewsletterMessage $model */

$this->title = 'Create Newsletter Message';
//$this->params['breadcrumbs'][] = ['label' => 'Newsletter Messages', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newsletter-message-create">

    <h1>Newsletter erstellen</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
