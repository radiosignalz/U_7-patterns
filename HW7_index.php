<?php


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;


require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
//путь к текущей директории автозагрузчика.


$request = Request::createFromGlobals();
// Обращение к глобальным переменным

$containerBuilder = new ContainerBuilder();
//очевидно вызов экземпляра класса контейнера с проектом

Framework\Registry::addContainer($containerBuilder);
//Добавляем контейнер для работы реестра

$response = (new Kernel($containerBuilder))->handle($request);
// вызов функции загрузчика конфигуратора у экземпляра класса ядра программы
$response->send();
// последующее действие с вызванной функцией