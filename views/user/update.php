<?php

use yii\helpers\Html;

// проверка прав пользователя
//\app\controllers\OrdersController::checkMyOrder($model->id);

/* @var $this yii\web\View */
/* @var $model app\models\User */

if(Yii::$app->user->can('manager')){
    $title = 'Изменить данные пользователя: ' . $model->username;
}
else
    $title = 'Изменить мои данные';

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
