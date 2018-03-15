<?php
require "db.php";
unset($_SESSION['logged_user']); //Закрываем сессию авторизованного пользователя
header('Location: /'); //Переключаем пользователя на главную страницу
?>