<?php
echo Yii::app()->user->getFlash("success-msg");
?>


<style type="text/css">
    <!--
    .style1 {color: #0000FF}
    -->
</style>
<h3>Isilah AHP Kriteria Sebagai Berikut: </h3>
<p>Untuk menilai perbandingan tingkat kepentingan satu elemen terhadap elemen lainnya digunakan skala Saaty 1 sampai 9.</p>
<p>Keterangan sebagai berikut.</p>
<table width="399" border="0">
    <tr>
        <td width="77"><strong>No</strong></td>
        <td width="312"> <strong>Keterangan </strong></td>
    </tr>
    <tr>
        <td>1</td>
        <td>Sama pentingnya dibandingkan yang lain </td>
    </tr>
    <tr>
        <td>3</td>
        <td>Moderat (cukup) penting dibandingkan yang lain </td>
    </tr>
    <tr>
        <td>5</td>
        <td>Kuat pentingnya dibandingkan yang lain </td>
    </tr>
    <tr>
        <td>7</td>
        <td>Sangat kuat pentingnya dibandingkan yang lain </td>
    </tr>
    <tr>
        <td>9</td>
        <td>Ekstrim pentingya dibandingkan yang lain </td>
    </tr>
    <tr>
        <td>2. 4. dan 6 </td>
        <td> Nilai diantara 2 nilai yang berdekatan </td>
    </tr>

</table>
<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'ahp-form',
    ));
    ?>

    <?php
    $this->widget('application.extensions.EMultiRatingView', array(
        'Options' => $data_ahp,
        'Debug' => false,
        'Template' => "{emrview}",
        'TemplateRow' => "Lebih memilih {option1} atau {option2}? <br />{emrrow}",
        'WithFormWrapper' => false,
        'FormId' => 'ahp-form',
        'IncompleteErrorMessage' => 'Data harus diisi dengan lengkap',
        'UseClassicView' => false,
        'DistanceBetweenOption' => 100
    ));
    ?>

    <div class="row buttons">
        <?php // echo CHtml::button('Back', array("onClick" => "javascript:history.back(-1)"));  ?>
        <?php echo CHtml::submitButton('Process'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->