<?php
namespace app\models;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
?>

<h1>Заказать торт по эскизу или фотографии</h1>

<?php $form = ActiveForm::begin();?>

<div class="orders-form row">
    <div class="col-md-8">

    <?/*=$form->field($model, 'deliv_date')->textInput(['id' => 'customer_date'])*/?>

    <div class="datepicker-field">
        <!--<label class="control-label" for="freeorderform-deliv_date">Дата поставки</label>-->
        <?=$form->field($model, 'deliv_date')->widget(DatePicker::classname(),[ 'name'  => 'deliv_date', 'value'  => '', 'language' => 'ru', 'dateFormat' => 'php:Y-m-d', 'clientOptions' => [
            'changeMonth' => 'true',
            'firstDay' => '1',
            'showOn' => "button",
            'buttonImage' => "http://andreychef/img/calendar.png",
            'buttonImageOnly' => true,
        ] ] )->label('Дата поставки')
        ?>
    </div>

    <div style="clear: both"></div>

    <?=$form->field($model, 'description')->textarea(['rows' => 5])?>
    <?=$form->field($model, 'address')->textarea(['rows' => 2])?>
    <?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

</div>

<?php ActiveForm::end();?>
