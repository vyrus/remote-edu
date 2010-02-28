<?php
	/* ссылки, доступные только админу*/	        
    $admin_user_action_elements = array(
    );
	
	/* ссылки, доступные только преподу*/	        
    $teacher_user_action_elements = array(
        'Мои слушатели' => '#',
        'Работа с дневником слушателя' => '#',
        'Инструкция' => '#',
    );

	/* ссылки, доступные только слушателю*/	        
    $student_user_action_elements = array(
    );
	
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
    $user_action_elements = array(
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