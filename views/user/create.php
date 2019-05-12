<?php

use yii\helpers\Html;
use app\controllers\UserController;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$newUserTitle = UserController::isManager() ? 'Клиент' : 'Пользователь';
$this->title = 'Новый '.$newUserTitle;

$this->params['breadcrumbs'][] = ['label' => UserController::isManager() ? 'Клиенты' : 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
