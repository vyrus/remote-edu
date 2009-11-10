<?php
	/* ссылки, доступные пользователю, независимо от прав доступа*/	    
    $elements = array(
        'Главная'      				=> 'index',
        'Ошибки'       				=> 'error'
    );
	
	/* ссылки, доступные только админу*/	        
    $admin_elements = array(
        'Пользователи' 				=> 'users',
		'Образовательные программы'	=> 'education_programs',		
		'Учебные Материалы'			=> 'educational_materials',
        'Заявки'                    => 'applications'
    );
	
	/* ссылки, доступные только преподу*/	        
    $teacher_elements = array(
        'Пользователи' 				=> 'users',
		'Учебные Материалы'			=> 'educational_materials'
    );

	/* ссылки, доступные только слушателю*/	        
    $student_elements = array(
        //'Слушатели' 				=> 'users',
        'Образовательные программы' => 'education_programs',        
		'Учебные Материалы'			=> 'educational_materials',
        'Заявки'                    => 'applications'
    );

    $cur_ctrl = $this->_request->_router['handler']['controller'];

$user = Model_User::create();
$udata = (object) $user->getAuth();

/* вывод общих пунктов меню*/
	  foreach ($elements as $title => $controller): ?>
    <?php if ($controller == strtolower ($cur_ctrl)): ?>
        <a href="/<?=$controller ?>/index/"><?=$title ?></a>
        <img alt="" src="/files/images/line_navigation.gif"/>
    <?php else: ?>
        <a href="/<?=$controller ?>/index/"><?=$title ?></a>
        <img alt="" src="/files/images/line_navigation.gif"/>
    <?php endif; ?>
<?php endforeach;

if (isset($udata->role))
{
	if (Model_User::ROLE_TEACHER == $udata->role)
	{
		$default_action = "index_by_teacher";
		$items = 'teacher_elements';
	}elseif (Model_User::ROLE_ADMIN == $udata->role)
	{
		$default_action = "index_by_admin";
		$items = 'admin_elements';	
	}elseif (Model_User::ROLE_STUDENT == $udata->role)
	{
		$default_action = "index_by_student";
		$items = 'student_elements';	
	}
	/* вывод пунктов меню, специфических для залогиненного пользователя */
		  foreach (${$items} as $title => $controller): ?>
		<?php if ($controller == strtolower ($cur_ctrl)): ?>
			<a href="/<?=$controller ?>/<?=$default_action?>/"><?=$title ?></a>
                        <img alt="" src="/files/images/line_navigation.gif"/>
		<?php else: ?>
			<a href="/<?=$controller ?>/<?=$default_action?>/"><?=$title ?></a>
                        <img alt="" src="/files/images/line_navigation.gif"/>
		<?php endif; ?>
	<?php endforeach; 	
}												  
?>