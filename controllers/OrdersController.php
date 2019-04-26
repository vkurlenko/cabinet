<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\FxMenuProduct;
use app\models\FxMenuProductContent;
use Yii;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
		}
	
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->update_date = date('Y-m-d H:i:s');
		
		$old_status = $model->status;
		$model->status = 7; // заказ редактируется!!!

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

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
	
	public function getUserEmail($uid = null)
	{
		$email = '';
		if($uid){
			$arr = \app\controllers\UserController::getUser($uid);
			
			$email = $arr['email'];
		}
		
		return $email;
	}
	
	public static function setStatus($id = null, $status = null)
	{
		if($id && $status){
			$order = Orders::find()->where(['id' => $id])->one();
			
			if($order){
				$order->status = $status;
			}
			
			$order->save();			
		}
	}


}
