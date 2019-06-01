<?php

namespace app\commands;


use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Orders;

class CompliterController extends Controller
{
    public function actionIndex()
    {
        /*$f = fopen('test.txt', 'a+');
        echo fwrite($f, date('H:i:s')."\r\n");*/

        /*$orders = Orders::find()->where(['IN', 'status', [5, 6]])->all();

        debug($orders);*/

        /*$connection = \Yii::$app->db;

        var_dump($connection);*/

        $orders = Orders::find()->where(['IN', 'status', [5, 6]])->all();

        return ExitCode::OK;

    }
}