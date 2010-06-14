<?php

    /* $Id$ */

    class Controller_Applications extends Mvc_Controller_Abstract {
        /**
        * форма для подачи заявки слушателем.
        */
        public function action_index_by_student()
        {
            $educationPrograms = Model_Education_Programs::create ();
            $this->set ('directions',	$educationPrograms->getDirections 				());
            $this->set ('courses', 		$educationPrograms->getCourses 					());
            $this->set ('disciplines',	$educationPrograms->getDirectionsDisciplines 	());

            $this->render();
        }

        /**
        * Список поданных заявок для админа.
        */
        public function action_index_by_admin()
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            /**
            * @todo Paginator.
            */
            $app = Model_Application::create();
            /**
            * @todo Выводить только заявки, которые ещё не полностью оплачены
            * (или вообще ешё не приняты/подписаны).
            */
            $apps = $app->getAllAppsInfo();

            $this->set('applications', $apps);
            $this->set('statuses', Model_Application::getStatusMap());
            $this->set ('invalidMaterialsForms', array ());

            $contract = Model_Contract::create ();

            $request	= $this->getRequest ();
            $requestData	= $request->files;
            if (empty ($requestData)) {
                $this->render ('applications/index_by_admin');
            }
        // [ загрузка договора
            $invalidMaterialsForms = array ();

            foreach ($request->files as $key=>$item)
            {
                //list($empty_str,$app_id) = (split('fileReference',$key));
                list($empty_str,$app_id) = (explode('fileReference',$key));
            }

            $request->set (
                'get',
                array (
                    'filename'		=> $request->files['fileReference' . $app_id]['name'],
                )
            );

            $contractForm = Form_Contract_Upload::create ('');
            $contractForm->setMethod (Form_Abstract::METHOD_GET);
            if (! $contractForm->validate ($request)) {
                $invalidMaterialsForms[] = $contractForm;
            }
            else
            {
                $postman = Resources::getInstance()->postman;

                $postman->sendContractStudent(
                    $udata->email,
                    $request->files['fileReference' . $app_id]['tmp_name']
                );

                $contract->addContract ($request->files['fileReference' . $app_id],$app_id);
            }

            if (! empty ($invalidMaterialsForms)) {
                $this->set		('invalidMaterialsForms', $invalidMaterialsForms);
                $this->render	('educational_materials/upload');
            }

            $links = Resources::getInstance()->links;

            $this->flash (
                'Договор успешно загружен',
                $links->get('admin.applications'),
                3
            );
        }

        /**
        * Подача заявки на программу/дисциплину.
        */
        public function action_apply($params)
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $links = Resources::getInstance()->links;

            /* Получаем параметры программы */
            $object_id = $params['program_id'];
            $type = $params['program_type'];

            /* Если подаётся заявка на дисциплину, */
            if (Model_Application::TYPE_DISCIPLINE === $type) {
                /* то получаем идентификатор программы по дисциплине */
                $disc = Model_Discipline::create();
                $info = $disc->get($object_id);
                $program_id = $info['program_id'];
            } else {
                /* Если же заявка на программу, то берём её идентификатор */
                $program_id = $object_id;
            }

            /* Получаем данные программы */
            $program = Model_Education_Programs::create();
            $info = (object) $program->getProgramInfo($program_id);

            /* Если программы платная и не заполнен расширенный профиль, то */
            if (
                Model_Education_Programs::PAID_TYPE_PAID === $info->paid_type &&
                !$user->isExtendedProfileSet($udata->user_id)
            ) {
                // просим пользователя его заполнить
                $msg = 'Заполните, пожалуйста, подробную анкету слушателя';
                $this->flash($msg, $links->get('student.extended-profile'));
            }

            $app = Model_Application::create();
            $app->apply($udata->user_id, $object_id, $type);

            $return_url = $links->get('student.applications');

            $msg = 'Вы успешно подали заявку на учебный курс.<p>
              Через 10 сек. Вас автоматически перенаправят на страницу просмотра
              поданых Вами <a href="' . $return_url . '" title=Мои заявки> заявок</a>.
              <p>
              Также, Вы можете, не дожидаясь перенаправления, перейти на страницу
              <a href="' . $links->get('student.apply') . '" title=Авторизация>Мой новый курс</a> и
              подать зявку на ещё один учебный курс!';

            $this->flash($msg, $return_url, 10);
        }

        /**
        * Просмотр статуса заявок слушателем.
        */
        public function action_list_by_student()
        {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $app = Model_Application::create();
			$paym = Model_Payment::create();

            $apps = $app->getAppsInfo($udata->user_id);
		    foreach ($apps as $i=>$a)
		    {
		    	if ($a['status'] == 'signed')
				{
				    if ($a['program_title'])
					{
						//товарищ учится по всему направлению
						$prog = $app->getProgram($a['object_id']);
						if ($prog['paid_type'] == 'paid')
						{
							$paid_money = $paym->getTotal($a['app_id']);
							$rest = $prog['cost'] - $paid_money; // (program price - paid already)
							$rest_rate = $rest/$prog['cost']; // how many cost's parts to pay
							$apps[$i] = array_merge($apps[$i],array('rest' => $rest, 'rest_rate' => $rest_rate));
						}else
						{
							$apps[$i] = array_merge($apps[$i],array('rest' => 'free', 'rest_rate' => 'free'));
						}
					}else
					{
						//учится по дисциплине
						$disc = $app->getDiscipline($a['object_id']);
						$upper_prog = $app->getProgram($disc['program_id']);
						if ($upper_prog['paid_type'] == 'paid')
						{
							$paid_money = $paym->getTotal($a['app_id']);
							$rest = ($upper_prog['cost']*$disc['coef'])/100 - $paid_money; // (program price - paid already)
							$rest_rate = $rest/(($upper_prog['cost']*$disc['coef'])/100); // how many cost's parts to pay
							$apps[$i] = array_merge($apps[$i],array('rest' => $rest, 'rest_rate' => $rest_rate));
						}else
						{
							$apps[$i] = array_merge($apps[$i],array('rest' => 'free', 'rest_rate' => 'free'));
						}
					}
				}
		    }
            $this->set('applications', $apps);
            $this->set('statuses', Model_Application::getStatusMap());
            $this->render();
        }


        public function action_download_contract($params)
        {
			$file_name = $params['file_name'];

			// folder with files
			define('BASE_DIR',ROOT.'/contracts/');

			// log file name
			define('LOG_FILE',ROOT.'/contracts/downloads.log');

			if (!isset($file_name) || empty($file_name)) {
			  die("Please specify file name for download.");
			}

			// Get real file name.
			$fname = basename($file_name);

			// Check if the file exists
			function find_file($dirname, $fname, &$file_path)
			{
				$dir = opendir($dirname);
				while ($file = readdir($dir))
				{
			    if (empty($file_path) && $file != '.' && $file != '..')
			    {
				      if (is_dir($dirname.'/'.$file))
				      {
				          find_file($dirname.'/'.$file, $fname, $file_path);
				      }
				      else
				      {
				        if (file_exists($dirname.'/'.$fname))
				        {
				            $file_path = $dirname.'/'.$fname;
				            return;
				        }
				      }
			    }
			  }
			} // find_file

			// get full file path (including subfolders)
			$file_path = '';
			find_file(BASE_DIR, $fname, $file_path);

			if (!is_file($file_path)) {
			  die("File does not exist. Make sure you specified correct file name.");
			}

			// file size in bytes
			$fsize = filesize($file_path);
			$fext = 'doc';
			$mtype = 'application/msword';
			$asfname = 'contract.doc';

			// set headers
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Type: $mtype");
			header("Content-Disposition: attachment; filename=\"$asfname\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . $fsize);

			// download
			// @readfile($file_path);
			$file = @fopen($file_path,"rb");
			if ($file) {
			  while(!feof($file)) {
			    print(fread($file, 1024*8));
			    flush();
			    if (connection_status()!=0) {
			      @fclose($file);
			      die();
			    }
			  }
			  @fclose($file);
			}

			$f = @fopen(LOG_FILE, 'a+');
			if ($f) {
			  @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$fname."\n");
			  @fclose($f);
			}
        }

        public function action_change_app_status($params)
        {
            $links = Resources::getInstance()->links;

            $user = Model_User::create();
            $udata = $user->getAuth();

            $new_status = $params['new_status'];
            $app_id = $params['app_id'];

            $app = Model_Application::create();
            $app->setAppStatus($new_status, $app_id);

            if ('signed' == $new_status) {
                $programs = Model_Education_Programs::create();
                $app_info = $app->getAppInfo($app_id);
                $first_section = $programs->getFirstSectionOfDiscipline($app_info[0]['object_id']);
                $programs->setCheckpoint(array('student_id' => $app_info[0]['user_id'], 'section_id' => $first_section[0]['section_id']));
            }

            $map = Model_Application::getStatusMap();
            $this->flash('Заявка ' . $map[$new_status],
                         $links->get('admin.applications'));

            $this->render();
        }

        /**
        * Удаление заявки из базы данных.
        *
        * @todo Удалять ли платежи из базы при удалении заявки?
        */
        public function action_delete(array $params = array()) {
            $links = Resources::getInstance()->links;
            $return_url = $links->get('admin.applications');

            if (empty($params)) {
                $this->flash('Не указан номер заявки', $return_url);
            }

            $app_id = intval(array_shift($params));
            $app = Model_Application::create();

            if (!$app->delete($app_id)) {
                $msg = 'Не удалось удалить заявку с номером ' . $app_id;
            } else {
                $msg = 'Заявка успешно удалена';
            }

            $this->flash($msg, $return_url);
        }
    }

?>