<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Hash;
use app\models\Orders;
use app\controllers\OrdersController;

use app\controllers\OptionsController;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */


// проверка прав пользователя (клиент или менеджер этого заказа)
if(!OrdersController::checkMyOrder($model->uid, $model->manager) && !Yii::$app->request->get('hash'))
    Yii::$app->response->redirect(['orders/index']);

/* проверим кто пришел */
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

if(count($roles) == 1 && $roles['user'])
    $isUser = true; // пришел клиент
/* /проверим кто пришел */


/* если пришел hash, то проверим его */
if(Yii::$app->request->get('hash')){
    if(!OrdersController::validHash(Yii::$app->request->get('hash'))){

        // если hash просрочен, то отправим ошибку
        Yii::$app->response->redirect(['site/errors', 'msg' => 'hash_expired']);
    }
    else{
        // иначе предложим оплатить
        $isUser = true;
    }

}
/* /если пришел hash, то проверим его */

$this->title = 'Заказ №'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$status     = Orders::getStatus()[$model->status];
$fills      = OrdersController::getFills();
$products   = OrdersController::getProducts();
$manager = \app\controllers\UserController::getUser($model->manager);

?>
<div class="orders-view">

    <?= $model->status == 50 ? '<p class="alert alert-danger"><strong>'.$status.'</strong> (истек срок ожидания оплаты)</p>' : '' ?>

    <h1><?= Html::encode($this->title) ?><span class="status"><strong>Статус заказа:&nbsp;</strong><?=$status?><br><strong>Менеджер:&nbsp;</strong><?=$manager['username']?></span></h1>

    <div class="orders-form row">
		<div class="col-md-8">
			<table width="100%" class="order-view-table" >
                <?php
                if(Yii::$app->user->getId()):
                ?>
				<tr>
					<td colspan=2 class="blank-link"><?=Html::img('/img/pdf.png').' '.Html::a('Бланк заказа', Url::to(['/orders/pdf', 'id' => $model->id]));?></td>
				</tr>
                <?php
                endif;
                ?>

                <?php
                if(!$isUser):
                    $client = \app\controllers\UserController::getUser($model->uid);
                ?>
                <tr>
                    <td width="30%">Клиент</td>
                    <td width="70%"><?=$client['username']?></td>
                </tr>
                <?php
                endif;
                ?>
                <tr>
                    <td>E-mail</td>
                    <td><?=OrdersController::getUserEmail($model->uid)?></td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td><?=$model->deliv_phone?></td>
                </tr>
                <tr>
                    <td>Дата доставки</td>
                    <td><?=\app\controllers\AppController::formatDate($model->deliv_date)?></td>
                </tr>
                <tr>
                    <td>Название торта</td>
                    <td><?/*=$products[$model->name]['name'];*/?><?=$model->name?></td>
                </tr>
                <tr>
                    <td>Начинка</td>
                    <td><?/*=$fills[$model->filling]*/?><?/*=$model->filling*/?><?php
                        $arr = explode('|', $model->filling);
                        $string = '';
                        foreach($arr as $f){
                            $string .= $f.'<br>';
                        }
                        echo  $string;
                        ?></td>
                </tr>
                <tr>
                    <td colspan=2 class="title">Описание заказа</td>
                </tr>
                <tr>
                    <td colspan=2><?=str_replace("\n", "<br />", $model->description)?></td>
                </tr>

                <tr>
                    <td>Дегустационный сет</td>
                    <td><?=$model->tasting_set ? 'Да' : 'Нет';?></td>
                </tr>

				<tr>
					<td width="30%">Заказчик<!--Клиент--></td>
					<td width="70%"><?=$model->deliv_name?></td>
				</tr>
				<tr>
					<td>Информация о доставке<!--Адрес доставки--></td>
					<td><?=$model->address?></td>
				</tr>

                <?php
                if($model->tasting_set):?>

                <tr>
                    <td colspan=2 class="cost-title">Стоимость дегустационного сета: <span><?=OptionsController::getOption('testingSetCost')->value?></span> <span class="currency">руб.</span></td>
                </tr>
                <?php
                endif;
                ?>

				<tr>
                    <?
                    $sum = OrdersController::getOrderSum($model->id);
                    //$sum = $sum * -1;
                    $note = ($sum < 0) ? '(Вернуть клиенту. Переплата)' : '';
                    ?>
					<td colspan=2 class="cost-title">Стоимость заказа: <span><?=OrdersController::getOrderCost($model->id)?></span> <span class="currency">руб.</span></td>
				</tr>
				<tr>
					<td colspan=2 class="cost-title bg-gray"><div>Ранее оплачено: <span class="sum"><?=$model->payed?></span> <span class="currency">руб.</span></div></td>					
				</tr>
				<tr>
					<td colspan=2 class="cost-title bg-gray"><div>Итого: <span class="sum"><?= OrdersController::getOrderSum($model->id)?></span> <span class="currency">руб.</span> <?=$note?></div></td>
				</tr>
				
			</table>
			
			<div>

                <?php
                if($isUser):
                    // если клиент или переход по ссылке с hash для оплаты

                    // если статус Выставлен счет, то кнопка оплаты
                    //if($model->status === 1):
                    if(in_array($model->status, [1, 4])):
                    ?>
                        <div class="tort2016_ch "></div>
                <div class="block-agree">

                    <?php
                    if(OptionsController::getOption('file_offer')->value){
                        $text = Html::a(' договор оферты', [OptionsController::getOption('file_offer')->value], ['style' => 'color: #CC3333', 'target' => 'blank',]);
                    }
                    else{
                        $text = ' договор оферты';
                    }

                    ?>

                    <?= Html::checkbox('agree', 0, ['label' => 'Я прочитал и принимаю '.$text, 'class' => 'agree' ]) ?><br>
                </div>

                        <div style="text-align: center; padding: 20px">
                        <?= Html::a('Оплатить', ['#'], ['class' => 'ext-btn btn-pay red btn-deactive', 'data' => ['url' => '/pay/pay-order?order_id='.$model->id, ], ]) ?>
                        </div>
                    <?php
                    else:
                        //echo '<p class="alert alert-danger">Оплата невозможна, обратитесь к менеджеру</p>';
                    endif;
                    ?>

                <?php
                elseif(Yii::$app->user->getId()):
                    if($model->status == 5){
                        // если статус заказа "Оплачен"
                        echo Html::a('Перезаказать', ['view', 'id' => $model->id, 'setstatus' => 40], ['class' => 'ext-btn red']);
                        echo Html::a('Доплата к заказу', ['update', 'id' => $model->id, 'setstatus' => 7], ['class' => 'ext-btn red']);
                    }
                    elseif($model->status == 4){
                        echo Html::a('Перезаказать', ['view', 'id' => $model->id, 'setstatus' => 40], ['class' => 'ext-btn red', 'data' => [
                            'confirm' => 'Уточните у клиента не находится ли он в банке и не оплачивает ли заказ',
                            'method' => 'post',
                        ]]);
                    }
                    /*elseif(in_array($model->status, [20, 30]) ){
                        echo Html::a('Перезаказать', ['view', 'id' => $model->id, 'setstatus' => 40], ['class' => 'ext-btn red']);
                        echo Html::a('Удалить заказ', ['delete', 'id' => $model->id, 'setstatus' => 30], [
                            'class' => 'ext-btn black',
                            'data' => [
                            'confirm' => 'Вы действительно хотите удалить заказ?',
                            'method' => 'post',
                            ],
                        ]);
                    }*/
                    elseif(!$model->manager){
                        echo Html::a('Взять заказ', ['update', 'id' => $model->id], ['class' => 'ext-btn']);
                        echo Html::a('Отмена', ['index'], ['class' => 'ext-btn']);
                    }
                    elseif(in_array($model->status, [20, 30]) ){

                    }
                    else{
                        ?>
                        <?= Html::a('Вернуть на редактирование', ['update', 'id' => $model->id, 'setstatus' => 7], ['class' => 'ext-btn']) ?>
                        <?= (!in_array($model->status, [1, 4]) && (int)OrdersController::getOrderSum($model->id) > 0) ? Html::a('Выставить счет', ['view', 'id' => $model->id, 'setstatus' => 1], ['class' => 'ext-btn red']) : '' ?>
                        <div></div>
                        <?/*= Html::a('Заказ оплачен', ['view', 'id' => $model->id, 'setstatus' => 5], ['class' => 'ext-btn gray']) */?>
                        <?= Html::a('Заказ оплачен', ['view', 'id' => $model->id, 'setstatus' => 5], ['class' => 'ext-btn gray']) ?>
                        <?= Html::a('Оплата при доставке', ['view', 'id' => $model->id, 'setstatus' => 6], ['class' => 'ext-btn red']) ?>
                        <div></div>
                        <?= $model->status == 4 ? Html::a('Перезаказать', ['view', 'id' => $model->id, 'setstatus' => 40], ['class' => 'ext-btn red']) : '' ?>
                        <?= Html::a('Удалить заказ', ['delete', 'id' => $model->id, 'setstatus' => 30], [
                            'class' => 'ext-btn black',
                            'data' => [
                                'confirm' => 'Вы действительно хотите удалить заказ?',
                                'method' => 'post',
                            ],
                        ]) ?>
                        <?php
                    }
                    ?>

                <?php
                endif;
                ?>


			</div>		
		</div>
		
		<div class="col-md-4">

            <div class="product-img">
                <?php
                $images = \app\controllers\OrdersController::getProductImages($model);

                ?>
                <div class="">
                    <div class="product-img-main">
                        <?php

                            foreach($images as $img){
                                if($img['isMain'])
                                    echo $img['filePath'];
                            }

                        ?>
                            <?/*= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*', 'class' => 'load', 'data-target' => 'image0']); */?>
                    </div>


                    <div class="product-img-other-view" style="margin-top: 5px">
                        <?php
                        $i = 0;
                        for($i = 0; $i < 3; $i++):
                        ?>
                        <div class="cont">

                            <?php
                            if(isset($images[$i]) && $images[$i]['id'] != ''):
                            $img = $images[$i];
                            ?>
                            <div id="img-<?=$img['id']?>" class="d">
                                <?= $img['filePath']?>
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
	</div>

    <?php
    if(!$isUser):
    ?>
	<div class="log">
		<?=OrdersController::getLog($model->id);?>
	</div>
    <?php
    endif;
    ?>


</div>