<?php
/*use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\controllers\AppController;
use yii\helpers\Html;
AppAsset::register($this);*/
use yii\helpers\Html;
?>

<!--<div class="container ext garamond pdf">
    <div class="row header-block">
-->
    <div id="pdf" class="pdf">
        <table class="header-table" cellpadding="10">
            <tr>
                <td width="30%">
                    <div class="header-addr">
                        <div class="addr" id="ad">
                            <?= $this->render('/blocks/header.addr.php');?>
                        </div>
                    </div>
                </td>

                <td width="40%" align="center" class="td-logo">
                    <div class="logo">
                        <?=Html::img('img/logo.png', ['alt' => 'КОНДИТЕРСКАЯ «КОЛЕСО ВРЕМЕНИ»', 'id' => 'im'])?>
                    </div>
                </td>

                <td width="30%">
                    <div class="header-phones">
                        <?= $this->render('/blocks/header.phones.php');?>
                    </div>
                </td>
            </tr>

        </table>

   <!-- </div>-->


        <div class="row">
            <div class="col-md-12 garamond">
                <?=$content?>
            </div>
        </div>

    </div>
<!--
</div>-->