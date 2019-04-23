<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 20.06.2018
 * Time: 18:27
 */

namespace app\modules\admin\components;

use yii\base\Widget;

class SortWidget extends Widget
{
    public $data;
    public $model_name;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $data = $this->data;
        $model_name = $this->model_name;;

        return $this->render('sort_block', compact('data', 'model_name'));
    }
}