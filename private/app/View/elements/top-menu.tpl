<?php

	/* ссылки, доступные пользователю, независимо от прав доступа */	    
    $generic_elements = array(
        'Главная'  => 'http://dist.uchimvas.ru/',
        //'Ошибки' => 'error'
    );
    
    /* ссылки, доступные только админу */	        
    $admin_elements = array(
        'Регистрация пользователя'      => 'users/index_by_admin/',
        'Формирование учебных программ' => 'education_programs/index/',
        'Загрузка материалов'           => 'educational_materials/index_by_admin/',		
        'Заявки на обучение'            => 'applications/index_by_admin',
    );
    
    /* ссылки, доступные только преподу */	        
    $teacher_elements = array(
        'Пользователи' => 'users/index/',
        'Материалы'    => 'educational_materials/index/',
    );
    
    /* ссылки, доступные только слушателю */	        
    $student_elements = array(
        'Моё меню'       => 'users/instructions_by_user/',
        'Мои курсы'      => 'education_programs/available/',        
        //'Материалы'      => 'educational_materials/index_by_student/',
        'Мой новый курс' => 'applications/index_by_student/',
    );

    /* Дополнительные ссылки */
    $external_links = array(
        'Прайс на дистанционное обучение' => 'price/',
        'Как оплатить'                    => 'payment/',
        'Форум'                           => 'http://uchimvas.ru/forum.html'
    );
    
    /* Карта соответствия ролей пользователей и выводимых пунктов меню */
    $_role2elems = array(
        Model_User::ROLE_STUDENT => $student_elements,
        Model_User::ROLE_TEACHER => $teacher_elements,
        Model_User::ROLE_ADMIN   => $admin_elements
    );
    
    $cur_ctrl = strtolower($this->_request->_router['handler']['controller']);
    
    $user = Model_User::create();
    $udata = $user->getAuth();
    $userId = (false === $udata ? false : $udata['user_id']);
    $role = (false === $udata ? false : $udata['role']);

    /* Берём общие для всех пользователей элементы меню */
    $elems = $generic_elements;
    
    /* Если пользователь авторизован, добавляем пункты меню для его роли */
    if (false !== $role) {
        $unreadMessagesCount = Model_Messages::getUnreadCount($userId); 
        $messagesCaption = $unreadMessagesCount ? '<b>Сообщения (' . $unreadMessagesCount . ')</b>' : 'Сообщения';
        $elems[$messagesCaption] = 'messages';
        $elems = array_merge($elems, $_role2elems[$role]);
    }
                                       
    /* Если пользователь - не администратор, то добавляем внешние ссылки */
    if (Model_User::ROLE_ADMIN !== $role) {
        $elems = array_merge($elems, $external_links);
    }
    
    end($elems);
    $last_key = key($elems);
    
?>
    
<?php foreach ($elems as $title => $link): ?>
    <?php if (strpos($link, 'http') === 0): ?>
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
    <?php elseif ($cur_ctrl == $link): ?>
        <a href="/<?php echo $link ?>"><?php echo $title ?></a>
    <?php else: ?>
        <a href="/<?php echo $link ?>"><?php echo $title ?></a>
    <?php endif; ?>
    
    <?php if ($last_key !== $title): ?>
        <img alt="" src="/files/images/line_navigation.gif" />
    <?php endif; ?>
<?php endforeach; ?>