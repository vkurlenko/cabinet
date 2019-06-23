<?php
//namespace app\models;

//use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

$session = Yii::$app->session;

//debug($model);

//$model = new FreeOrderForm();
$model->deliv_date = $session['free-order']['deliv_date'] ? $session['free-order']['deliv_date'] : '';
$model->description = $session['free-order']['description'] ? $session['free-order']['description'] : '';
$model->address = $session['free-order']['address'] ? $session['free-order']['address'] : '';

?>

<h1>Заказать торт по эскизу или фотографии</h1>

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]);?>

<div class="orders-form row">
    <div class="col-md-8">


    <div class="datepicker-field">
        <!--<label class="control-label" for="freeorderform-deliv_date">Дата поставки</label>-->
        <?=$form->field($model, 'deliv_date')->widget(DatePicker::classname(),[ 'name'  => 'deliv_date', 'language' => 'ru', 'dateFormat' => 'php:Y-m-d', 'clientOptions' => [
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

        <?php

        if($session['free-order-images']){
            $arr = unserialize($session['free-order-images']);
            //debug($arr);
            foreach($arr as $image){
                //echo $image['file_name'];
                echo Html::img('/upload/temp/'.$image['file_name'], ['width' => 115]);
            }
        }
        ?>

    <?=$form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); ?>
    <?=$form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label(''); ?>
    <?=$form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label(''); ?>

        <div class="form-group" style="text-align: center">
            <?= Html::submitButton('Отправить', ['class' => 'btn ext-btn']) ?>
        </div>
    </div>

</div>

<?php ActiveForm::end();?>
