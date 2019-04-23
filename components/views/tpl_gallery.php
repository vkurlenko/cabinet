<?php
use yii\helpers\Html;
//debug($img);
?>

<div class="form-img" data-id="<?=$img->id?>">

    <div class="form-img-top">
        <a class="del_img" title="Удалить?" href="<?=$url_delete?>" data-id="<?=$img->id?>">
            <span class="glyphicon glyphicon-remove"></span>
        </a>

        <?php
        if($fields['active']):?>
            <input type="checkbox" title="Показывать на сайте" class="img-active" <?=$img->active ? 'checked' : ''?>>
        <?php
        endif;
        ?>
    </div>



    <div class="img-cont"><?/*=$img->id*/?>
        <?=Html::img($img->getUrl('200x200'));?>
    </div>

    <?php
    if($fields['sort']):?>
        <input type="hidden" title="Порядковый номер" placeholder="Порядковый номер" value="<?=$img->sort?>" class="img-sort" size="5">
    <?php
    endif;
    ?>


    <a class="btn set_name" style="display: none;"  title="Сохранить?" href="<?=$url_setname?>" data-id="<?=$img->id?>">
        <span class="glyphicon glyphicon-floppy-disk"></span>
    </a>


    <span class="label label-primary">Название картинки (alt, title)</span>
    <input type="text" title="Название" placeholder="Название" value="<?=$img->name?>" class="img-name">

    <?php
    if($fields['url']):?>
        <span class="label label-primary">Url ссылки</span>
        <input type="text" title="Url" placeholder="url" value="<?=$img->url?>" class="img-url">
    <?php
    endif;
    ?>




    <div class="img-role">
        <?php
        if($role && count($role) > 1):?>
            <span class="label label-primary">Назначение</span>
        <select>
            <option value="">Назначение картинки</option>
        <?php
            foreach($role as $r => $label):?>
                <option value="<?=$r?>" <?= $img->role == $r ? 'selected':''; ?> ><?=$label?></option>
            <?php
            endforeach;
            ?>
        </select>
        <?php
        elseif($role && count($role) == 1):?>
            <?php
            foreach($role as $r => $v):
            ?>
                <input type="text" value="<?=$r;?>">
            <?php
            endforeach;
            ?>
        <?php
        endif;
        ?>
    </div>
</div>
