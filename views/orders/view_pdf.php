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

$status = \app\models\Orders::getStatus()[$model->status];
$fills = \app\controllers\OrdersController::getFills();
$products = \app\controllers\OrdersController::getProducts();

$st = $model->status;

?>
<div class="orders-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="orders-form row">
		<div class="col-md-8">
			<table width="100%" class="order-view-table" > 

				<tr>
					<td width="30%">Заказчик</td>
					<td width="30%"><?=$model->deliv_name?></td>
                    <td rowspan="7"  width="40%" valign="top">
                        <!--<div class="product-img">
                            <?/*=htmlspecialchars(Html::img(Yii::$app->params['mainDomain'].'/images/restoran_menu/'.$products[$model->name]['img'], ['width' => '200']))*/?>
                            <?/*=$products[$model->name]['img'] ? Html::img(Yii::$app->params['mainDomain'].'/images/restoran_menu/'.$products[$model->name]['img'], ['width' => '200']) : '';*/?>
                        </div>-->

                        <div class="product-img">

                            <?php
                            $images = \app\controllers\OrdersController::getProductImages($model, 'pdf');

                            if($images):
                                ?>
                                <div class="">
                                    <table border="0" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td colspan="3">
                                                <div class="product-img-main">
                                                    <?php
                                                    foreach($images as $img){
                                                        if($img['isMain'])
                                                            echo $img['filePath'];
                                                       }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>

                                    </table>
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
                    </td>
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
					<td>Название торта</td>
					<td><?=$model->name?></td>
				</tr>
				
				<tr>
					<td colspan=2 class="title">Описание заказа</td>					
				</tr>
				<tr>
					<td colspan=3><?=$model->description?></td>
				</tr>
                <tr>
                    <td>Дегустационный сет</td>
                    <td><?=$model->tasting_set ? 'Да' : 'Нет';?></td>
                </tr>

                <?php
                if($model->tasting_set):?>

                <tr>
                    <td colspan=2 class="cost-title">Стоимость дегустационного сета: <span><?=number_format(Yii::$app->params['testingSetCost'], 2, '.', ' ')?></span> <span class="currency">руб.</span></td>
                </tr>
                <?php
                endif;
                ?>


				<tr>
					<td colspan=2 class="cost-title">Стоимость заказа: <span><?=number_format(\app\controllers\OrdersController::getOrderCost($model->id), 2, '.', ' ')?></span> <span class="currency">руб.</span></td>
				</tr>

                <tr>
                    <td colspan=2 class="cost-title bg-gray">
                        <div>Ранее оплачено: <span class="sum"><?= number_format($model->payed, 2, '.', ' ') ?> <span class="currency">руб.</span></span> </div>
                    </td>
                </tr>
                <tr>
                    <td colspan=2 class="cost-title bg-gray">
                        <div>Итого: <span class="sum"><?= number_format(\app\controllers\OrdersController::getOrderSum($model->id), 2, '.', ' ') ?> <span class="currency">руб.</span></span>
                            </div>
                    </td>
                </tr>
                <?php
                if($st == 6):
                ?>
                    <tr><td colspan="3"><p id="status6">Заказ оплачивается при доставке</p></td></tr>
                <?php
                endif;
                ?>

				
			</table>
			
			<div>
                <p>Претензий к внешнему виду не имею _____________________________________________</p>
			</div>		
		</div>
		

	</div>
	


</div>