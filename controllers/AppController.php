<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 14.04.2019
 * Time: 19:44
 */

namespace app\controllers;

use Yii;
use yii\base\Controller;

class AppController extends Controller
{
    public $arr;

    public function debug($arr = null){
        return '<pre>'.print_r($arr).'</pre>';
    }

    public function getRole()
    {
        $role = '';

        $arr = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        return $arr;
    }

    public function getRoleDescription()
    {
        $arr = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
    }

    public function formatDate($datetime, $showTime = false)
    {
        $format = $showTime ? 'd.m.Y h:i:s' : 'd.m.Y';
        return date($format ,strtotime($datetime));
    }
}