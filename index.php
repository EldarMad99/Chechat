<?php
/**
 * @version 1.3 (Output Buffering Fix)
 * @name index.php
 * @path htdocs/index.php
 * @description Добавлена буферизация вывода для решения всех проблем с "headers already sent".
 */

// 1. Включаем буферизацию вывода
ob_start();

// 2. Запускаем сессию
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Включаем отображение ошибок (только для разработки!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 4. Подключаем конфигурацию БД и автозагрузчик приложений
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/app_loader.php';

// 5. Определяем запрашиваемый модуль и очищаем его имя
$module = $_GET['module'] ?? 'chat';
$module = preg_replace('/[^a-zA-Z0-9_-]/', '', $module);
$module_view_path = __DIR__ . "/modules/{$module}/view.php";

// 6. Проверяем, что view-файл существует
if (!file_exists($module_view_path)) {
    http_response_code(404);
    ob_end_clean(); // очищаем буфер
    die("Ошибка 404: Модуль «{$module}» не найден.");
}

// 7. Подключаем основной шаблон (layout.php), внутри которого будет подключён наш view
require_once __DIR__ . '/templates/layout.php';

// 8. Отправляем накопленный в буфере HTML в браузер
ob_end_flush();