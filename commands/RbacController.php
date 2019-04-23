<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
	public function actionInit()
	{
		$auth = Yii::$app->authManager;
		
		//$auth->removeAll();		
		
		/* пользователи */
		$admin = $auth->createRole('admin');
		$admin->description = 'Администратор';
		
		$director = $auth->createRole('director');
		$director->description = 'Директор';
		
		$manager = $auth->createRole('manager');
		$manager->description = 'Менеджер';
		
		$user = $auth->createRole('user');
		$user->description = 'Клиент';
		
		$auth->add($admin);
		$auth->add($director);
		$auth->add($manager);
		$auth->add($user);
		/* /пользователи */
		
		
		/* определяем права пользователей */
		// права админа
		$viewAdminPage = $auth->createPermission('viewAdminPage');
		$viewAdminPage->description = 'Просмотр админки';
		$auth->add($viewAdminPage);
		
		// права директора
		$editManagment = $auth->createPermission('editManagment');
		$editManagment->description = 'Управление менеджерами';
		$auth->add($editManagment);
		
		// права менеджера
		$editOrder = $auth->createPermission('editOrder');
		$editOrder->description = "Управление заказами";
		$auth->add($editOrder);
		
		$editUser = $auth->createPermission('editUser');
		$editUser->description = "Управление клиентами";
		$auth->add($editUser);
		
		// права пользователя
		$createOrder = $auth->createPermission('createOrder');
		$createOrder->description = "Создание заказа";
		$auth->add($createOrder);
		
		/* /определяем права пользователей */
		
		
		/* раздадим права */
		
		// клиенту
		$auth->addChild($user, $createOrder);
		
		// менеджеру
		$auth->addChild($manager, $user);
		$auth->addChild($manager, $editOrder);
		$auth->addChild($manager, $editUser);
		
		// директору
		$auth->addChild($director, $manager);
		$auth->addChild($director, $editManagment);
		
		$auth->addChild($admin, $director);
		$auth->addChild($admin, $viewAdminPage);
		
		/* /раздадим права */
		
		
		$auth->assign($admin, 1);
		$auth->assign($director, 2);
		$auth->assign($manager, 3);
		$auth->assign($user, 4);
		
		$user = $auth->getRole('user');
		$auth->assign($user, 555);
		
		//echo date('d-m-Y', 1555066555);	
		
	}
}