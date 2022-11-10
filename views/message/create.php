<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\NewsletterMessage $model */

$this->title = 'Create Newsletter Message';
$this->params['breadcrumbs'][] = ['label' => 'Newsletter Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newsletter-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
