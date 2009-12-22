<?php
	
	/* ссылки, доступные только админу*/	        
    $admin_user_action_elements = array(
      'Регистрация сотрудника'	=> 'register_employee_by_admin',
      'Регистрация слушателя' 	=> 'register_student'
    );
	
	/* ссылки, доступные только преподу*/	        
    $teacher_user_action_elements = array(
    );

	/* ссылки, доступные только слушателю*/	        
    $student_user_action_elements = array(
      //'Регистрация слушателя' 	=> 'register_student',
      'Подробная анкета слушателя'  => 'profile_extended_by_student'
    );
	
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
    $user_action_elements = array(
	//'Вход'                        => 'login',
    //'Кто я?'                      => 'whoami',
	  'Инструкция пользователю'     => 'instructions',
      'Выйти из системы'	        => 'logout'
    );

    //Wtfi
    $cur_ctrl = $_SERVER['REQUEST_URI'];
	$prefix = '/users/';

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
		<?php if ($controller == strtolower ($cur_ctrl)): ?>
			<li class="active"><?php echo $title; ?></li>
		<?php else: ?>
			<li class="headli">
				<a href="<?=$prefix.$controller ?>"><?=$title ?></a>
			</li>
		<?php endif; ?>
	<?php endforeach; 	

	/* вывод общих пунктов меню*/
		  foreach ($user_action_elements as $title => $controller): ?>
		<?php if ($prefix. $controller == strtolower ($cur_ctrl)): ?>
			<li class="headli active"><?php echo $title; ?></li>
		<?php else: ?>
			<li class="headli">
				<a href="<?=$prefix.$controller ?>"><?=$title ?></a>
			</li>
		<?php endif; ?>
	<?php endforeach;
}												  
?>