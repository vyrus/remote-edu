<?php
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
	$educationProgramsAction = array(
    );
	
	/* ссылки, доступные только админу*/	        
    $admin_educationProgramsAction = array(
		'Добавление направлений/дисциплин' => '/education_programs',
		'Формирование порядка изучения дисциплин' => '#',
		'Назначение отвественных преподавателей' => '/assignment/responsible_teacher',
		'Назначение кураторов' => '/assignment/students_curator',
		'Инструкция' => '#',
    );

	/* ссылки, доступные только преподу*/	        
    $teacher_educationProgramsAction = array(
    );

	/* ссылки, доступные только слушателю*/	        
    $student_educationProgramsAction = array(
    );
	
    //Wtfi
    //$cur_ctrl = $_SERVER['REQUEST_URI'];

	$user = Model_User::create();
	$udata = (object) $user->getAuth();
	
	/* вывод общих пунктов меню*/
		  foreach ($educationProgramsAction as $title => $controller): ?>
			<li class="headli">
				<a href="<?=$controller ?>"><?=$title ?></a>
			</li>
	<?php endforeach;
	
	if (isset($udata->role))
	{
		if (Model_User::ROLE_TEACHER == $udata->role)
		{
			$items = 'teacher_educationProgramsAction';
		}elseif (Model_User::ROLE_ADMIN == $udata->role)
		{
			$items = 'admin_educationProgramsAction';	
		}elseif (Model_User::ROLE_STUDENT == $udata->role)
		{
			$items = 'student_educationProgramsAction';	
		}
	/* вывод пунктов меню, специфических для залогиненного пользователя */
		  foreach (${$items} as $title => $controller): ?>
			<li class="headli">
				<a href="<?=$controller ?>"><?=$title ?></a>
			</li>
	<?php endforeach; 	
}												  
?>