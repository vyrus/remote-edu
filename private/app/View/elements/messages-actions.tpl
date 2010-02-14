<?php
	/* ссылки, доступные пользователю, независимо от прав доступа*/
	$messagesAction = array(
        'Написать сообщение' => '/messages/send',
	    'Входящие' => '/messages/inbox',
    );

	/* ссылки, доступные только админу*/
    $admin_messagesAction = array(
    );

	/* ссылки, доступные только преподу*/
    $teacher_messagesAction = array(
    );

	/* ссылки, доступные только слушателю*/
    $student_messagesAction = array(
    );

	$user = Model_User::create();
	$udata = (object) $user->getAuth();
	
	/* вывод общих пунктов меню*/
		  foreach ($messagesAction as $title => $controller): ?>
			<li class="headli">
				<a href="<?=$controller ?>"><?=$title ?></a>
			</li>
	<?php endforeach;

	if (isset($udata->role))
	{
		if (Model_User::ROLE_TEACHER == $udata->role)
		{
			$items = 'teacher_messagesAction';
		}elseif (Model_User::ROLE_ADMIN == $udata->role)
		{
			$items = 'admin_messagesAction';	
		}elseif (Model_User::ROLE_STUDENT == $udata->role)
		{
			$items = 'student_messagesAction';	
		}
	/* вывод пунктов меню, специфических для залогиненного пользователя */
		  foreach (${$items} as $title => $controller): ?>
			<li class="headli">
				<a href="<?=$controller ?>"><?=$title ?></a>
			</li>
	<?php endforeach;
}
?>