<?php
    $user = Model_User::create();
    $udata = $user->getAuth();
    $role = (false === $udata ? false : $udata['role']);

    /* Если не пользователь авторизован, выводим форму логина */
    if (false !== $role) {
?>
        <a href="/users/logout/" title="Выход">
        <font color=#fff>
            ВЫХОД
        </font>
    </a>
<?php
    } else {
        $action = '/users/login/';
        $form = Form_Profile_Login::create($action);
?>
    <form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
        <input type="text" class="txtfld" id="login" value="логин" onclick="seltext()" name="login">&nbsp;<input type="password" id="passwd" value="пароль" onclick="seltext()" name="passwd" class="txtfld">
        <input name="" src="/files/images/icon_ok.gif" class="button" type="image">
        <!--<input type="submit" value="ок">-->
        <a href="/users/register_student" title="Регистрация"><font color=#fff>РЕГИСТРАЦИЯ</font></a>
        <!--<tr><td><a href="/remember_password.html" title="Забыли пароль?">Забыли пароль?</a></td></tr>-->
    </form>
<?php
    }
?>