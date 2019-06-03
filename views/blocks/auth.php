<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 16.04.2019
 * Time: 18:43
 */
use yii\helpers\Html;

/* Fake user */
$session = Yii::$app->session;
if($session->get('fuid')){
    $fuid = $session->get('fuid');
    $fuid_info = \app\controllers\UserController::getUser($fuid);
    $fuid_string = '<br><span class="role">клиент</span> '.Html::a($fuid_info['email'], '/orders/index?uid='.$fuid).Html::a('выйти', '/user/unset-fake-uid').'<br>';
}
else
    $fuid_string = '';
/* /Fake user */

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
        . '<span class="role">'.$roles.'</span><span>'.Html::a(Yii::$app->user->identity->email, '/orders/')
        //. '('.Yii::$app->user->getId().') '
        . '</span>'
        . Html::submitButton('выйти',['class' => 'btn btn-link logout'])
        . $fuid_string
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
/*if(Yii::$app->user->getId())
{
    echo '<iframe style="display:block"  src="http://andreychef/auth.php?temp='.time().'&uid_login='.Yii::$app->user->getId().'"></iframe>';
}*/
