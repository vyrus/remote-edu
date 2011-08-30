<?php
	/* ссылки, доступные только админу*/	        
    $admin_user_action_elements = array(
    );
	
	/* ссылки, доступные только преподу*/	        
    $teacher_user_action_elements = array(
        'Наполнение системы' => '/educational_materials/upload',
	'Работа со слушателями' => '/teacher_courses/index',
	'Формирование контрольных работ' => '/control_works/index_by_teacher',
        //'Общение с администратором' => '#',
        'Инструкция' => $this->_links->get('help.teacher_courses')
    );

	/* ссылки, доступные только слушателю*/	        
    $student_user_action_elements = array(
    );
	
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
    $user_action_elements = array(
      'Выйти из системы' => '/logout'
    );

	$user = Model_User::create();
	$udata = (object) $user->getAuth();
	
	if (isset($udata->role))
	{
		if (Model_User::ROLE_TEACHER == $udata->role)
		{
			$items = 'teacher_user_action_elements';
		}elseif (Model_User::ROLE_ADMIN == $udata->role)
		{
			$items = 'admin_user_action_elements';	
		}elseif (Model_User::ROLE_STUDENT == $udata->role)
		{
			$items = 'student_user_action_elements';	
		}
		
	/* вывод пунктов меню, специфических для залогиненного пользователя */
		  foreach (${$items} as $title => $controller): ?>
			<li class="headli">
				<a href="<?=$controller ?>"><?=$title ?></a>
			</li>
	<?php endforeach; 	

	/* вывод общих пунктов меню*/
		  foreach ($user_action_elements as $title => $controller): ?>
			<li class="headli">
				<a href="<?=$controller ?>"><?=$title ?></a>
			</li>
	<?php endforeach;
}												  
?>
