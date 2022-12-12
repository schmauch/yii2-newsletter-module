<?php 
    use yii\widgets\ActiveForm;
    
    $form = ActiveForm::begin(['action' => 'content-tools-image-upload?id=3', 'options' => ['enctype' => 'multipart/form-data']]); 

?>
    
    <input type="file" name="image" />
    <button type="submit">ok</button>
    <?php ActiveForm::end(); ?>
