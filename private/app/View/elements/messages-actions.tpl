<?php

    /* ссылки, доступные пользователю, независимо от прав доступа*/
    $generic_elements = array(
        'Написать сообщение' => $this->_links->get('messages.send'),
        'Входящие'           => $this->_links->get('messages.inbox')
    );

    /* ссылки, доступные только админу */
    $admin_elements = array();

    /* ссылки, доступные только преподу */
    $teacher_elements = array();

    /* ссылки, доступные только слушателю */
    $student_elements = array();

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

    /* Берём общие для всех пользователей элементы меню */
    $elems = $generic_elements;
    
    /* Если пользователь авторизован, добавляем пункты меню для его роли */
    if (false !== $role) {
        $elems = array_merge($elems, $_role2elems[$role]);
    }
    
?>
    
<?php foreach ($elems as $title => $link): ?>
    <li class="headli">
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
    </li>
<?php endforeach; ?>