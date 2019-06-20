<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

// проверка прав пользователя

if(!\app\controllers\OrdersController::checkMyOrder($model->uid, $model->manager))
    Yii::$app->response->redirect(['orders/index']);

$this->title = 'Заказ № ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
$old_status = $model::getStatus()[$old_status];;
$status = $model::getStatus()[$model->status];
$manager = \app\controllers\UserController::getUser($model->manager);
?>
<div class="orders-update">

    <h1><?= Html::encode($this->title) ?><span class="status"><strong>Статус заказа:&nbsp;</strong><?=$status?><br><strong>Менеджер:&nbsp;</strong><?=$manager['username']?></span></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'old_status' => $old_status
    ]) ?>

</div>
