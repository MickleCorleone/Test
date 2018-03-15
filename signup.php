<?php

require "db.php";

echo "<link rel='stylesheet' href='libs/style.css'>";
?>

<?php

$data = $_POST;
if (isset($data['do_signup'])) { //Запускаем при нажатии на кнопку зарегистрироваться

    $errors = null; //Создаем массив для записи ошибок
    if (trim($data['login'] == '')) { //Проверяем заполнена ли строка логина
        $errors = 'login'; //Выводим ошибку, что не заполнена
    }
    if (trim($data['email'] == '')) { //Проверяем заполнена ли строка емаила
        $errors = 'email';  //Выводим ошибку, что не заполнена
    }
    if ($data['password'] == '') { //Проверяем заполнена ли строка пароля
        $errors = 'password';  //Выводим ошибку, что не заполнена
    }
    if ($data['password_2'] != $data['password']) { //Проверяем одинаковый ли пароль
        $errors = 'password2!';  //Выводим ошибку, что не совпадают
    }
    if (trim($data['name'] == '')) { //Проверяем заполнена ли строка с именем
        $errors = 'name'; //Выводим ошибку, что не заполнена
    }
    $xml = simplexml_load_file('libs/users.xml'); //Загружаем xml файл
    foreach ($xml->user as $login) { //Запускаем цикл поиска
        if ($login->login == $data['login']) { //Если полученное значение login находим в файле xml
            $errors = 'user'; //То заносим ошибку в переменную errors
            break; //Прекращаем дальнейший поиск по файлу xml
        }
    }
    $xml = simplexml_load_file('libs/users.xml'); //Загружаем xml файл
    foreach ($xml->user as $email) { //Запускаем цикл поиска
        if ($email->email == $data['email']) { //Если полученное значение email находим в файле xml
            $errors = 'anotherEmail'; //То заносим ошибку в переменную errors
            break; //Прекращаем дальнейший поиск по файлу xml
        }
    }
    if (empty($errors)) { //Если ошибок не возникло идем дальше
        $dom = new DomDocument(); //Создаем новый документ
        $dom->load('libs/users.xml'); //Загружаем xml-файл
        $xpath = new DOMXPath ($dom);
        $parent = $xpath->query('//users');
        $next = $xpath->query('//users/user');
        $new_user = $dom->createElement('user'); //Создаем новый узел элемента
        $new_login = $dom->createElement('login', $data['login']); //Создаем новый узел элемента
        $new_name = $dom->createElement('name', $data['name']); //Создаем новый узел элемента
        $new_email = $dom->createElement('email', $data['email']); //Создаем новый узел элемента
        $new_password = $dom->createElement('password', md5(md5($data['password'] . SALT))); //Создаем новый узел элемента
        $new_user->appendChild($new_login); //Добавляем элемент user в конец
        $new_user->appendChild($new_name);  //Добавляем элемент name в конец
        $new_user->appendChild($new_email); //Добавляем элемент email в конец
        $new_user->appendChild($new_password); //Добавляем элемент password в конец
        $parent->item(0)->insertBefore($new_user, $next->item(0)); //Заносим данные нового пользователя в начало списка
        $dom->save("libs/users.xml"); //Сохраняем обновленный файл

        echo '<div id="luck">Вы успешно зарегистрировались!<br>Вернутья на <a href="/">главную</a> страницу.</div><hr>'; //Выводим сообщение об успешной регистрации
    } else {
        $file = file_get_contents('libs/errors.json'); //Подключаем файл со списком ошибок
        $json = json_decode($file); //Декодируем файл со списком ошибок
        echo $json->$errors; //Выводим текст ошибки помещенной в переменную errors
    }
}

include('forms/signup_form.html'); //Подключаем форму для регистрации

?>
