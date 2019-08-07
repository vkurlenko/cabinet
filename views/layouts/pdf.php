<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\controllers\AppController;
use yii\helpers\Html;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <meta name="p:domain_verify" content="5df6ffee19f01cff59bcb1a2e73962e6"/>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
    <title></title>
    <?php $this->registerCsrfMetaTags() ?>
    <?php /*$this->head() */?>
</head>

<body>
<?php /*$this->beginBody() */?>


<div class="container ext garamond pdf">
    <div class="row header-block">

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
                        <?=Html::img('img/logo.png', ['alt' => 'МАСТЕРСКАЯ ТОРТОВ', 'id' => 'im'])?>
                    </div>
                </td>

                <td width="30%">
                    <div class="header-phones">
                        <?= $this->render('/blocks/header.phones.php');?>
                    </div>
                </td>
            </tr>

        </table>

    </div>


    <div class="row">
        <div class="col-md-12 garamond">
            <?=$content?>
        </div>
    </div>

</div>



<?php /*$this->endBody() */?>
</body>
</html>
<?php $this->endPage() ?>