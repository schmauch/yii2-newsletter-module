<?php

use yii\helpers\Html;
use yii\helpers\Url;

use bizley\contenttools\ContentTools;

$this->title = 'Bearbeite Newsletter: ' . $model->subject;


?>


<div class="newsletter-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_nav', ['id' => $model->id]) ?>

<?php

ContentTools::begin(['saveEngine' => ['save' => Url::current()]]);

echo $model->html;

ContentTools::end();

?>