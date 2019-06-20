<?php

use yii\helpers\Html;
use app\controllers\UserController;

/* @var $this yii\web\View */
/* @var $model app\models\User */

if(UserController::isManager() || UserController::isDirector()){
    $newUserTitle = 'Клиент';
    $label = 'Клиенты';
}
else{
    $newUserTitle = 'Пользователь';
    $label = 'Пользователи';
}


$this->title = 'Новый '.$newUserTitle;

$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
