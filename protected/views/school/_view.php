<?php
/* @var $this SchoolController */
/* @var $data School */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Id), array('view', 'id'=>$data->Id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Name')); ?>:</b>
	<?php echo CHtml::encode($data->Name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('InitialCost')); ?>:</b>
	<?php echo CHtml::encode($data->InitialCost); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PeriodicalCost')); ?>:</b>
	<?php echo CHtml::encode($data->PeriodicalCost); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Accreditation')); ?>:</b>
	<?php echo CHtml::encode($data->Accreditation); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Website')); ?>:</b>
	<?php echo CHtml::encode($data->Website); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CategoryId')); ?>:</b>
	<?php echo CHtml::encode($data->CategoryId); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('GoogleId')); ?>:</b>
	<?php echo CHtml::encode($data->GoogleId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Latitude')); ?>:</b>
	<?php echo CHtml::encode($data->Latitude); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Longitude')); ?>:</b>
	<?php echo CHtml::encode($data->Longitude); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Address')); ?>:</b>
	<?php echo CHtml::encode($data->Address); ?>
	<br />

	*/ ?>

</div>