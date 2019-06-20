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

// произвольный заказ?
if(null !== Yii::$app->request->get('free'))
    $isFree = true;

?>

<div class="orders-form row garamond">
	<div class="col-md-8">
		<?php $form = ActiveForm::begin(); ?>

        <?php
/* только для менеджера */
        if(!$isClient):
            $client = \app\controllers\UserController::getUser($model->uid);
        ?>

		<?/*= $form->field($model, 'uid')
            ->dropDownList(\app\controllers\OrdersController::getPersons('user'), ['prompt' => 'Выберите клиента'])
            ->hint('Только зарегистрированные клиенты') */?>

        <div class="form-group">
            <label class="control-label">Клиент</label>
            <span><?=$client['username']?></span>
        </div>

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

        <?= $form->field($model, 'deliv_date')->widget(DatePicker::className(), $datepicker); ?>

        <?= $form->field($model, 'name')->textarea(['rows' => 2]) ?>

        <?= $form->field($model, 'filling')->hiddenInput(['rows' => 2]) ?>

        <div id="fill-field">
        <?php
        $other = '';
        if($model->filling){
            $arr = explode("|", $model->filling);

            foreach($arr as $f) {
                if(in_array($f, \app\controllers\OrdersController::getFills())){
                    echo $form->field($model, 'fill[]')->dropDownList(\app\controllers\OrdersController::getFills(), ['prompt' => 'Выберите начинку', 'value' => $f, 'style' => 'width: 80%'])->label('');
                }
                else{
                    $other = $f;
                }
            }
        }
        else{
            echo $form->field($model, 'fill[]')->dropDownList(\app\controllers\OrdersController::getFills(), ['prompt' => 'Выберите начинку', 'value' => $f, 'style' => 'width: 80%'])->label('');
        }
        ?>
        <?= Html::a('Добавить начинку', '#', ['class' => 'add-fill']) ?>
        </div>



        <?= $form->field($model, 'fill[]')->textarea(['rows' => 2, 'value' => $other])->label('Дополнительные начинки') ?>


        <?= $form->field($model, 'description')->textarea(['rows' => 6, 'value' => str_replace(['<br>', '<br />'], "\n", $model->description)]) ?>

        <?= $form->field($model, 'tasting_set')->checkbox([]);?>

        <?= $form->field($model, 'deliv_name')->textInput()->hint('Кому предназначен заказ') ?>

        <?= $form->field($model, 'deliv_phone')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->params['phoneMask'],])->hint('Кому предназначен заказ')  ?>

        <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>


		<?/*= $form->field($model, 'name')->dropDownList($products, ['prompt' => 'Выберите торт']) */?>



        <?php
        /* только для менеджера */
        if(!$isClient):
        ?>

            <?= $form->field($model, 'cost')->textInput() ?>

            <?/*= $form->field($model, 'payed')->textInput() */?>

            <?php
            if(Yii::$app->user->can('admin') || Yii::$app->user->can('director')){
                echo $form->field($model, 'payed')->textInput();
            }
            else{
                echo '<div class="bg-gray"><label class="control-label" for="orders-cost">Ранее оплачено</label><div>'.$model->payed.'</div></div>';
            }
            ?>

            <?php
            if(Yii::$app->user->can('admin') || Yii::$app->user->can('director')){
                echo $form->field($model, 'manager')->dropDownList(\app\controllers\OrdersController::getPersons('manager'), ['prompt' => 'Выберите менеджера', 'options' =>[ Yii::$app->user->getId() => ['Selected' => true]]]);
            }
            else{
                if($model->manager){
                    $manager = \app\controllers\UserController::getUser($model->manager);
                    echo '<div class="bg-gray"><label class="control-label" for="orders-cost">Менеджер</label><div>'.$manager['username'].'</div></div>';
                }
            }
            ?>

            <?php
            if(Yii::$app->user->can('admin') || Yii::$app->user->can('director')){
                echo $form->field($model, 'status')->dropDownList(\app\models\Orders::getStatus())->hint('Прежний статус: '.$old_status);
            }
            else{
                echo '<div class="bg-gray"><label class="control-label" for="orders-cost">Статус</label><div>'.\app\models\Orders::getStatus()[$model->status].'</div></div>';
            }
            ?>

            <?/*= $form->field($model, 'status')->dropDownList(\app\models\Orders::getStatus())->hint('Прежний статус: '.$old_status) */?>

        <?php
        endif;
        /* /только для менеджера */
        ?>

        <?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); ?>
        <?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); ?>
        <?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); ?>

        <span id="load-pic">Загрузить</span>

        <div class="form-group">
			<?= Html::submitButton('Сохранить', ['class' => 'btn ext-btn']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
	
	<div class="col-md-4">
			<div class="product-img">

                <?php
                $images = \app\controllers\OrdersController::getProductImages($model);
                if($images):
                ?>
                <div class="">
                    <div class="product-img-main">
                        <?php
                        foreach($images as $img){
                            if($img['isMain'])
                                echo $img['filePath'];
                        }
                        ?>
                    </div>
                    <div class="product-img-other">
                        <?php
                        foreach($images as $img) {
                                ?>
                                <div id="img-<?=$img['id']?>"><?= $img['filePath'] ?>
                                    <?= Html::a('удалить', '#', ['class' => 'product-img-del', 'data-imgid' => $img['id'], 'data-modelid' => $model->id]) ?>
                                </div>
                            <?php
                        }
                            ?>
                    </div>
                </div>
                <?php
                endif;
                ?>

			<?php
           /*
            else{
                echo $arr[$model->name]['img'] ? Html::img(Yii::$app->params['mainDomain'].'/images/restoran_menu/'.$arr[$model->name]['img']) : '';
            }*/
			?>

			</div>
		</div>
</div>


    

