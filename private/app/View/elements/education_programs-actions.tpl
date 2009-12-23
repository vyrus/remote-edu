<?php
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
	$educationProgramsAction = array(
    );
	
	/* ссылки, доступные только админу*/	        
    $admin_educationProgramsAction = array(
    );

	/* ссылки, доступные только преподу*/	        
    $teacher_educationProgramsAction = array(
    );

	/* ссылки, доступные только слушателю*/	        
    $student_educationProgramsAction = array(
        'Доступные программы'           => 'available/',
        'Инструкция пользователю'	=> 'instructions_by_student/'
    );
	
    //Wtfi
    $cur_ctrl = $_SERVER['REQUEST_URI'];
	$prefix = '/education_programs/';

	$user = Model_User::create();
	$udata = (object) $user->getAuth();
	
	/* вывод общих пунктов меню*/
		  foreach ($educationProgramsAction as $title => $controller): ?>
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