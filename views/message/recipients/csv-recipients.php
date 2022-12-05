<div>
    <label for="csv-upload">Datei hoch laden</label>
    <input id="csv-upload" type="file" name="NewsletterMessage[recipients_config][fileUpload]" />
    <input type="hidden" name="NewsletterMessage[recipients_config][uploadToSlug]" value="<?= $model->slug?>" />
</div>
<div>
    <input id="firstLineToColumns" 
        type="checkbox" 
        name="NewsletterMessage[recipients_config][firstLineToColumns]" 
        value="yes"
    <label for="firstLineToColumns">Erste Zeile als Spaltennamen verwenden.</label> 
</div>

<?php

/*
foreach($model->recipientsObject->getColumns() as $index => $column) {
    echo $form->field($model->recipientsObject, 'columns['.$index.']')->textInput();
}
*/