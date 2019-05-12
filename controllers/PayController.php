<?php

namespace app\controllers;

use app\models\Orders;
use Yii;
use app\models\Pay;
use app\models\PaySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayController implements the CRUD actions for Pay model.
 */
class PayController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Pay models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pay model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Pay model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pay();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Pay model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Pay model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Отправим клиента на форму оплаты банка
     *
     * @param null $order_id - номер заказа в системе сайта
     *
     * @return boolean
     */
    public function actionPayOrder($order_id = null)
    {
        if($order_id){

            // установим статус заказа 'Отправлен в банк'
            OrdersController::setStatus($order_id, 4);

            $order = Orders::findOne($order_id);

            if($order){

                // сумма к оплате
                /*$sum = $order->cost;

                if($order->payed)
                    $sum = $sum - $order->payed;*/
                $sum = \app\controllers\OrdersController::getOrderSum($order_id);

                // создадим новую запись в таблице платежей (транзакций)
                $pay = new Pay();

                if($pay->save(false) && $sum){

                    // 2 стадийная оплата
                    self::registerPreAuth($pay, $sum, $order_id);
                }
            }
        }

        return false;
    }

    /**
     * Получим статус платежа по его ID в платежной системе
     *
     * @param null $pay_id - ID платежа в платежной системе
     *
     * @return array|mixed
     */
    public static function getPayStatus($pay_id = null)
    {
        $res = [];

        if($pay_id){

            $vars = array();
            $vars['userName'] = Yii::$app->params['merchantLogin']; //'логин';
            $vars['password'] = Yii::$app->params['merchantPwd']; //'пароль';
            $vars['orderId'] = $pay_id;

            $ch = curl_init('https://3dsec.sberbank.ru/payment/rest/getOrderStatusExtended.do?' . http_build_query($vars));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $res = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($res, JSON_OBJECT_AS_ARRAY);
            //debug($res);
        }

        return $res;
    }

    /**
     * 2-стадийная оплата
     *
     * @param $pay
     * @param $sum - сумма платежа
     * @param $order_id - id заказа в системе сайта
     *
     * @return bool|\yii\web\Response
     */
    protected function registerPreAuth($pay, $sum, $order_id)
    {
        $vars = array();
        $vars['userName'] = Yii::$app->params['merchantLogin']; //'логин';
        $vars['password'] = Yii::$app->params['merchantPwd']; //'пароль';

        // ID платежа в магазине
        $vars['orderNumber'] = $pay->id; //$order_id;

        // Сумма заказа в копейках.
        $vars['amount'] = $sum * 100;

        // URL куда клиент вернется в случае успешной оплаты.
        $vars['returnUrl'] = Yii::$app->params['subDomain'].'/orders/view?id='.$order_id;

        // URL куда клиент вернется в случае ошибки.
        $vars['failUrl'] = Yii::$app->params['subDomain'].'/orders/view?id='.$order_id;

        // Описание заказа, не более 24 символов, запрещены % + \r \n
        $vars['description'] = 'Заказ №' . $order_id . ' на '.Yii::$app->params['subDomain'];

        $ch = curl_init('https://3dsec.sberbank.ru/payment/rest/registerPreAuth.do?' . http_build_query($vars));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, JSON_OBJECT_AS_ARRAY);

        //debug($res); die;

        if (empty($res['orderId'])){
            // Возникла ошибка:
            self::setPayItem($pay, $order_id, null, $vars['amount'], $res['errorCode'], $res['errorMessage']);
            Yii::$app->session->setFlash('danger', $res['errorMessage']);
            return $this->redirect(['/orders/view', 'id' => $order_id]);
        }
        else {
            // Успех:
            // Тут нужно сохранить ID платежа в своей БД - $res['orderId']

            self::setPayItem($pay, $order_id, $res['orderId'], $vars['amount']);
            Yii::$app->session->setFlash('success', 'Платеж зарегистрирован');

            // Перенаправление клиента на страницу оплаты.
            header('Location: ' . $res['formUrl'], true);

            // Или на JS
            echo '<script>document.location.href = "' . $res['formUrl'] . '"</script>';
        }

        return true;
    }

    /**
     * Завершение оплаты, если платеж был подтвержден
     *
     * @param $orderId - id платежа в платежной системе (fd010571-eae9-70aa-8380-a60404b424c3
     * )
     * @return bool
     */
    public static function depositDo($orderId)
    {
        $vars = array();
        $vars['userName'] = Yii::$app->params['merchantLogin']; //'логин';
        $vars['password'] = Yii::$app->params['merchantPwd']; //'пароль';

        // Номер заказа в платежной системе.
        $vars['orderId']  = $orderId;

        // Сумма платежа в копейках, Если указать 0, то завершение произойдет на всю сумму.
        $vars['amount'] = 0;

        $ch = curl_init('https://3dsec.sberbank.ru/payment/rest/deposit.do?' . http_build_query($vars));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, JSON_OBJECT_AS_ARRAY);
        if (!empty($res['errorCode'])) {
            Yii::$app->session->setFlash('danger', $res['errorMessage']);
            return false;
        } else {
            Yii::$app->session->setFlash('success', 'Оплата завершена');
            return true;
        }
    }

    /**
     * Сохраним в БД запись о проведенной операции по оплате
     *
     * @param null $orderNumber - id заказа в системе сайта
     * @param null $orderId - id заказа в платежной системе
     * @param null $errorCode - код ошибки
     * @param null $errorMessage - текст ошибки
     *
     * @return mixed
     */
    protected function setPayItem($pay, $orderNumber = null, $orderId = null, $amount = null, $errorCode = null, $errorMessage = null)
    {
        //$pay = new Pay();

        $pay->orderNumber = $orderNumber;
        $pay->orderId = $orderId;
        $pay->amount = $amount / 100;
        $pay->errorCode = $errorCode;
        $pay->errorMessage = $errorMessage;
        $pay->datetime = date('Y-m-d H:i:s');

        $res = $pay->save(false);

        return $res;
    }


    /**
     * Finds the Pay model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pay the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pay::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
