<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\FxMenuPart;
use app\models\FxMenuProduct;
use app\models\FxMenuProductContent;
use app\models\Hash;
use kartik\mpdf\Pdf;
use Yii;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\User;
use app\models\Status;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\models\FreeOrderForm;
use yii\web\UploadedFile;
use rico\yii2images\models\Image;

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
                        'actions' => ['view', 'success', 'error'],
                        'roles' => ['user','?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete', 'update', 'pdf', 'free-order-form', 'remove-img', 'set-main-img'],
                        'roles' => ['admin', 'director', 'manager']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete', 'pdf', 'free-order-form', 'remove-img', 'set-main-img'],
                        'roles' => ['user']
                    ],
                    
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
        self::setProductImageFromCatalog($order);

       /* if(!$order->manager && UserController::isManager()){
            $order->manager = Yii::$app->user->getId();
            $order->save(false);
        }*/

		/*if($order->status !== 1 && \app\controllers\UserController::isClient()){
            return $this->redirect(['index']);
        }
        else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }*/
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

        $session = Yii::$app->session;
        $model->uid = $session->get('fuid') ? $session->get('fuid') : $model->uid;

        $model->order_date = date('Y-m-d H:i:s');
        $model->update_date = date('Y-m-d H:i:s');
        $model->cost = 0;
        $model->payed = 0;
        $model->description = nl2br($model->description);
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->images = UploadedFile::getInstances($model, 'images');
            $model->UploadImages();

            $vars = [
                'order_number' => $model->id,
                'domain' => Yii::$app->params['mainDomain']
            ];

            /* отправим клиенту письмо о формировании заказа */
            $tpl_alias = 'new_order_for_client';
            $user = UserController::getUser($model->uid);

            $send = Yii::$app
                ->mailer
                ->compose(
                   /* ['html' => 'newOrder-html', 'text' => 'newOrder-html'],
                    ['model' => $model]*/
                    ['html' => 'tpl', 'text' => 'tpl'],
                    ['tpl_alias' => $tpl_alias, 'vars' => $vars]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($user->email)
                //->setSubject('Заказ №'.$model->id.' на сайте '.Yii::$app->params['mainDomain'].' успешно сформирован ')
                ->setSubject(MailTplController::getSubject($tpl_alias, $vars))
                ->send();
            /* /отправим клиенту письмо о формировании заказа */

            /* отправим менеджерам письмо о формировании заказа */
            $tpl_alias = 'new_order_for_manager';
            foreach(UserController::getManagersEmails() as $manager){
                $send = Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'tpl', 'text' => 'tpl'],
                        ['tpl_alias' => $tpl_alias, 'vars' => $vars]
                    )
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    ->setTo($manager['email'])
                    ->setSubject(MailTplController::getSubject($tpl_alias, $vars))
                    ->send();
            }
            /* /отправим менеджерам письмо о формировании заказа */

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

       //debug($model->fill); //ie;

        $model->update_date = date('Y-m-d H:i:s');
        $model->description = str_replace("\n", "<br />", $model->description);

        if(!$model->manager && UserController::isManager()){
             $model->manager = Yii::$app->user->getId();
             $model->save(false);
        }
		
		$old_status = $model->status; // предыдущий статус заказа
		$model->status = 7; // установим статус Заказ редактируется!!!
		if($setstatus){
			self::setStatus($id, $setstatus);
		}

        //self::setProductImageFromCatalog($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->images = UploadedFile::getInstances($model, 'images');
            $model->UploadImages();
			
			self::setLog($id, $old_status);
			
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
			'old_status' => $old_status
        ]);
    }

    /**
     * Загрузим картинку продукта, если заказ из каталога и ранее загрузок не было
     *
     * @param $model - модель заказа
     */
    public function setProductImageFromCatalog($model)
    {
        $i = $model->getImages();

        // проверим, создана ли ранее папка с картинками для этого продукта
        $is_dir = is_dir($_SERVER['DOCUMENT_ROOT'].'/web/upload/store/Orders/Orders'.$model->id);

        if(count($i) == 1 && $i[0]->urlAlias == 'placeHolder' && !$is_dir){
            if($model->product_id){
                $product = self::getProduct($model->product_id);
                $model->attachImage(Yii::$app->params['mainDocumentRoot'].'/images/restoran_menu/'.$product['ProductImgLarge']);
            }
        }
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

    public function actionFreeOrderForm()
    {
        $model = new FreeOrderForm();

        if($model->load(Yii::$app->request->post()) /*&& $model->validate()*/){

            //debug($model); die;
            $freeorder = new Orders();

            $freeorder->uid = Yii::$app->user->getId();
            $freeorder->order_date = date('Y-m-d H:i:s');
            $freeorder->update_date = date('Y-m-d H:i:s');
            $freeorder->cost = 0;
            $freeorder->payed = 0;

            $freeorder->name = 'Произвольный заказ';
            $freeorder->filling = '';
            $freeorder->description = $model->description;
            $freeorder->deliv_date = $model->deliv_date;
            $freeorder->address = $model->address;
            $freeorder->manager = 0;
            $freeorder->status =  0;

            //echo $freeorder->id; die;

            $model->images = UploadedFile::getInstances($model, 'images');
            $model->UploadImages();

            if ($freeorder->save()) {

                /* отправим клиенту письмо о формировании заказа */
                $user = UserController::getUser($freeorder->uid);

                $send = Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'newOrder-html', 'text' => 'newOrder-html'],
                        ['model' => $freeorder]
                    )
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    ->setTo($user->email)
                    ->setSubject('Заказ №'.$freeorder->id.' на сайте '.Yii::$app->params['mainDomain'].' успешно сформирован ')
                    ->send();
                /* /отправим клиенту письмо о формировании заказа */

                return $this->redirect('/orders/index');
            }

            return $this->render('/orders/index');
        }

        return $this->render('free-form', ['model' => $model]);
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
     * Вывод картинок продукта
     *
     * @param null $model - объект продукта
     * @param null $mode - режим генерации (если 'pdf', то для pdf-файла)
     *
     * @return array
     */
    public static function getProductImages($model = null, $mode = null)
    {
        $img = [];

        if($model){
            $arr = $model->getImages();

            if($arr){
                foreach($arr as $image){

                    if($mode == 'pdf'){
                        $size = $image['isMain'] ? '300x300' : '100x100';
                        $slash = Yii::$app->params['prevSlash'] ? '/' : '';
                    }
                    else{
                        $size = $image['isMain'] ? '400x400' : '115x115';
                        $slash = '/';
                    }

                    $img[] = [
                        'id' => $image['id'],
                        'isMain' => $image['isMain'], // главное изображение
                        'filePath' => Html::img($slash.$image->getPath($size), [
                            'data-origin' => $slash.$image->getPathToOrigin(),
                            //'data-origin' => $slash.$image->getPath('400x400'),
                            'data-imgid' => $image['id'],
                            'data-modelid' => $model->id,
                            ])
                    ];
                }
            }
        }

        return $img;
    }

    /**
     * Удаление картинки продукта AJAX-ом
     *
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionRemoveImg()
    {
        if(Yii::$app->request->post('imgid') && Yii::$app->request->post('modelid')){
            $imgid = Yii::$app->request->post('imgid');
            $modelid = Yii::$app->request->post('modelid');
            $remove = false;

            $model = self::findModel($modelid);

            foreach($model->getImages() as $img){
                if($img['id'] == $imgid) {
                    $remove = $model->removeImage($img);
                }
            }

            foreach($model->getImages() as $img){
                if($img->filePath != 'no-image.png'){
                    $model->setMainImage($img);
                    break;
                }
            }


            return $remove;
        }
        else
            return false;
    }

    /**
     * Сделаем картинку главной AJAX-ом
     *
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionSetMainImg()
    {
        if(Yii::$app->request->post('imgid') && Yii::$app->request->post('modelid')){
            $imgid = Yii::$app->request->post('imgid');
            $modelid = Yii::$app->request->post('modelid');
            $setMain = false;

            $model = self::findModel($modelid);

            foreach($model->getImages() as $img){
                if($img['id'] == $imgid) {
                    $setMain = $model->setMainImage($img);
                }
            }
            return $setMain;
        }
        else
            return false;
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
		Сгруппированный по категориям список продуктов
	*/
    public function getProductsGroped()
    {
        $parts = [];
        $products = [];

        // выберем все категории
        $p = FxMenuPart::find()
            ->where(['>', 'ParentID', 0])
            ->asArray()
            ->indexBy('id')
            ->all();

        foreach($p as $k => $v){
            $parts[] = $k;
        }

        // выберем все продукты
        $arr = FxMenuProduct::find()
                ->select(['id', 'PartID', 'Name', 'ProductImgLarge'])
                //->where(['IsPublish' => true])
                ->andWhere(['in', 'PartID', $parts])
                ->andWhere(['TortType' => true])
                ->orderBy(['SortID' => SORT_ASC])
                ->asArray()
                ->all();

        //debug($arr); die;

        // создадим сгруппированный по категориям массив
        if($arr){
            foreach($p as $part){
                $products[$part['Name']] = [];

                foreach($arr as $product){
                   if($product['PartID'] == $part['id']){
                       $products[$part['Name']][$product['id']] = $product['Name'];
                   }
                }
            }
            //debug($products); die;
        }

        return $products;
    }


    /**
     * Простой список продуктов без группировки
     *
     * @return array
     */
    public function getProducts()
    {
        $products = [];

        $arr = FxMenuProduct::find()
            ->select(['id', 'PartID', 'Name', 'ProductImgLarge'])
            ->where(['IsPublish' => true])
            ->andWhere(['TortType' => true])
            ->orderBy(['SortID' => SORT_ASC])
            ->asArray()
            ->all();

        //debug($arr); die;

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
     * Прочитаем данные продукта по его ID
     *
     * @param null $id - id продукта
     *
     * @return bool|null|static
     */
    public function getProduct($id = null)
    {
        if($id){
            $product = FxMenuProduct::findOne($id);

            //debug($product);

            return $product;
        }

        return false;
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
                //$fills[$product['id']] = $product['Name'];
                $fills[$product['Name']] = $product['Name'];
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
                $old_status = $order->status;

			    if($status == 30 && $order->payed != 0 && $order->status > 3){
			        Yii::$app->session->setFlash('danger', 'Нельзя удалить предоплаченный заказ или заказ, отправленный на оплату');
                }
                elseif((int)$status === 1 && $old_status == 6){
                    // если заказ был в статусе "Оплата при доставке", то при "Выставить счет" статус не меняется
                    self::doStatusAction($id, $status, $old_status);
                }
                else{
                    $order->status = $status;

                    if($order->save()){
                        self::setLog($id, $old_status);

                        self::doStatusAction($id, $status, $old_status);
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
        $isAuthor = false;


        if($order_uid){
            $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

            if($role['user'] && $order_uid == Yii::$app->user->getId()){
                //return Yii::$app->response->redirect(['orders/index']);
                $isAuthor = true;
            }

            // если заказ не этого менеджера, то провал
			if($role['manager'] && ($order_manager == 0 || $order_manager == Yii::$app->user->getId())){
                //return Yii::$app->response->redirect(['orders/index']);
                $isAuthor = true;
            }

            if(Yii::$app->user->can('admin') || Yii::$app->user->can('director'))
                $isAuthor = true;
        }


        return $isAuthor;
    }

    public function getOrderSum($order_id = null){
	    $sum = 0;

	    if($order_id){
	        $order = Orders::findOne($order_id);
            $sum = (int)$order->cost - (int)$order->payed;

            $sum = $order->tasting_set ? ($sum + Yii::$app->params['testingSetCost']) : $sum;
        }

	    return $sum;
    }

    public function getOrderCost($order_id = null){
        $sum = 0;

        if($order_id){
            $order = Orders::findOne($order_id);
            $sum = $order->tasting_set ? ((int)$order->cost + Yii::$app->params['testingSetCost']) : (int)$order->cost;
        }

        return $sum;
    }

    public function getOrderManager($order_id = null)
    {
        if($order_id) {
            $order = Orders::findOne($order_id);

            return $order->manager;
        }

        return false;
    }

    /**
     * Установка статуса ВЫПОЛНЕН для всех заказов со статусом ОПЛАЧЕН (5) и ОПЛАТА ПРИ ДОСТАВКЕ(6)
     * если текущее время позже 23.30 даты доставки
     *
     */
    public function setOrderComplete()
    {
        $orders = Orders::find()->where(['IN', 'status', [5, 6]])->all();

        foreach($orders as $order){

            // дата доставка + время 23.30
            $delive_date = strtotime($order->deliv_date." 23:30");

            // текущее время
            $now = strtotime(date("Y-m-d H:i"));

            $complete = $delive_date < $now ? true : false;

            if($complete){
                self::setStatus($order->id, 20);
            }
        }
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
			$status->uid = Yii::$app->user->getId() ? Yii::$app->user->getId() : 'unknown';
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
     * Формирование бланка заказа в PDF
     *
     * @param $id - id заказа
     * @param null $mode - режим вывода ('email' - в виде приложения к письму, по умолчанию - скачать файл)
     *
     * @return null
     * @throws NotFoundHttpException
     */
    /*public function actionPdf($id, $mode = null)
    {
        $model = $this->findModel($id);
        $this->layout = 'pdf';

        $pdf = Yii::$app->pdf;

        $mpdf = $pdf->api;
        $mpdf->SetHeader(Yii::$app->params['mainDomain']);
        $mpdf->WriteHtml($this->render('view_pdf', ['model' => $model]));

        if($mode && $mode == 'email'){
            $content = $mpdf->Output('', 'S');
            return $content;
        }
        else{
            $blank_filename = 'Бланк заказа №'.$id.'.pdf';
            echo $mpdf->Output($blank_filename, 'D'); // call the mpdf api output as needed
        }

        return null;
    }*/

    public function actionPdf($id, $mode = null)
    {
        $model = $this->findModel($id);
        $this->layout = 'text'; //'pdf';

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $css = 'strong {color: red}';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $this->render('view_pdf', ['model' => $model]),
            'cssFile' => 'css/pdf.css',
            'marginLeft' => 5,
            'marginRight' => 5,
            //'showImageErrors' =>  true,
            //'cssInline' => 'strong {color: #f00}',
            'options' => ['title' => 'Отчет для к заседанию комиссии'],
            'methods' => [
                'SetFooter' => [''],
                'SetHeader' => Yii::$app->params['mainDomain'],
                ]
        ]);

        //$pdf->showImageErrors = true;

        return $pdf->render();


        /*$pdf = Yii::$app->pdf;
        $mpdf = $pdf->api;
        $mpdf->SetHeader(Yii::$app->params['mainDomain']);
        $mpdf->WriteHtml($this->render('view_pdf', ['model' => $model]));

        if($mode && $mode == 'email'){
            $content = $mpdf->Output('', 'S');
            return $content;
        }
        else{
            $blank_filename = 'Бланк заказа №'.$id.'.pdf';
            echo $mpdf->Output($blank_filename, 'D'); // call the mpdf api output as needed
        }*/


        //return null;
    }


    /**
     * Действия при изменении статуса заказа
     *
     * @param null $order_id - id заказа на сайте
     * @param null $status - установленный статус заказа
     * @param null $old_status - пердыдущий статус заказа
     *
     * @return bool
     */
    protected function doStatusAction($order_id = null, $status = null, $old_status = null)
    {
        if($order_id && $status){
            $order = Orders::findOne($order_id);
            $user = User::findOne($order->uid);

            switch($status){

                // отправить бланк заказа клиенту на email
                case 1 :

                    if(!OrdersController::getOrderSum($order_id)){
                        Yii::$app->session->setFlash('danger', 'Сумма заказ должна быть больше 0');
                        break;
                    }

                    // прикрепим бланк заказа
                    $blank_content = Yii::$app->runAction('orders/pdf', ['id' => $order_id, 'mode' => 'email']);
                    $blank_filename = 'Бланк заказа №'.$order_id.'.pdf';

                    // если "Оплата при доставке"
                    if($old_status == 6){
                        // ссылка на оплату
                        $link = 'Ссылка на оплату не нужна';
                    }
                    else{

                        // ссылка на оплату
                        $link = self::getPayLink($order_id);
                    }

                    $vars = [
                        'link' => Html::a($link, $link)
                    ];


                    $send = Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => 'tpl', 'text' => 'tpl'],
                            ['tpl_alias' => 'pay_link', 'vars' => $vars]
                        )
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                        ->setTo($user->email)
                        ->setSubject('Ваша ссылка для оплаты заказа на сайте '.Yii::$app->params['mainDomain'])
                        ->attachContent($blank_content, ['fileName' => $blank_filename, 'contentType' => 'application/pdf'])
                        ->send();

                    if($send)
                        Yii::$app->session->setFlash('success', 'Ссылка на оплату по заказу №'.$order_id.' отправлена клиенту на адрес '.$user->email);
                    else
                        Yii::$app->session->setFlash('danger', 'Ошибка отправки ссылки на оплату по заказу №'.$order_id);

                    break;

                // заказа выполнен (отправка письма менеджеру)
                case 20:
                    $tpl_alias = 'order_complete';
                    $vars = [
                        'order_number' => $order_id
                    ];
                    $manager_id = self::getOrderManager($order_id);

                    if($manager_id){
                        $manager_email = self::getUserEmail($manager_id);

                        $send = Yii::$app
                            ->mailer
                            ->compose(
                                ['html' => 'tpl', 'text' => 'tpl'],
                                ['tpl_alias' => $tpl_alias, 'vars' => $vars]
                            )
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                            ->setTo($manager_email)
                            ->setSubject(MailTplController::getSubject($tpl_alias, $vars))
                            ->send();
                    }

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
            $hash = self::setOrderHash($order_id);
            $link = Yii::$app->params['subDomain'].'/orders/view?id='.$order_id.'&hash='.$hash;
        }

        return $link;
    }

    /**
     * Генерация hash-строки для формирования ссылки на оплату
     * @param $order_id - id заказа в системе сайта
     * @return string
     *
     * TODO сделать уборщик недействительных hash
     */
    protected function setOrderHash($order_id = null)
    {
        if(!$order_id)
            return false;
             //throw new NotFoundHttpException('User not found');

        $oldHash = Hash::deleteAll(['order_id' => $order_id]);

        $hash = new Hash();

        // id заказа
        $hash->order_id = $order_id;

        // hash
        $hash->hash = md5($order_id.time());

        // дата время генерации
        $hash->hash_datetime = date('Y-m-d H:i:s');

        // сохраним hash в БД
        $hash->save(false);

        return $hash->hash;
    }

    /**
     * Проверка действительности ссылки на оплату по ее hash,
     * если ссылка просрочена, то удалим hash из БД
     *
     * @param string $hash
     * @return bool
     */
    public function validHash($hash = '')
    {
        $period = Yii::$app->params['hashLifetime']; // период жизни hash
        $hash_valid = false;

        $hash = Hash::find()->where(['hash' => Yii::$app->request->get('hash')])->one();

        if($hash){
            //echo  ((int) strtotime($hash->hash_datetime) + (int) $period) . ' > ' . time();

            if((int) (strtotime($hash->hash_datetime) + (int) $period) > time())
                $hash_valid = true;
        }

        if(!$hash_valid)
            self::deleteHash(Yii::$app->request->get('hash'));

        //echo 'hash_valid = '.$hash_valid;

        return $hash_valid;
    }

    /**
     * Удаление строки с hash из БД
     * @param null $hash
     */
    protected function deleteHash($hash = null)
    {
        if($hash)
            Hash::deleteAll(['hash' => $hash]);
    }

    public function actionSuccess()
    {
        if(Yii::$app->request->get('orderId')){
            $res = PayController::getPayStatus(Yii::$app->request->get('orderId'));

            //debug($res);

            if($res['orderStatus'] === 1){
                //echo __LINE__;
                if(PayController::depositDo(Yii::$app->request->get('orderId'))){
                    //echo __LINE__;
                    self::setStatus(Yii::$app->request->get('id'), 5); // заказ оплачен

                    $order = $this->findModel(Yii::$app->request->get('id'));
                    $order->payed = $order->payed + $res['amount'] / 100;
                    $order->save(false);
                }
            }
        }

        return $this->render('success');
    }


}
