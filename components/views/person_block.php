<?php
use yii\helpers\Url;
use yii\db\ActiveRecord;
use rico\yii2images\models\Image;
use app\modules\admin\models\Pages;
use app\modules\admin\controllers\PagesController;
?>

<div class="form-gallery">

    <?php

    $fields = [
        'name' => true,
        'sort' => true,
        'role' => true,
        'url'  => false,
        'active'  => true,
    ];

    $role = [
        'photo_big'     => 'Большое фото',
        'photo_small'   => 'Маленькое фото',
        'photo_cake'    => 'Фото торта',
        'photo_on_main' => 'Фото на главную'
    ];

    $gallery = $model->getImages();
    $gallery2 = [];
    $arr = Image::find()->asArray()->where(['itemId' => $model->id])->orderBy(['sort' => SORT_ASC])->all();

    foreach($arr as $row){
        foreach($gallery as $img){
            if($img->id == $row['id'] && $img->role != 'video'){
                $gallery2[] = $img;
            }
        }
    }


    foreach($gallery2 as $img){
        $url_delete     = Url::toRoute([$modelName.'/deleteimg',  'page_id' => $model->id, 'img_id' => $img->id, 'model_name' => $modelName]);
        $url_setname    = Url::toRoute([$modelName.'/setnameimg', 'page_id' => $model->id, 'img_id' => $img->id, 'model_name' => $modelName]);
        require 'tpl_gallery.php';
    }
    ?>
</div>