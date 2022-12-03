<?php

//echo $form->field($model, 'recipients_config[fileUpload]')->fileInput();

?>

<input type="file" name="NewsletterMessage[recipients_config][fileUpload]" />
<input type="hidden" name="NewsletterMessage[recipients_config][uploadToSlug]" value="<?= $model->slug?>" />

<?php

foreach($model->recipientsObject->getColumns() as $index => $column) {
    echo $form->field($model->recipientsObject, 'columns['.$index.']')->textInput();
}