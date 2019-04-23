<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.07.2018
 * Time: 00:54
 */

namespace app\modules\admin\components;


use yii\base\Widget;

class ImageOneWidget extends Widget
{
    public $model;
    public $field;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $model = $this->model;
        $field = $this->field;

        $modelName = strtolower(\yii\helpers\StringHelper::basename(get_class($model)));

        //echo $modelName; die;

        return $this->render('image_one_block', compact('model', 'modelName', 'field'));

    }
}