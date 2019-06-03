<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrdersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orders-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?/*= $form->field($model, 'id') */?>

    <?/*= $form->field($model, 'uid') */?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => 'НАЙТИ ЗАКАЗ',]) ?>

    <?php // $form->field($model, 'filling') ?>

    <?php // $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'deliv_date') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'cost') ?>

    <?php // echo $form->field($model, 'payed') ?>

    <?php // echo $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'update_date') ?>

    <?php // echo $form->field($model, 'manager') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group search-buttons">
        <?= Html::submitButton(Html::img('/img/search.png'), ['class' => 'btn btn-search ']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary', 'onclick'=>"document.location.href = '/orders/index'"]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
