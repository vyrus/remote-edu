<?php

    /* ссылки, доступные пользователю, независимо от прав доступа*/
    $generic_elements = array(
    );

    /* ссылки, доступные только админу */
    $admin_elements = array(
        'Добавление направлений/дисциплин'        => 'education_programs/index/',
        //'Формирование порядка изучения дисциплин' => '#',
        'Назначение отвественных преподавателей'  => 'assignment/responsible_teacher/',
        'Назначение кураторов'                    => 'assignment/students_curator/',
        //'Инструкция'                              => '#',
    );

    /* ссылки, доступные только преподу */
    $teacher_elements = array();

    /* ссылки, доступные только слушателю */
    $student_elements = array(
        'Доступные программы'     => 'available/',
        'Инструкция пользователю' => 'instructions_by_student/'
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
    
    $userId = (false === $udata ? false : $udata['user_id']);
    $role   = (false === $udata ? false : $udata['role']);

    /* Берём общие для всех пользователей элементы меню */
    $elems = $generic_elements;
    
    /* Если пользователь авторизован, добавляем пункты меню для его роли */
    if (false !== $role) {
        $elems = array_merge($elems, $_role2elems[$role]);
    }
    
?>
    
<?php foreach ($elems as $title => $link): ?>
    <?php if (strpos($link, 'http') === 0): ?>
        <li class="headli">
            <a href="<?php echo $link ?>"><?php echo $title ?></a>
        </li>
    <?php else: ?>
        <li class="headli">
            <a href="/<?php echo $link ?>"><?php echo $title ?></a>
        </li>
    <?php endif; ?>
<?php endforeach; ?>