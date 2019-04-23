<?php
use yii\helpers\Url;

$fields = [
    'name' => true,
];
?>
<div class="form-gallery">
<?php


$img = $model->getImage();
$url_delete     = Url::toRoute([$modelName.'/deleteimg', 'page_id' => $model->id, 'img_id' => $img->id, 'model_name' => $modelName]);
$url_setname    = Url::toRoute([$modelName.'/setnameimg', 'page_id' => $model->id, 'img_id' => $img->id, 'model_name' => $modelName]);

require 'tpl_gallery.php';
?>
</div>
