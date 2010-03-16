<?php

    /* ссылки, доступные пользователю, независимо от прав доступа*/
    $generic_elements = array(
        'Выйти из системы' => $this->_links->get('users.logout')
    );

    /* ссылки, доступные только админу */
    $admin_elements = array(
        'Инструкция'             => $this->_links->get('admin.help'),
        'Регистрация сотрудника' => $this->_links->get('admin.register-employee'),
        'Регистрация слушателя'  => $this->_links->get('student.register'),
        'Список пользователей'   => '/users/users_list',
    );

    /* ссылки, доступные только преподу */
    $teacher_elements = array();

    /* ссылки, доступные только слушателю */
    $student_elements = array(
        'Подробная анкета слушателя'  => $this->_links->get('student.extended-profile'),
        'Инструкция пользователю'     => $this->_links->get('student.index')
    );

    /* Карта соответствия ролей пользователей и выводимых пунктов меню */
    $_role2elems = array(
        Model_User::ROLE_STUDENT => $student_elements,
        Model_User::ROLE_TEACHER => $teacher_elements,
        Model_User::ROLE_ADMIN   => $admin_elements
    );

    /* Получаем данные пользователя, если он авторизован */
    $user = Model_User::create();
    $udata = $user->getAuth();
    
    $role = (false === $udata ? false : $udata['role']);

    /* Список пунктов меню */
    $elems = array();
    
    /* Если пользователь авторизован, добавляем пункты меню для его роли */
    if (false !== $role) {
        $elems = array_merge($elems, $_role2elems[$role]);
    }
    
    /* Добавляем общие для всех пользователей элементы меню */
    $elems = array_merge($elems, $generic_elements);
    
?>
    
<?php foreach ($elems as $title => $link): ?>
    <li class="headli">
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
    </li>
<?php endforeach; ?>