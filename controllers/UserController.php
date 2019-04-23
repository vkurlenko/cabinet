<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\AuthItem;
use Yii;
use app\models\User;
use app\models\UserSearch;
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

        if ($model->load(Yii::$app->request->post())/* && $model->save()*/) {

            // сохраним пароль
            $model->setPassword($model->password_hash);
            $model->generateAuthKey();

            $model->save();

            $post = Yii::$app->request->post();

            if($post['User']['role']){
                $role = new AuthAssignment();
                $role->user_id = $model->id;
                $role->item_name = $post['User']['role'];
                $role->save(false);
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

            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
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
