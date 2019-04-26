<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

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
				<?= Html::a('Вернуть на редактирование', ['update', 'id' => $model->id], ['class' => 'ext-btn']) ?>
				<?= Html::a('Выставить счет', ['update', 'id' => $model->id], ['class' => 'ext-btn red']) ?>
				<div></div>
				<?= Html::a('Заказ оплачен', ['view', 'id' => $model->id, 'setstatus' => 5], ['class' => 'ext-btn gray']) ?>
				<?= Html::a('Оплата при доставке', ['view', 'id' => $model->id, 'setstatus' => 6], ['class' => 'ext-btn red']) ?>
				<div></div>
				<?= Html::a('Удалить заказ', ['delete', 'id' => $model->id], [
					'class' => 'ext-btn black',
					'data' => [
						'confirm' => 'Вы действительно хотите удалить заказ?',
						'method' => 'post',
					],
				]) ?>			
			</div>		
		</div>
		
		<div class="col-md-4">
			<div class="product-img">
				<?=$products[$model->name]['img'] ? Html::img('http://andreychef/images/restoran_menu/'.$products[$model->name]['img']) : '';?>
			</div>			
		</div>
	</div>
	
	

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