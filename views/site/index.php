<?php
/* @var $this yii\web\View */

$this->title = '';

// отправим ID авторизованного пользователя на основной сайт
if($uid_logout)
{
    echo '<iframe style="display: none" src="'.Yii::$app->params['mainDomain'].'/auth.php?temp='.time().'&uid_logout=1"></iframe>';
}
?>

<div class="site-index">


</div>
