<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 22.06.2018
 * Time: 14:05
 */

namespace app\modules\admin\components;

use yii\base\Widget;

class CheckboxWidget extends Widget
{
    public $data;
    public $model_name;
    public $attr;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $data = $this->data;
        $model_name = $this->model_name;
        $attr = $this->attr;
        return $this->render('checkbox', compact('data', 'model_name', 'attr'));
    }
}