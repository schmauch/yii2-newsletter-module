<?php

use yii\helpers\Html;

$placeholders = $model->getPlaceholders();

$template = '<div class="col"> <label for="Recipient-%1$s-%2$s">%2$s</label> </div> <div class="col"> <input type="text" id="Recipient-%1$s-%2$s" name="NewsletterMessage[recipients_config][recipients][%1$s][%2$s]" value="%3$s" /> </div>';

$script = '
    function addRecipient(id) {
        $("#recipients").append(\'';
foreach($placeholders as $placeholder) {
    $script .= sprintf($template, '\' + id + \'', $placeholder, '');
}

$script .= '\');
}';

$this->registerJs($script, \yii\web\View::POS_HEAD);

$this->registerCss('#recipients .row:nth-of-type(odd) { background-color: rgba(0,0,0,0.05); }');

echo '<div id="recipients">';

echo '
<div class="form-group">' .
    Html::button('Empfänger hinzufügen', [
        'class' => 'btn btn-primary ml-0 my-3', 
        'onclick' => 'addRecipient($("#recipients .row").length)'
    ]) .
'</div>';
 
foreach($model->recipientsObject->recipients as $index => $recipient) {
    echo '<div class="row py-2">';
    foreach($placeholders as $placeholder) {
        printf($template, $index, $placeholder, $recipient[$placeholder] ?? '');
    }
    echo '</div>';
}

echo '</div>';
