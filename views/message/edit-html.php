<?php

use yii\helpers\Html;
use yii\helpers\Url;

use bizley\contenttools\ContentTools;

$this->title = 'Bearbeite Newsletter: ' . $model->subject;

$this->registerCss('
    .ct-ignition {
        visibility: hidden;
    }
');

echo '<div class="newsletter-message-update">';

echo '<h1>' . $this->title . '</h1>';

echo $this->render('_nav', ['id' => $model->id]);

echo Html::button('Html bearbeiten', ['class' => 'btn btn-primary mb-2', 'onclick' => 'ContentTools.EditorApp.get().start();']);

echo '<div class="row">';
ContentTools::begin(['saveEngine' => ['save' => Url::current()], 'options' => ['class' => 'col-10 border']]);

echo $model->html;

ContentTools::end();

echo '<div class="col-2"><h5>Verfügbare Platzhalter:</h5><ul>';

foreach($placeholders as $placeholder) {
    echo '<li>[[' . $placeholder . ']]</li>';
}

echo '</ul></div></div>';



echo '<div class="form-group">' .
    Html::button('speichern', ['class' => 'btn btn-success', 'onclick' => 'ContentTools.EditorApp.get().stop(true);']) .
    Html::a('abbrechen', Url::current(), ['class' => 'm-3 btn btn-dark']) .
    '</div>';
    
    