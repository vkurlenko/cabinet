<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pay */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'orderNumber')->textInput() ?>

    <?= $form->field($model, 'orderId')->textInput() ?>

    <?= $form->field($model, 'errorCode')->textInput() ?>

    <?= $form->field($model, 'errorMessage')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'datetime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn ext-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
