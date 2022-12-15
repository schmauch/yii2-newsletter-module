<?php

use common\models\NewsletterMessage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\NewsletterMessageSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

//print_r($dataProvider->query);
//die();

$this->title = 'Newsletter Messages';
?>
<div class="newsletter-message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Newsletter Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'subject',
            'template',
            'send_date',
            'send_time',
            'mails_sent',
            'blacklisted',
            'completed_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'buttonOptions' => ['class' => 'btn btn-secondary'],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
