<?php

use schmauch\newsletter\models\NewsletterBlacklist;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\NewsletterBlacklistSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Newsletter Blacklists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newsletter-blacklist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('E-Mail-Adresse der Blacklist hinzufügen', ['sign-off'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'email:email',
            'added_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, NewsletterBlacklist $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
