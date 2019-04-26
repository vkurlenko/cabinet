<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */
/* @var $form yii\widgets\ActiveForm */
//debug(\app\controllers\OrdersController::getClients());
use kartik\date\DatePicker;

$datepicker = [
    'name' => 'Выберите дату',
    'value' => date('d-M-Y', strtotime('+2 days')),
    'options' => ['placeholder' => 'Выберите дату ...'],
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]
];

//$products = dropDownList(\app\controllers\OrdersController::getProducts();
$arr = \app\controllers\OrdersController::getProducts();
foreach($arr as $k => $v){
	$products[$k] = $v['name'];
}
?>

<div class="orders-form row">
	<div class="col-md-8">
		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'uid')->dropDownList(\app\controllers\OrdersController::getPersons('user'), ['prompt' => 'Выберите клиента']) ?>
		
		<div class="form-group">
			<label class="control-label">E-mail</label>
			<span><?=\app\controllers\OrdersController::getUserEmail($model->uid)?></span>
		</div>
		
		<?= $form->field($model, 'deliv_name')->textInput() ?>

		<?= $form->field($model, 'deliv_phone')->textInput() ?>		

		<?= $form->field($model, 'name')->dropDownList($products, ['prompt' => 'Выберите торт']) ?>

		<?= $form->field($model, 'filling')->dropDownList(\app\controllers\OrdersController::getFills(), ['prompt' => 'Выберите начинку']) ?>

		<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

		<?= $form->field($model, 'deliv_date')->textInput();//widget(\kartik\date\DatePicker::className(), $datepicker); ?>

		<?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

		<?= $form->field($model, 'cost')->textInput() ?>

		<?= $form->field($model, 'payed')->textInput() ?>

		<?/*= $form->field($model, 'order_date')->textInput() */?>

		<?/*= $form->field($model, 'update_date')->textInput() */?>

		<?= $form->field($model, 'manager')->dropDownList(\app\controllers\OrdersController::getPersons('manager')) ?>

		
		<?= $form->field($model, 'status')->dropDownList(\app\models\Orders::getStatus())->hint('Прежний статус: '.$old_status) ?>

		<div class="form-group">
			<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
	
	<div class="col-md-4">
			<div class="product-img">
			<?php
			//debug( $arr[$model->name]['img']);
			?>
				<?=$arr[$model->name]['img'] ? Html::img('http://andreychef/images/restoran_menu/'.$arr[$model->name]['img']) : '';?>
			</div>			
		</div>
</div>


    

