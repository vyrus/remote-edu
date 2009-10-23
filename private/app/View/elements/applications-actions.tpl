<?php
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
	$application_action_elements = array(
    );
	
	/* ссылки, доступные только админу*/	        
    $admin_application_action_elements = array(
        'Список поданных заявок'	=> 'index_by_admin'
    );
	
	/* ссылки, доступные только преподу*/	        
    $teacher_application_action_elements = array(
    );

	/* ссылки, доступные только слушателю*/	        
    $student_application_action_elements = array(
        'Подать заявку' 	=> 'index_by_student',
        'Мои заявки'       => 'list_by_student'
    );
	
    //Wtfi
    $cur_ctrl = $_SERVER['REQUEST_URI'];
	$prefix = '/applications/';

	$user = Model_User::create();
	$udata = (object) $user->getAuth();
	
	/* вывод общих пунктов меню*/
		  foreach ($application_action_elements as $title => $controller): ?>
		<?php if ($prefix. $controller == strtolower ($cur_ctrl)): ?>
			<li class="headli active"><?php echo $title; ?></li>
		<?php else: ?>
			<li class="headli">
				<a href="<?=$prefix.$controller ?>"><?=$title ?></a>
			</li>
		<?php endif; ?>
	<?php endforeach;
	
	if (isset($udata->role))
	{
		if (Model_User::ROLE_TEACHER == $udata->role)
		{
			$items = 'teacher_application_action_elements';
		}elseif (Model_User::ROLE_ADMIN == $udata->role)
		{
			$items = 'admin_application_action_elements';	
		}elseif (Model_User::ROLE_STUDENT == $udata->role)
		{
			$items = 'student_application_action_elements';	
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
}												  
?>