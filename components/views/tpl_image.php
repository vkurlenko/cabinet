<?php
use yii\helpers\Html;
//debug($img);

$arr = unserialize($model->$field);
//debug($arr);

if(!empty($arr) && $arr['src']):?>
    <div class="form-img" data-id="<?=$model->id?>">

        <div class="form-img-top">
            <a class="del_one_img" title="Удалить?" href="<?=$url_delete?>" data-id="<?=$model->id?>">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
        </div>

        <div class="img-cont">
            <?=Html::img($arr['src'], $options = ['class' => 'postImg', 'style' => ['width' => '200px']])?>
        </div>

    </div>


<?php
endif;
?>


