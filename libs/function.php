<?php
function login()
{
    if (isset($_SESSION['logged_user'])) //Работаем если сесcия есть
    {
        $data = $_POST;
        if (isset($_COOKIE['login']) && isset($_COOKIE['password'])) { //Если cookie есть, то просто обновим время их жизни и вернём true
            SetCookie("login", "", time() - 1, '/');
            SetCookie("password", "", time() - 1, '/');
            setcookie("login", $_COOKIE['login'], time() + 50000, '/');
            setcookie("password", $_COOKIE['password'], time() + 50000, '/');
            return true;
        } else //Иначе добавим cookie с логином и паролем, чтобы после перезапуска браузера сессия не слетала
        {
            setcookie("login", $data ['login'], time() + 50000, '/');
            setcookie("password", md5(md5($data['password'] . SALT)), time() + 50000, '/');
            return true;
        }
    } else { //Не работаем если сессии нет
        return false;
    }
}