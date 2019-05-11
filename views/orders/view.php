<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

// проверка прав пользователя
\app\controllers\OrdersController::checkMyOrder($model->uid, $model->manager);

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

if(count($roles) == 1 && $roles['user'])
    $isUser = true;



$this->title = 'Заказ №'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$status = $model::getStatus()[$model->status];
$fills = \app\controllers\OrdersController::getFills();
$products = \app\controllers\OrdersController::getProducts();

?>
<div class="orders-view">

    <h1><?= Html::encode($this->title) ?><span class="status"><strong>Статус заказа:&nbsp;</strong><?=$status?></span></h1>

    <div class="orders-form row">
		<div class="col-md-8">
			<table width="100%" class="order-view-table" > 
				<tr>
					<td colspan=2 class="blank-link"><?=Html::img('/img/pdf.png').' '.Html::a('Бланк заказа', '/');?></td>
				</tr>
				<tr>
					<td width="30%">Клиент</td>
					<td width="70%"><?=$model->deliv_name?></td>
				</tr>
				<tr>
					<td>Телефон</td>
					<td><?=$model->deliv_phone?></td>
				</tr>
				<tr>
					<td>E-mail</td>
					<td><?=\app\controllers\OrdersController::getUserEmail($model->uid)?></td>
				</tr>
				<tr>
					<td>Дата доставки</td>
					<td><?=$model->deliv_date?></td>
				</tr>
				<tr>
					<td>Адрес доставки</td>
					<td><?=$model->address?></td>
				</tr>
				<tr>
					<td>Начинка</td>
					<td><?=$fills[$model->filling]?></td>
				</tr>
				<tr>
					<td>Название торта</td>
					<td><?=$products[$model->name]['name'];?></td>
				</tr>
				
				<tr>
					<td colspan=2 class="title">Описание заказа</td>					
				</tr>
				<tr>
					<td colspan=2><?=$model->description?></td>					
				</tr>
                <tr>
                    <td>Дегустационный сет</td>
                    <td><?=$model->tasting_set ? 'Да' : 'Нет';?></td>
                </tr>
				<tr>
					<td colspan=2 class="cost-title">Стоимость заказа: <span><?=$model->cost?></span> <span class="currency">руб.</span></td>					
				</tr>
				<tr>
					<td colspan=2 class="cost-title bg-gray"><div>Ранее оплачено: <span class="sum"><?=$model->payed?></span> <span class="currency">руб.</span></div></td>					
				</tr>
				<tr>
					<td colspan=2 class="cost-title bg-gray"><div>Итого: <span class="sum"><?=((int)$model->cost - (int)$model->payed)?></span> <span class="currency">руб.</span></div></td>					
				</tr>
				
			</table>
			
			<div>

                <?php
                if($isUser):

                    //echo \app\controllers\PayController::getPayLink($model->id, $model->cost);
                ?>
                    <?= Html::checkbox('agree', false, ['label' => 'Я прочитал и принимаю договор оферты', 'class' => 'agree']) ?><br>

                    <?php
                    // если статус Выставлен счет, то кнопка оплаты
                    if($model->status === 1):
                    ?>
                    <?= Html::a('Оплатить', ['#'], ['class' => 'ext-btn btn-pay red btn-deactive', 'data' => ['url' => '/pay/pay-order?order_id='.$model->id, ]]) ?>
                    <?php
                    endif;
                    ?>

                <?php
                else:
                ?>
                    <?= Html::a('Вернуть на редактирование', ['update', 'id' => $model->id, 'setstatus' => 7], ['class' => 'ext-btn']) ?>
                    <?= Html::a('Выставить счет', ['view', 'id' => $model->id, 'setstatus' => 1], ['class' => 'ext-btn red']) ?>
                    <div></div>
                    <?= Html::a('Заказ оплачен', ['view', 'id' => $model->id, 'setstatus' => 5], ['class' => 'ext-btn gray']) ?>
                    <?= Html::a('Оплата при доставке', ['view', 'id' => $model->id, 'setstatus' => 6], ['class' => 'ext-btn red']) ?>
                    <div></div>
                    <?= Html::a('Удалить заказ', ['delete', 'id' => $model->id, 'setstatus' => 30], [
                        'class' => 'ext-btn black',
                        'data' => [
                            'confirm' => 'Вы действительно хотите удалить заказ?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php
                endif;
                ?>


			</div>		
		</div>
		
		<div class="col-md-4">
			<div class="product-img">
				<?=$products[$model->name]['img'] ? Html::img('http://andreychef/images/restoran_menu/'.$products[$model->name]['img']) : '';?>
			</div>			
		</div>
	</div>
	
	<div class="log">
		<?=\app\controllers\OrdersController::getLog($model->id);?>
	</div>

    сбер
    <?php
    /*$order_id = 123;
    $sum  = 1000;

    $vars = array();
    $vars['userName'] = 'andreychef-api';
    $vars['password'] = 'andreychef';
    $vars['password'] = 'andreychef';

    // ID заказа в магазине.
    $vars['orderNumber'] = $order_id;

    // Сумма заказа в копейках.
    $vars['amount'] = $sum * 100;

    // URL куда клиент вернется в случае успешной оплаты.
    $vars['returnUrl'] = 'http://andreychef.com/success/';

    // URL куда клиент вернется в случае ошибки.
    $vars['failUrl'] = 'http://andreychef.com/error/';

    // Описание заказа, не более 24 символов, запрещены % + \r \n
    $vars['description'] = 'Заказ №' . $order_id . ' на andreychef.com';

    $ch = curl_init('https://3dsec.sberbank.ru/payment/rest/registerPreAuth.do?' . http_build_query($vars));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($res, JSON_OBJECT_AS_ARRAY);
    if (empty($res['orderId'])){
        // Возникла ошибка:
        echo $res['errorMessage'];
    } else {
        // Успех:
        // Тут нужно сохранить ID платежа в своей БД - $res['orderId']

        // Перенаправление клиента на страницу оплаты.
        //header('Location: ' . $res['formUrl'], true);

        // Или на JS
        echo '<script>document.location.href = "' . $res['formUrl'] . '"</script>';
    }*/
    ?>


    <? /*=DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            [
				// заказчик
                'attribute' => 'uid',
                'value' => function($data){
                    $users = \app\controllers\OrdersController::getPersons('user');
                    return $users[$data->uid].' ('.\app\controllers\OrdersController::getUserEmail($data->uid).')';
                }
            ],
			'deliv_name',
			'deliv_phone',			
            [				
                'attribute' => 'name',
                'value' => function($data){
                    $products = \app\controllers\OrdersController::getProducts();
                    return $products[$data->name];
                }
            ],
            [
                'attribute' => 'filling',
                'value' => function($data){
                    $fills = \app\controllers\OrdersController::getFills();
                    return $fills[$data->filling];
                }
            ],
            'description:ntext',
            'deliv_date',			
            'address:ntext',
            'cost',
            'payed',
            'order_date',
            'update_date',
            [
                'attribute' => 'manager',
                'value' => function($data){
                    $users = \app\controllers\OrdersController::getPersons('manager');
                    return $users[$data->manager];
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($data, $model){
                    $arr = \app\models\Orders::getStatus();
                    return $arr[$data->status];
                }
            ],
        ],
    ])*/ ?>

</div>