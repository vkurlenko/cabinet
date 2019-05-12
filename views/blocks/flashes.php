<?php
$allflash=\Yii::$app->session->getAllFlashes();

$title = \app\controllers\SiteController::alertTitle();

foreach($allflash as $k => $v){
    echo '<p class="alert alert-'.$k.'"><strong>'.$title[$k].'</strong> '.$v.'</p>';
}