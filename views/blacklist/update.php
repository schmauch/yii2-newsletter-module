<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\NewsletterBlacklist $model */

$this->title = 'Update Newsletter Blacklist: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Newsletter Blacklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="newsletter-blacklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
