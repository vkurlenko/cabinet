<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 29.04.2019
 * Time: 15:31
 */

if($logs){
    //debug($logs);
    $status = \app\models\Orders::getStatus();
    $users = \app\models\User::find()->indexBy('id')->all();
    ?>
    <table class="table table-striped table-bordered">
        <tr>
            <th>Время</th>
            <th>Старый статус</th>
            <th>Новый статус</th>
            <th>Кто изменил</th>
        </tr>
    <?php
    foreach($logs as $log){
        $userRole = \app\controllers\UserController::getUserRole($log['uid']);
        ?>
        <tr>
            <td><?=$log['datetime']?></td>
            <td><?=$status[$log['old_status']]?></td>
            <td><?=$status[$log['new_status']]?></td>
            <td><?=$users[$log['uid']]['username'].' ('.$userRole.')'?></td>
        </tr>

        <?php

    }
    ?>
    </table>
    <?
}


