<div class="">    <div class="menu_bottom">
        <a href="<?=Yii::$app->params['mainDomain']?>/search/">Поиск</a>
        </a><a href="<?=Yii::$app->params['mainDomain']?>/">На главную</a>
    </div>
    <a href="http://www.multysite.ru/" class="vinchi"></a>
</div>

<?php

$session = Yii::$app->session;

// Fake uid
$fuid = $session->get('fuid') ? $session->get('fuid') : '';

if(Yii::$app->user->getId())
{
    // отправим ID авторизованного пользователя на основной сайт для авторизации,
    // данные авторизации там сохранятся в cookie
    echo '<iframe style="display: none"  src="'.Yii::$app->params['mainDomain'].'/auth.php?temp='.time().'&uid_login='.Yii::$app->user->getId().'&fuid='.$fuid.'"></iframe>';

    // редирект на предыдущую перед авторизацией страницу
    if($session->has('ref')){
        $ref = $session->get('ref');
        $session->remove('ref');
        ?>
        <script>
            window.document.location.href = '<?=$ref?>';
        </script>
        <?php
    }
}
?>
</div>