<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\AuthItem;
use app\models\Orders;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Application;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $role;
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
                        'actions' => ['update', 'view',],
                        'roles' => ['admin', 'director', 'manager', 'user']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete', 'set-fake-uid', 'unset-fake-uid'],
                        'roles' => ['admin', 'director', 'manager']
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();


        if(Yii::$app->request->get('temp')){
            $row = (new \yii\db\Query())
                ->select(['value'])
                ->from('temp')
                ->where(['id' => Yii::$app->request->get('temp')])
                ->one();

            if($row['value']){
                $arr = unserialize(base64_decode($row['value']));
                $model->username = $arr['name'];
                $model->phone = $arr['phone'];
                $model->email = $arr['email'];
            }

            Yii::$app->session->set('temp_order', Yii::$app->request->get('temp'));

        }

        if ($model->load(Yii::$app->request->post())/* && $model->save()*/) {

            $post = Yii::$app->request->post();

            // сохраним пароль
            $model->setPassword($post['User']['password_hash']);
            $model->generateAuthKey();

            $model->save(false);

            /* создание заказа из temp */
            if(Yii::$app->session->has('temp_order')){

                $row = (new \yii\db\Query())
                    ->select(['value'])
                    ->from('temp')
                    ->where(['id' => Yii::$app->session->get('temp_order')])
                    ->one();

                /*
                 * [newcab] => 1
                    [date] => 2019-07-10
                    [fill_id] => 163
                    [name] => Курленко Екатерина
                    [phone] => +79997355178
                    [email] => vkurlenko@ramwbler.ru
                    [customer_address] =>
                    [customer_description] =>
                    [tort_name] => Свадебный торт с подсветкой
                    [part_name] => СВАДЕБНЫЕ ТОРТЫ
                    [nachinka] =>
                    [price] => 0
                    [page_name] => /svadebnye-torty/sinij-svadebnyj-tort-s-geometricheskim-uzorom.html
                    [tort_id] => 3149
                    [tasting_set] => 0
                    [fuid] =>
                    [DoOrder] => Отправить заказ
                    [degustation] => Нет
                    [send] => Y*/

                if($row){
                    $arr = unserialize(base64_decode($row['value']));

                    //debug($arr); die;

                    $params = [
                        'uid' => $model->id,
                        'product_id' => $arr['tort_id'],
                        'name' => $arr['tort_name'],
                        'filling' => $arr['nachinka'],
                        'tasting_set' => $arr['tasting_set'],
                        'description' => $arr['customer_description'],
                        'deliv_date' => $arr['date'],
                        'deliv_name' => $arr['name'],
                        'deliv_phone' => $arr['phone'],
                        'address' => $arr['customer_address'],
                        'order_date' => date('Y-m-d H:i:s'),
                        'update_date' => date('Y-m-d H:i:s')
                    ];

                    //debug($params);
                }

                Yii::$app->db->createCommand()->insert('orders', $params)->execute();

                //debug(Yii::$app -> db -> getLastInsertID()); die;
                $new_order_id = Yii::$app -> db -> getLastInsertID();
            }
            /* /создание заказа из temp */

            //debug($post['User']); die;
            if($post['User']['role']){
                $role = new AuthAssignment();
                $role->user_id = $model->id;
                $role->item_name = $post['User']['role'];
                $role->save(false);

                $vars = [
                    'user_name' => $model->username,
                    'user_role' => self::getRoleName($post['User']['role']),
                    'user_login' => $model->email,
                    'user_password' => $post['User']['password_hash'],
                    'domain' => Yii::$app->params['mainDomain']
                ];

                $tpl_alias = 'new_role';

                $send = Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'tpl', 'text' => 'tpl'],
                        ['tpl_alias' => $tpl_alias, 'vars' => $vars]
                    )
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    ->setTo($model->email)
                    ->setSubject(MailTplController::getSubject($tpl_alias, $vars))
                    ->send();

                if($new_order_id){
                    if($send){
                        Yii::$app->session->setFlash('success', 'Пользователь '.$model->username.' успешно зарегистрирован!');

                        /* отправим клиенту письмо о формировании заказа */
                        $vars = [
                            'order_number' => $new_order_id,
                            'domain' => Yii::$app->params['mainDomain']
                        ];

                        $tpl_alias = 'new_order_for_client';
                        $user = UserController::getUser($model->id);
                        UserController::actionSetFakeUid($model->id);

                        // прикрепим бланк заказа
                        $blank_content = Yii::$app->runAction('orders/pdf', ['id' => $new_order_id, 'mode' => 'email']);
                        $blank_filename = 'Бланк заказа №'.$new_order_id.'.pdf';

                        $send = Yii::$app
                            ->mailer
                            ->compose(
                                ['html' => 'tpl', 'text' => 'tpl'],
                                ['tpl_alias' => $tpl_alias, 'vars' => $vars]
                            )
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                            ->setTo($user->email)
                            //->setSubject('Заказ №'.$model->id.' на сайте '.Yii::$app->params['mainDomain'].' успешно сформирован ')
                            ->setSubject(MailTplController::getSubject($tpl_alias, $vars))
                            ->attachContent($blank_content, ['fileName' => $blank_filename, 'contentType' => 'application/pdf'])
                            ->send();

                        if($send)
                            Yii::$app->session->setFlash('success', 'Письмо о создании заказа №'.$new_order_id.' отправлено клиенту на адрес '.$user->email);

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
                    }

                    else
                        Yii::$app->session->setFlash('danger', 'Ошибка регистрации пользователя '.$model->username);
                }

            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            // сохраним пароль
           /* echo $model->password_hash;
            die;
            if($model->password_hash){
                $model->setPassword($model->password_hash);
                $model->generateAuthKey();
            }*/

            $post = Yii::$app->request->post();



            if($post['User']['role']){
                $role = AuthAssignment::find()->where(['user_id' => $model->id])->one();
                $role->item_name = $post['User']['role'];
                $role->save(false);
            }

            if($post['User']['password_hash']){
                $model->setPassword($post['User']['password_hash']);
                $model->generateAuthKey();
            }



            if($model->save()){
                if(Yii::$app->user->can('manager')){
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else{
                    return $this->redirect(['orders/index']);
                }

            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        // удаление пользователя из таблицы ролей
        $user = AuthAssignment::find()->where(['user_id' => $id])->one();
        $user->delete();

        return $this->redirect(['index']);
    }

    /**
     * получим роль пользователя в виде строки (Клиент Администратор)
     * */
    public function getUserRole($uid = null)
    {
        $roles = '';
        if($uid){
            $arr = Yii::$app->authManager->getRolesByUser($uid);

            $roles = '';

            foreach($arr as $role){
                $roles .= $role->description;
            }
        }

        return $roles;
    }

    /**
     * получим название роли по ее алиасу (admin => Адмиистратор)
     * */
    public function getRoleName($role = null)
    {
        $roleName = '';

        //echo $role; die;

        if($role){
            $arr = AuthItem::find()->where(['name' => $role])->one();
            if($arr){
                $roleName = $arr['description'];
            }
        }

        return $roleName;
    }

    /**
    *   получим данные пользователя
     */
	public function getUser($uid = null)
	{	
		$user = [];
		
		if($uid){
			$user = User::findOne($uid);
		}
		
		return $user;
	}

    /**
     * Вывод блока информации о текущем пользоватле (только для клиента)
     * @param null $uid
     * @return string
     */
    public function renderUserInfo($uid = null)
    {

        if($uid){
            $user = User::find()->where(['id' => $uid])->asArray()->one();
            return $this->render('/blocks/userinfo', compact('user'));
        }
    }


    /**
     * Является ли текущий пользователь клиентом
     *
     * @return bool
     */
    public function isClient()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        if(count($roles) == 1 && $roles['user'])
            return true;
        else
            return false;
    }

    /**
     * Есть ли заказ менеджером от имени клиента
     *
     * @return $user (array) || false
     */
    public function isFakeClient()
    {
        $fuid = isset($_SESSION['fuid']) ? $_SESSION['fuid'] : null;

        if($fuid){
            return self::getUser($fuid);
        }

        return false;
    }

    /**
     * Является ли текущий пользователь менеджером
     *
     * @return bool
     */
    public function isManager()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        if(count($roles) == 1 && $roles['manager'])
            return true;
        else
            return false;
    }

    /**
     * Является ли текущий пользователь директором
     *
     * @return bool
     */
    public function isDirector()
    {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());

        if(count($roles) == 1 && $roles['director'])
            return true;
        else
            return false;
    }

    public function getManagersAll()
    {
        $managers = ['admin', 'director', 'manager'];

        $arr = AuthAssignment::find()->where(['IN', 'item_name', $managers])->asArray()->all();

        return $arr;
    }

    public function getManagersEmails()
    {
        $arr = self::getManagersAll();
        $ids = [];
        $emails = [];

        foreach($arr as $manager){
            $ids[] = $manager['user_id'];
        }

        if($ids){
            $emails = User::find()->select('email')->where(['IN', 'id', $ids])->asArray()->all();
        }

        return $emails;
    }

    /**
     * Запишем в сессию id клиента, от имени которого работает менеджер
     * @param bool $fuid
     */
    public function actionSetFakeUid($fuid = false)
    {
        if($fuid){
            $session = Yii::$app->session;
            $session->set('fuid', $fuid);
        }
    }

    /**
     * Удалим из сессии id клиента, от имени которого работает менеджер
     * @return \yii\web\Response
     */
    public function actionUnsetFakeUid()
    {
            $session = Yii::$app->session;
            $session->remove('fuid');

            return $this->redirect(['index', 'role' => 'user']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}
