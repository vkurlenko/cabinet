<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

$role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

$items = [];

if(Yii::$app->user->can('admin')){
    $items[] = ['label' => 'Все пользователи', 'url' => ['/user/index']];
}

if(Yii::$app->user->can('director')){
    //$items[] = ['label' => 'Менеджеры', 'url' => ['/user/index?role=manager']];
    $items[] = ['label' => 'Менеджеры', 'url' => ['/user/index?role=manager']];
}

if(Yii::$app->user->can('manager')){
    $items[] = ['label' => 'Список заказов', 'url' => ['/orders/index']];
    $items[] = ['label' => 'Клиенты', 'url' => ['/user/index?role=user']];
}

if(Yii::$app->user->can('user')){
    $items[] =  ['label' => 'Мои заказы', 'url' => ['/site/index']];
    /*$items[] =  ['label' => 'About', 'url' => ['/site/about']];
    $items[] =  ['label' => 'Contact', 'url' => ['/site/contact']];*/
}

?>

<div class="menu">
    <?php
    //echo $this->context->route;
    foreach($items as $item){
        echo Html::a($item['label'], $item['url']);
    }
    ?>
</div>
