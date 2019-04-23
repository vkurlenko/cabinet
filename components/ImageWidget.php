<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 16.06.2018
 * Time: 12:05
 */
namespace app\modules\admin\components;

use yii\base\Widget;


class ImageWidget extends Widget
{
    public $model;
    public $mode;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $model = $this->model;
        $mode = $this->mode;
        $modelName = strtolower(\yii\helpers\StringHelper::basename(get_class($model)));

        //echo $modelName; die;

        return $this->render($mode.'_block', compact('model', 'modelName'));

    }
}