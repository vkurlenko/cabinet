<?php

use yii\helpers\Html;
use app\controllers\MailTplController;

$tpl_alias = 'pay_link';

$vars = [
    'link' => $link
];
?>

<?=MailTplController::getTplByAlias($tpl_alias, $vars);?>

