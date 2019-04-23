<?php
use yii\helpers\Html;
use yii\helpers\Url;

$fields = [
    'name' => true,
    'url'  => true,
    'active'  => true,
];
?>
    <div class="form-image-one">
    <?php
    if(!empty($model->$field)){

        $url_delete     = Url::toRoute([$modelName.'/delete-one-img', 'page_id' => $model->id, 'field' => $field, 'model_name' => $modelName]);
        //$url_setname    = Url::toRoute([$modelName.'/setparam_one_img', 'page_id' => $model->id, 'model_name' => $modelName]);

        require 'tpl_image.php';

    }

    ?>
    </div>
