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
     * сформируем ссылку на оплату счета клиентом
     *
     * @param null $order_id - номер заказа в системе сайта
     *
     * @return boolean
     */
    public function actionPayOrder($order_id = null)
    {
        if($order_id){

            $order = Orders::findOne($order_id);

            if($order){
                $sum = $order->cost;

                if($sum){
                    $vars = array();
                    $vars['userName'] = Yii::$app->params['merchantLogin']; //'логин';
                    $vars['password'] = Yii::$app->params['merchantPwd']; //'пароль';

                    // ID заказа в магазине.
                    $vars['orderNumber'] = $order_id;

                    // Сумма заказа в копейках.
                    $vars['amount'] = $sum * 100;

                    // URL куда клиент вернется в случае успешной оплаты.
                    $vars['returnUrl'] = Yii::$app->params['subDomain'].'/success/';

                    // URL куда клиент вернется в случае ошибки.
                    $vars['failUrl'] = Yii::$app->params['subDomain'].'/error/';

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
                        self::setPayItem($order_id, null, $res['errorCode'], $res['errorMessage']);

                        echo '<p class="alert alert-danger">'.$res['errorMessage'].'</p>';
                    }
                    else {
                        // Успех:
                        // Тут нужно сохранить ID платежа в своей БД - $res['orderId']
                        self::setPayItem($order_id, $res['orderId']);

                        // Перенаправление клиента на страницу оплаты.
                        header('Location: ' . $res['formUrl'], true);

                        // Или на JS
                        echo '<script>document.location.href = "' . $res['formUrl'] . '"</script>';
                        //$link = $res['formUrl'];
                    }
                }
            }
        }

        return false;
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
    protected function setPayItem($orderNumber = null, $orderId = null, $errorCode = null, $errorMessage = null)
    {
        $pay = new Pay();

        $pay->orderNumber = $orderNumber;
        $pay->orderId = $orderId;
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
