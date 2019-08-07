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
        'todayHighlight' => true,
        'startDate' => date('Y-m-d')
    ]
];

//debug($model);

$isClient = \app\controllers\UserController::isClient();

$products = \app\controllers\OrdersController::getProductsGroped();

$fakeClient = \app\controllers\UserController::isFakeClient();

if($uid && !$isClient){
    $model->uid = $uid;
    $model->manager = Yii::$app->user->getId();
}

// произвольный заказ?
if(null !== Yii::$app->request->get('free'))
    $isFree = true;

?>

<div class="orders-form row garamond">
    <?php $form = ActiveForm::begin(); ?>
	<div class="col-md-8">


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
        //echo 'filing = '.$model->filling;
        $other = '';
        if($model->filling && trim($model->filling) != '|'){
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

        <?php
        /* ??????????????????????? */
        /*if($isClient){
            $client = \app\controllers\UserController::getUser(Yii::$app->user->getId());
            $deliv_name = $client['username'];
            $deliv_phone = $client['phone'];
        }
        else{
            $deliv_name = '';
            $deliv_phone = '';
        }
        $model->deliv_phone = $deliv_phone;*/
        if($fakeClient){
            $model->deliv_name = $fakeClient['username'];
            $model->deliv_phone = $fakeClient['phone'];
        }
        else{
            /*$model->deliv_name = '';
            $model->deliv_phone = '';*/
        }
        //$model->deliv_phone = $deliv_phone;
        ?>
        <?/*= $form->field($model, 'deliv_name')->textInput(['value' => $deliv_name])->hint('Кому предназначен заказ') */?>
        <?/* ??????????????????????? */?>
        <?= $form->field($model, 'deliv_name')->textInput()->hint('Кому предназначен заказ') ?>

        <?= $form->field($model, 'deliv_phone')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->params['phoneMask']])->hint('Кому предназначен заказ')  ?>

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

        <?/*= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); */?><!--
        <?/*= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label(''); */?>
        --><?/*= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label(''); */?>

        <!--<span id="load-pic">Загрузить</span>-->

        <div class="form-group">
			<?= Html::submitButton('Сохранить', ['class' => 'btn ext-btn']) ?>
		</div>


	</div>
	
	<div class="col-md-4">
			<div class="product-img">
                <?php
                $images = \app\controllers\OrdersController::getProductImages($model);

                ?>
                    <div class="">
                        <div class="product-img-main">
                            <!--<img src="#" alt="" class="image0" />-->
                            <?php
                            //debug($images);
                            if(count($images) === 1 && $images[0]['id'] == ''){
                                echo '<img src="#" alt="" class="image0" />';
                            }
                            else{
                                foreach($images as $img){
                                    if($img['isMain'])
                                        echo $img['filePath'];
                                }
                            }
                            ?>
                            <?/*= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*', 'class' => 'load', 'data-target' => 'image0']); */?>
                        </div>


                        <div class="product-img-other">
                            <?php
                                $i = 0;
                                for($i = 0; $i < 3; $i++):
                                ?>
                                <div class="cont">
                                    <div>
                                        <img src="#" alt="" class="image<?=$i?>" />
                                        <?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*', 'class' => 'load', 'data-target' => 'image'.$i])->label(''); ?>
                                        <span>Добавить фото</span>
                                    </div>

                                    <?php
                                    if(isset($images[$i]) && $images[$i]['id'] != ''):
                                        $img = $images[$i];
                                        ?>
                                        <div id="img-<?=$img['id']?>" class="d">
                                            <?= $img['filePath']?>
                                            <?= Html::a('удалить', '#', ['class' => 'product-img-del', 'data-imgid' => $img['id'], 'data-modelid' => $model->id]) ?>
                                        </div>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <?php
                                endfor;
                            ?>
                            <div style="clear: both"></div>
                        </div>
                    </div>


			<?php
           /*
            else{
                echo $arr[$model->name]['img'] ? Html::img(Yii::$app->params['mainDomain'].'/images/restoran_menu/'.$arr[$model->name]['img']) : '';
            }*/
			?>

			</div>
		</div>
    <?php ActiveForm::end(); ?>
</div>


    

