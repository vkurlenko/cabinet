<?php
use yii\helpers\Html;
use app\controllers\MailTplController;
?>

<?=MailTplController::getTplByAlias($tpl_alias, $vars);?>