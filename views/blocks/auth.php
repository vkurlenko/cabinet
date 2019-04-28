<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 16.04.2019
 * Time: 18:43
 */
use yii\helpers\Html;

$arr = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

$roles = '';

foreach($arr as $role){
    $roles .= $role->description;
}

if(Yii::$app->user->isGuest){
    $items[] = ['label' => 'Вход', 'url' => ['/site/login']];
    $items[] = ['label' => 'Регистрация', 'url' => ['/site/login']];
}
else{
    $items[] = Html::beginForm(['/site/logout'], 'post')
        . '<span>'.Yii::$app->user->identity->email.' ('.Yii::$app->user->getId().') '
        . '<br><span class="role">'.$roles.'</span></span>'
        . ''
        . Html::submitButton(
            'выйти',
            ['class' => 'btn btn-link logout']
        )
        . Html::endForm();
}


foreach($items as $item){
    if(is_array($item)){
        echo Html::a($item['label'], $item['url']);
    }
    else
        echo $item;
}

// отправим ID авторизованного пользователя на основной сайт
if(Yii::$app->user->getId())
{
    //echo Yii::$app->user->getId();
    echo '<iframe style="display:none"  src="http://andreychef/auth.php?temp='.time().'&uid_login='.Yii::$app->user->getId().'"></iframe>';
}
