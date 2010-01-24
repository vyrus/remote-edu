<?php
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
	$assigmentAction = array(
    );
	
	/* ссылки, доступные только админу*/	        
    $admin_assigmentAction = array(
        'Назначить ответсвенного за дисциплину/курсы' => 'responsible_teacher',
        'Назанчить кураторов для студентов' => 'students_curator',
    );

	/* ссылки, доступные только преподу*/	        
    $teacher_assigmentAction = array(
    );

	/* ссылки, доступные только слушателю*/	        
    $student_assigmentAction = array(
    );
	
    //Wtfi
    $cur_ctrl = $_SERVER['REQUEST_URI'];
	$prefix = '/assignment/';

	$user = Model_User::create();
	$udata = (object) $user->getAuth();
	
	/* вывод общих пунктов меню*/
		  foreach ($assigmentAction as $title => $controller): ?>
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
			$items = 'teacher_assigmentAction';
		}elseif (Model_User::ROLE_ADMIN == $udata->role)
		{
			$items = 'admin_assigmentAction';	
		}elseif (Model_User::ROLE_STUDENT == $udata->role)
		{
			$items = 'student_assigmentAction';	
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