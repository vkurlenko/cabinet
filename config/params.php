<?php

return [
    'adminEmail' => 'admin@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'supportEmail' => 'robot@andreychef.com',

    // доменные имена
	'mainDomain' => 'http://andreychef',
    'subDomain' => 'http://cabinet.andreychef',
    'mainDocumentRoot' => 'D:/OpenServer/domains/andreychef',

    /*'mainDomain' => 'http://test.andreychef.com',
    'subDomain' => 'http://cab.andreychef.com',
    'mainDocumentRoot' => '/home/kolesovrem/andreychef.com/docs',*/

    // реквизиты мерчанта
    'merchantLogin' => 'andreychef-api',
    'merchantPwd' => 'andreychef',

    // продукты
    'testingSetCost' => 3000, // стоимость дегустационного сета

    // пользователи
    'phoneMask' => '+79999999999',

    // пути
    'prevSlash' => true, // предварять относительный путь к папке upload слешем (необходимо true на сервере)

    // шаблоны
    'insertBegin' => '{{',
    'insertEnd' => '}}',

    // длительность действия ссылки на оплату (секунд)
    'hashLifetime' => 60 * 60,

    // время жизни сессии для оплаты (секунд)
    'sessionTimeoutSecs' => 60, // * 60 * 24, // 24 часа

    // сообщения об ошибках
    'errors' => [
        'hash_expired' => 'Ссылка на оплату не действительна. Обратитесь к менеджеру',
    ],

	
];
