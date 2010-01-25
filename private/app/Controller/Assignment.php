<?php
	class Controller_Assignment extends Mvc_Controller_Abstract {
		public function action_student_curator() {
			$users = Model_User::create();
			$request = $this->getRequest ();
			$method = 'post';

			if (empty($request->$method)) {
				$this->set('teachers', $users->getTeachersList());
				$this->set('students', $users->getStudentsList());				
				$this->render('assignment/student_curator');
			}
			else {
				$requestData = $request->$method;
				
				if (count($requestData)) {
					foreach ($requestData as $i => $value) {
						$users->setUserCurator($i, $value);
					}
				}
				$this->flash('Кураторы успешно назначены', '/assignment/students_curator', 3);
			}			
		}
		
		public function action_responsible_teacher() {
			$programs = Model_Education_Programs::create();
			$request = $this->getRequest ();
			$method = 'post';
			if (empty($request->$method)) {
				$users = Model_User::create();				
				$this->set('teachers', $users->getTeachersList());
				$this->set('disciplines', $programs->getDisciplinesResponsibleTeachersList());
				$this->set('courses', $programs->getCoursesResponsibleTeachersList());
				$this->render('assignment/responsible_teacher');
			}
			else {
				$requestData = $request->$method;
				
				if (isset($requestData['courses'])) {
					foreach ($requestData['courses'] as $courseId => $teacherId) {
						$programs->setCoursesResponsibleTeacher($courseId, $teacherId);
					}
				}
				
				if (isset($requestData['disciplines'])) {
					foreach ($requestData['disciplines'] as $disciplineId => $teacherId) {
						$programs->setDisciplineResponsibleTeacher($disciplineId, $teacherId);
					}
				}
				
				$this->flash('Ответсвенные преподаватели успешно назначены', '/assignment/responsible_teacher', 3);
			}
		}
				
		public function action_index() {
			$this->action_responsible_teacher();
		}
	}
?>