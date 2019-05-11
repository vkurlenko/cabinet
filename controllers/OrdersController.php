<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\FxMenuProduct;
use app\models\FxMenuProductContent;
use Yii;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\User;
use app\models\Status;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete', 'update'],
                        'roles' => ['admin', 'director', 'manager']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete'],
                        'roles' => ['user']
                    ],
                   /* [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['admin', 'director', 'manager']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete'],
                        'roles' => ['admin', 'director', 'manager', 'user']
                    ],*/
                ],
            ],
        ];
    }


    /** 
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrdersSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $setstatus = null)
    {
		if($setstatus){
			self::setStatus($id, $setstatus);
            return $this->redirect(['view', 'id' => $id]);
		}

		// если пришел id платежа (fd010571-eae9-70aa-8380-a60404b424c3),
        // то проверим его статус и если он подтвержден (т.е. оплата прошла),
        // установим статус "Оплачен" в системе сайта
		if(Yii::$app->request->get('orderId')){
		    $res = PayController::getPayStatus(Yii::$app->request->get('orderId'));

		    if($res['orderStatus'] === 1){
                if(PayController::depositDo(Yii::$app->request->get('orderId'))){
                    self::setStatus($id, 5); // заказ оплачен

                    $order = $this->findModel($id);
                    $order->payed = $order->payed + $res['amount'] / 100;
                    $order->save(false);
                }
            }
        }

        // клиент может просмотреть заказ (и оплатить) только если статус "Выставлен счет"
        $order = Orders::findOne($id);
		if($order->status !== 1 && \app\controllers\UserController::isClient()){
            return $this->redirect(['index']);
        }
        else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Orders();

        $model->order_date = date('Y-m-d H:i:s');
        $model->update_date = date('Y-m-d H:i:s');
        $model->cost = 0;
        $model->payed = 0;
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            /* отправим клиенту письмо о формировании заказа */
            $user = UserController::getUser($model->uid);

            $send = Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'newOrder-html', 'text' => 'newOrder-html'],
                    ['model' => $model]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($user->email)
                ->setSubject('Заказ №'.$model->id.' на сайте '.Yii::$app->params['mainDomain'].' успешно сформирован ')
                ->send();
            /* /отправим клиенту письмо о формировании заказа */

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $setstatus = 7)
    {
        $model = $this->findModel($id);

        $model->update_date = date('Y-m-d H:i:s');
		
		$old_status = $model->status; // предыдущий статус заказа
		$model->status = 7; // установим статус Заказ редактируется!!!
		if($setstatus){
			self::setStatus($id, $setstatus);
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			self::setLog($id, $old_status);
			
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
			'old_status' => $old_status
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $setstatus = 30)
	{
		// проверка прав на удаление
		$model = $this->findModel($id);
		if(self::checkMyOrder($model->uid, $model->manager)){
            if($setstatus)
                self::setStatus($id, $setstatus);
        }
        
		//self::setLog($id, $model->status, $setstatus);
		
		//$this->findModel($id)->delete();
		
		return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * получим список клиентов
     * @return array
     */
	public function getPersons($role = null)
	{
	    if($role){
            $arr = [];

            $users = AuthAssignment::find()
                ->select('user_id')
                ->where(['item_name' => $role])
                ->asArray()
                ->all();

            if($users){
                $a = [];
                foreach($users as $k => $v){
                    $a[] = $v['user_id'];
                }
            }

            $clients = User::find()
                ->select(['id', 'username'])
                ->where(['id' => $a])
                ->asArray()
                ->all();

            if($clients){
                foreach($clients as $user){
                    $arr[$user['id']] = $user['username'];
                }
            }
        }
		
		return $arr;
	}

	/**
		список тортов
	*/
    public function getProducts()
    {
        $products = [];

        $arr = FxMenuProduct::find()
                ->select(['id', 'Name', 'ProductImgLarge'])
                ->where(['IsPublish' => true])
                ->andWhere(['TortType' => true])
                ->asArray()
                ->all();

        if($arr){
            foreach($arr as $product){
                $products[$product['id']] = [
					'name' => $product['Name'], 
					'img' => $product['ProductImgLarge']
					];
            }
        }

        return $products;
    }

	/**
		список начинок
	*/
    public function getFills()
    {
        $fills = [];

        $arr = FxMenuProductContent::find()
            ->select(['id', 'Name'])
            ->where(['IsPublish' => true])
            ->asArray()
            ->all();

        if($arr){
            foreach($arr as $product){
                $fills[$product['id']] = $product['Name'];
            }
        }

        return $fills;
    }

    /**
     * получить email пользователя по его ID
     *
     */
	public function getUserEmail($uid = null)
	{
		$email = '';
		if($uid){
			$arr = \app\controllers\UserController::getUser($uid);
			
			$email = $arr['email'];
		}
		
		return $email;
	}

	/**
	 * изменение статуса заказа
	 * $id - id заказа
	 * $status - новый статус заказа
     */
	public static function setStatus($id = null, $status = null)
	{
		if($id && $status){
			$order = Orders::find()->where(['id' => $id])->one();
			
			if($order){

			    if($status == 30 && $order->payed != 0 && $order->status > 3){
			        Yii::$app->session->setFlash('danger', 'Нельзя удалить предоплаченный заказ или заказ, отправленный на оплату');
                }
                else{
                    $old_status = $order->status;
                    $order->status = $status;

                    if($order->save()){
                        self::setLog($id, $old_status);

                        self::doStatusAction($id, $status);
                    }
                }
			}					
		}
	}

    /**
     * проверка прав пользователя на просмотр/редактирование заказа
     * $order_uid - id заказчика
     */
	public function checkMyOrder($order_uid = null, $order_manager = null)
    {
        $isAuthor = true;

        if($order_uid){
            $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

            // если заказ не этого клиента, то провал
            if($role['user'] && $order_uid != Yii::$app->user->getId()){
                return Yii::$app->response->redirect(['orders/index']);
            }

            // если заказ не этого менеджера, то провал
			if($role['manager'] && $order_manager != Yii::$app->user->getId()){
                return Yii::$app->response->redirect(['orders/index']);
            }
        }
        else
            $isAuthor = true;


        return $isAuthor;
    }
	
	/**
     * логирование изменения статусов заказа
     * $order_id - id заказа
     * $old_status - прежний статус заказа
	 */
	public function setLog($order_id = null, $old_status = null, $new_status = null)
	{		
		if($order_id){
			$model = Orders::find()->where(['id' => $order_id])->one();
			
			$status = new Status();
			$status->order_id = $model->id;
			$status->datetime = date('Y-m-d H:i:s');
			$status->uid = Yii::$app->user->getId();			
			$old_status ? $status->old_status = $old_status : $status->old_status = 0;					
			$new_status ? $status->new_status = $new_status : $status->new_status = $model->status;			
			$status->save(false);
		}
		
		return true;
	}
	
	/**
     * Печать лога изменения статусов заказа
     *
     * $order_id - id заказа
	 */
	public function getLog($order_id = null)
	{
		if($order_id){
		    $logs = Status::find()
                ->where(['order_id' => $order_id])
                ->orderBy(['datetime' => SORT_DESC])
                ->asArray()
                ->all();

            return $this->render('/blocks/log', compact('logs'));
        }

        return true;
	}


    /**
     * Действия при измененеии статуса заказа
     *
     * @param null $order_id - id заказа на сайте
     * @param null $status - установленный статус заказа
     *
     * @return bool
     */
    protected function doStatusAction($order_id = null, $status = null)
    {
        if($order_id && $status){
            $order = Orders::findOne($order_id);
            $user = User::findOne($order->uid);

            switch($status){

                // отправить ссылку на оплату клиенту
                case 1 :
                    $link = self::getPayLink($order_id);

                    $send = Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => 'payLink-html', 'text' => 'payLink-html'],
                            ['user' => $user, 'link' => Html::a($link, $link)]
                        )
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                        ->setTo($user->email)
                        ->setSubject('Ваша ссылка для оплаты заказа на сайте '.Yii::$app->params['mainDomain'])
                        ->send();

                    if($send)
                        Yii::$app->session->setFlash('success', 'Ссылка на оплату по заказу №'.$order_id.' отправлена клиенту на адрес '.$user->email);
                    else
                        Yii::$app->session->setFlash('danger', 'Ошибка отправки ссылки на оплату по заказу №'.$order_id);

                    break;

                // перезаказ
                case 40:
                    $order_copy = new Orders();
                    $order_copy->attributes = $order->attributes;
                    $order_copy->status = 0;
                    if(!$order_copy->save()){
                        //either print errors or redirect
                        Yii::$app->session->setFlash('danger', $order_copy->getErrors());
                    }
                    else
                        Yii::$app->session->setFlash('success', 'Перезаказ успешно зарегистрирован '.Html::a('Перейти в новый заказ', '/orders/view?id='.$order_copy->id));
                    break;

                default: break;
            }
        }

        return true;
    }

    /**
     * Генерация ссылки на оплату
     *
     * @param $order_id - id заказа
     *
     * @return string - ссылка
     */
    protected function getPayLink($order_id = null)
    {
        $link = '';

        if($order_id){
            $link = Yii::$app->params['subDomain'].'/orders/view?id='.$order_id;
        }

        return $link;
    }


}
