<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\controllers\AppController;

AppAsset::register($this);

//$site = 'http://andreychef';
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <meta name='yandex-verification' content='6522c487f6396c04' />
    <meta name="p:domain_verify" content="5df6ffee19f01cff59bcb1a2e73962e6"/>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
    <title></title>
    <link rel="icon" href="<?=Yii::$app->params['mainDomain']?>/favicon1.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="<?=Yii::$app->params['mainDomain']?>/favicon1.ico" type="image/x-icon" />
    <meta content=":::menu_description:::" name="description" />
    <meta content=":::menu_keywords:::" name="keywords" />
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>


<div class="container ext garamond">
    <div class="row header-block">
        <div class="col-md-3 header-addr">

            <div class="auth">
                <?= $this->render('/blocks/auth.php');?>
            </div>

            <div class="addr">
                <?= $this->render('/blocks/header.addr.php');?>
            </div>


            <div class="head_search_frm">
                <div class="search_form">
                    <form method="post" action="/search/">
                        <input type="text" name="SEARCH_INP" class="inp" value="">
                        <input type="submit" name="SEARCH_BTN" class="btn" value="Найти">
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 logo">
            <a class="header" href="<?=Yii::$app->params['mainDomain']?>">
                <img src="<?=Yii::$app->params['mainDomain']?>/img/_kond_v2/logo.png" alt="КОНДИТЕРСКАЯ «КОЛЕСО ВРЕМЕНИ»">
            </a>
        </div>

        <div class="col-md-3 header-phones ">
            <?= $this->render('/blocks/header.phones.php');?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 menu-block">
            <?= $this->render('/blocks/navbar');?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 garamond">
            <?php
            //debug($_COOKIE);
            // //debug($allflash);

            $allflash=\Yii::$app->session->getAllFlashes();

            $title = \app\controllers\SiteController::alertTitle();

            foreach($allflash as $k => $v){
                echo '<p class="alert alert-'.$k.'"><strong>'.$title[$k].'</strong> '.$v.'</p>';
            }
            ?>
            <?=$content?>
            <?
            /*debug(Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
            echo Yii::$app->user->can('manager');*/
            /*debug(app\controllers\AppController::getRole());
            echo Yii::$app->user->can('admin');*/
            ?>
        </div>
    </div>

    <footer class="footer-block">
        <div class="">
            footer
            <?php
            // отправим ID авторизованного пользователя на основной сайт
            if(Yii::$app->user->getId())
            {
                echo '<iframe style="display: none"  src="'.Yii::$app->params['mainDomain'].'/auth.php?temp='.time().'&uid_login='.Yii::$app->user->getId().'"></iframe>';

                // редирект на предыдущую перед авторизацией страницу
                $session = Yii::$app->session;
                //echo $session->get('ref'); die;
                if($session->has('ref')){
                    $ref = $session->get('ref');
                    $session->remove('ref');
                    ?>
                    <script>
                        //alert('ref='+'<?=$ref?>');
                        window.document.location.href = '<?=$ref?>';
                    </script>
                    <?php
                }
            }

            ?>
        </div>
    </footer>

</div>



<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>