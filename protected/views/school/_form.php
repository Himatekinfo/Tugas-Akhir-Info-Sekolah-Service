<?php
/* @var $this SchoolController */
/* @var $model School */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'school-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'Name'); ?>
        <?php echo $form->textField($model, 'Name', array('size' => 60, 'maxlength' => 150)); ?>
        <?php echo $form->error($model, 'Name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'InitialCost'); ?>
        <?php echo $form->textField($model, 'InitialCost', array('size' => 20, 'maxlength' => 20)); ?>
        <?php echo $form->error($model, 'InitialCost'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'PeriodicalCost'); ?>
        <?php echo $form->textField($model, 'PeriodicalCost', array('size' => 20, 'maxlength' => 20)); ?>
        <?php echo $form->error($model, 'PeriodicalCost'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'Accreditation'); ?>
        <?php echo $form->dropDownList($model, 'Accreditation', array('A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D')); ?>
        <?php echo $form->error($model, 'Accreditation'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'Website'); ?>
        <?php echo $form->textField($model, 'Website', array('size' => 60, 'maxlength' => 100)); ?>
        <?php echo $form->error($model, 'Website'); ?>
    </div>
    <?php /*
      <div class="row">
      <?php echo $form->labelEx($model, 'CategoryId'); ?>
      <?php echo $form->dropDownList($model, 'CategoryId', CHtml::listData(Lookup::model()->findAll(), 'Id', 'LookupValue')); ?>
      <?php echo $form->error($model, 'CategoryId'); ?>
      </div>
     */ ?>
    <div>
        <?php echo $form->hiddenField($model, 'CategoryId', array("value" => 4)); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'GoogleId'); ?>
        <?php echo $form->textField($model, 'GoogleId', array('size' => 41, 'maxlength' => 41)); ?>
        <?php echo $form->error($model, 'GoogleId'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'Latitude'); ?>
        <?php echo $form->textField($model, 'Latitude'); ?>
        <?php echo $form->error($model, 'Latitude'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'Longitude'); ?>
        <?php echo $form->textField($model, 'Longitude'); ?>
        <?php echo $form->error($model, 'Longitude'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'Address'); ?>
        <?php echo $form->textField($model, 'Address', array('size' => 60, 'maxlength' => 5000)); ?>
        <?php echo $form->error($model, 'Address'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->