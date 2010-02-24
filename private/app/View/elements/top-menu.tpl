<?php

	/* ссылки, доступные пользователю, независимо от прав доступа */	    
    $generic_elements = array(
        'Главная'  => $this->_links->get('index'),
        //'Ошибки' => 'error'
    );
    
    /* ссылки, доступные только админу */	        
    $admin_elements = array(
        'Регистрация пользователя'      => $this->_links->get('employee.register'),
        'Формирование учебных программ' => $this->_links->get('programs.manage'),
        'Загрузка материалов'           => $this->_links->get('materials.manage'),
        'Заявки на обучение'            => $this->_links->get('applications.manage'),
    );
    
    /* ссылки, доступные только преподу */	        
    $teacher_elements = array(
        /**
        * @todo А что здесь за действие должно быть?
        */
        'Пользователи' => '#',
        /**
        * @todo Broken: Mvc_View_Exception, template not found
        */
        'Материалы'    => '/educational_materials/index/',
    );
    
    /* ссылки, доступные только слушателю */	        
    $student_elements = array(
        'Моё меню'       => $this->_links->get('student.index'),
        'Мои курсы'      => $this->_links->get('student.programs'),
        //'Материалы'      => 'educational_materials/index_by_student/',
        'Мой новый курс' => $this->_links->get('student.applications')
    );

    /* Дополнительные ссылки */
    $external_links = array(
        'Прайс на дистанционное обучение' => $this->_links->get('price'),
        'Как оплатить'                    => $this->_links->get('payment'),
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
        $elems[$messagesCaption] = $this->_links->get('messages.inbox');
        
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
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
    <?php else: ?>
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
    <?php endif; ?>
    
    <?php if ($last_key !== $title): ?>
        <img alt="" src="<?php echo $this->_links->getPath('/files/images/line_navigation.gif') ?>" />
    <?php endif; ?>
<?php endforeach; ?>