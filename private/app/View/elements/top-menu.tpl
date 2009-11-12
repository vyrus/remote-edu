<?php

	/* ссылки, доступные пользователю, независимо от прав доступа */	    
    $generic_elements = array(
        'Главная'  => 'http://uchimvas.ru/',
        //'Ошибки' => 'error'
    );
	
	/* ссылки, доступные только админу */	        
    $admin_elements = array(
        'Пользователи' => 'users',
		'Программы'	   => 'education_programs',		
		'Материалы'	   => 'educational_materials',
        'Заявки'       => 'applications'
    );
	
	/* ссылки, доступные только преподу */	        
    $teacher_elements = array(
        'Пользователи' => 'users',
		'Материалы'	   => 'educational_materials'
    );

	/* ссылки, доступные только слушателю */	        
    $student_elements = array(
        //'Слушатели' => 'users',
        'Программы'   => 'education_programs',        
		'Материалы'	  => 'educational_materials',
        'Заявки'      => 'applications'  
    );

    /* Дополнительные ссылки */
    $external_links = array(
        'Преподаватели и сотрудники' => 'http://uchimvas.ru/article899',
        'Прайс'                      => 'http://uchimvas.ru/article990',
        'Оплата'                     => 'http://uchimvas.ru/article991',
        'Форум'                      => 'http://uchimvas.ru/forum.html'
    );
    
    /* Карта соответствия ролйе пользователей и выводимых пунктов меню */
    $_role2elems = array(
        Model_User::ROLE_STUDENT => $student_elements,
        Model_User::ROLE_TEACHER => $teacher_elements,
        Model_User::ROLE_ADMIN   => $admin_elements
    );
    
    $cur_ctrl = strtolower($this->_request->_router['handler']['controller']);
    
    $user = Model_User::create();
    $udata = $user->getAuth();
    $role = (false === $udata ? false : $udata['role']);

    /* Берём общие для всех пользователей элементы меню */
    $elems = $generic_elements;
    
    /* Если пользователь авторизован, добавляем пункты меню для его роли */
    if (false !== $role) {
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
        <a href="/<?php echo $link ?>/index/"><?php echo $title ?></a>
    <?php else: ?>
        <a href="/<?php echo $link ?>/index/"><?php echo $title ?></a>
    <?php endif; ?>
    
    <?php if ($last_key !== $title): ?>
        <img alt="" src="/files/images/line_navigation.gif" />
    <?php endif; ?>
<?php endforeach; ?>