<?php

require "db.php";

include('libs/function.php');

echo "<link rel='stylesheet' href='libs/style.css'>";

$data = $_POST;
if (login()) { //Вызываем функцию логин проверяющую авторизован пользователь или нет
} else if (isset($data ['do_login'])) { //Начинаем обработку при нажатии кнопки "Войти!"
    $errors = null; //Создаем массив для ошибок
    $xml = simplexml_load_file('libs/users.xml'); //Загружаем xml файл
    foreach ($xml->user as $login) { //Запускаем цикл поиска
        if ($login->login == $data['login']) { //Если полученное значение login находим в файле xml
            $user = $login; //То заносим полученный логин в переменную user
            break; //Прекращаем дальнейший поиск по файлу xml
        }
    }
    if ($user) {        //Если логин существует
        if (md5(md5($data['password'] . SALT)) == $user->password) { //Применяем проверку пароля
            $_SESSION ['logged_user'] = $data['login']; //Открываем сессию
            setcookie("login", $data ['login'], time() + 50000); //Пишем логин в куки
            setcookie("password", md5(md5($data['password'] . SALT)), time() + 50000); //Пишем шифрованный пароль в куки
            echo '<div id="luck">Вы авторизованы! Можете перейти на <a href="/">главную</a> страницу.</div><hr>'; //Выводим зеленым цветом, что авторизация прошла успешно и можно перейти на главную страницу
        } else {
            $errors = 'wrongPassword'; //Если логин верный, а пароль не верен
        }
    } else {
        $errors = 'anotherUser'; //Если логин не верен
    }
    if (!empty($errors)) { //Если переменная не null, выполняем следующее условие
        $file = file_get_contents('libs/errors.json'); //Подключаем файл со списком ошибок
        $json = json_decode($file); //Декодируем файл со списком ошибок
        echo $json->$errors; //Выводим текст ошибки помещенной в переменную errors
    }
}
include('forms/login_form.html'); //Подключаем форму для авторизации

?>