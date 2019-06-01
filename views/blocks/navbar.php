<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

/**
 * если текущий url == меню url, то добавим class=active
 */
function checkUrlActive($menu_url, $context_route)
{
    $cls = '';
    $url = '/'.$context_route;

    if($_GET){
        $n = count($_GET);
        $url .= '?';
        foreach($_GET as $param => $value){
            $url .= $param.'='.$value;
            $n--;

            if($n)
                $url .= '&';
        }
    }

    if($menu_url == $url)
        $cls = 'active';

    return $cls;
}

$role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

$items = [];


if(Yii::$app->user->can('admin')){
    $items[] = ['label' => 'Все пользователи', 'url' => Url::to(['/user/index'])];
    $items[] = ['label' => 'Шаблоны', 'url' => Url::to(['/mail-tpl/index'])];
}

if(Yii::$app->user->can('director')){
    $items[] = ['label' => 'Менеджеры', 'url' => Url::to(['/user/index', 'role' => 'manager'])];
}

if(Yii::$app->user->can('manager')){
    $items[] = ['label' => 'Список заказов', 'url' => Url::to(['/orders/index'])];
    $items[] = ['label' => 'Клиенты', 'url' => Url::to(['/user/index', 'role' => 'user'])];
}

if(!Yii::$app->user->can('manager') && Yii::$app->user->can('user')){
    $items[] =  ['label' => 'Мои заказы', 'url' => Url::to(['/orders/index'])];
}

?>

<div class="menu">
    <?php
    foreach($items as $item){
        $cls = checkUrlActive($item['url'], $this->context->route);
        echo Html::a($item['label'], $item['url'], ['class' => $cls]);
    }
    ?>
</div>
