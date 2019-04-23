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
            'url'  => true,
            'active'  => true,
    ];

    $gallery = $model->getImages();
    $gallery2 = [];
    $arr = Image::find()->asArray()->where(['itemId' => $model->id])->orderBy(['sort' => SORT_ASC])->all();

    foreach($arr as $row){
        foreach($gallery as $img){
            if($img->id == $row['id']){
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
    <div style="clear:both;"></div>
</div>
<!--<a class="save-gallery">Сохранить</a>-->