<?php
use yii\helpers\Html;

if(!$user){
    return false;
}
//debug($user);
?>
<h1>Мои данные</h1>
<table class="table table-striped table-bordered user-info">
    <tr>
        <td><?=$user['username']?></td>
        <td><?=$user['phone']?></td>
        <td><?=$user['email']?></td>
        <td><?=Html::a('Сменить пароль', '/user/update?id='.$user['id'])?></td>
    </tr>
</table>


