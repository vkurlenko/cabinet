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

$isClient = \app\controllers\UserController::isClient();

$products = \app\controllers\OrdersController::getProductsGroped();


if($uid && !$isClient){
    $model->uid = $uid;
    $model->manager = Yii::$app->user->getId();
}


?>

<div class="orders-form row">
	<div class="col-md-8">
		<?php $form = ActiveForm::begin(); ?>

        <?php
        /* только для менеджера */
        if(!$isClient):
        ?>

		<?= $form->field($model, 'uid')
            ->dropDownList(\app\controllers\OrdersController::getPersons('user'), ['prompt' => 'Выберите заказчика'])
            ->hint('Только зарегистрированные клиенты') ?>
		
		<div class="form-group">
			<label class="control-label">E-mail</label>
			<span><?=\app\controllers\OrdersController::getUserEmail($model->uid)?></span>
		</div>

        <?php
        /* /только для менеджера */

        /* только для клиента */
        else:
            ?>

            <?= $form->field($model, 'uid')->hiddenInput(['value' => Yii::$app->user->getId()])->label('') ?>

            <?= $form->field($model, 'email')->hiddenInput(['value' => \app\controllers\OrdersController::getUserEmail(Yii::$app->user->getId())])->label('') ?>

        <?php
        endif;
        /* /только для клиента */
        ?>
		
		<?= $form->field($model, 'deliv_name')->textInput()->hint('Кому предназначен заказ') ?>

		<?= $form->field($model, 'deliv_phone')->textInput()->hint('Кому предназначен заказ')  ?>

		<?= $form->field($model, 'name')->dropDownList($products, ['prompt' => 'Выберите торт']) ?>

		<?= $form->field($model, 'filling')->dropDownList(\app\controllers\OrdersController::getFills(), ['prompt' => 'Выберите начинку']) ?>

		<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'tasting_set')->checkbox([]);?>

		<?= $form->field($model, 'deliv_date')->widget(DatePicker::className(), $datepicker); ?>

		<?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

        <?php
        /* только для менеджера */
        if(!$isClient):
        ?>

            <?= $form->field($model, 'cost')->textInput() ?>

            <?= $form->field($model, 'payed')->textInput() ?>

            <?= $form->field($model, 'manager')->dropDownList(\app\controllers\OrdersController::getPersons('manager'), ['prompt' => 'Выберите менеджера']) ?>

            <?= $form->field($model, 'status')->dropDownList(\app\models\Orders::getStatus())->hint('Прежний статус: '.$old_status) ?>

        <?php
        endif;
        /* /только для менеджера */
        ?>

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


    

