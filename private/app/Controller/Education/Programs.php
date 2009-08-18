<?php
	class Controller_Education_Programs extends Mvc_Controller_Abstract {
		public function action_index () {
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('specialities', $educationPrograms->getSpecialities ());
			$this->set ('disciplines', $educationPrograms->getSpecialitiesDisciplines ());
			$this->set ('sections', $educationPrograms->getDisciplinesSections ());
			
			$this->render ("education_programs/index");
		}
		
		public function action_add_speciality () {
			$this->set ('buttonCaption', 'Добавить');
						
			$request = $this->getRequest ();
			
			$form = Form_Speciality_Add::create('/add_speciality');
			$this->set ('form', $form);			
			$method = $form->method ();
			if (empty ($request->$method)) {
				$this->render ("education_programs/speciality_form");
			}
			
			$educationPrograms = Model_Education_Programs::create ();
			
			if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/speciality_form");
            }

			$educationPrograms->createSpeciality (
				$form->title->value, 
				$form->labourIntensive->value
			);
			$this->flash (
				'Направление успешно добавлено',
				'/education_programs/index',
				3
			);
		}
		
		public function action_add_discipline ($params) {
			$this->set ('buttonCaption', 'Добавить');
			
			$request = $this->getRequest ();
			$request->set ('speciality', $params['speciality_id']);

			$form = Form_Discipline_Add::create ('/add_discipline/' . $params['speciality_id']);
			$this->set ('form', $form);
			$method = $form->method ();
			$form->setValue ('speciality', $params['speciality_id']);
			
			if (empty ($request->$method)) {
				$this->render ('education_programs/discipline_form');
			}
			
			$educationPrograms = Model_Education_Programs::create ();
			
			if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/discipline_form");
            }

			$educationPrograms->createDiscipline (
				$form->speciality->value,
				$form->title->value,
				$form->coef->value,
				$form->labourIntensive->value
			);			
			$this->flash (
				'Дисциплина успешно добавлена',
				'/education_programs/index',
				3				
			);
		}
		
		public function action_add_section ($params) {
			$this->set ('buttonCaption', 'Добавить');
			
			$request = $this->getRequest ();
			$request->set ('discipline', $params['discipline_id']);
			
			$form = Form_Section_Add::create ('/add_section/' . $params['discipline_id']);			
			$this->set ('form', $form);
			$method = $form->method ();
			if (empty ($request->$method)) {
				$this->render ('education_programs/section_form');
			}
			
			$educationPrograms = Model_Education_Programs::create ();
			
			if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/section_form");
            }

			$educationPrograms->createSection (
				$form->discipline->value,
				$form->title->value,
				$form->number->value
			);			
			$this->flash (
				'Раздел успешно добавлен',
				'/education_programs/index',
				3				
			);		
		}
		
		public function action_remove_speciality ($params) {
			$educationPrograms = Model_Education_Programs::create ();
			$educationPrograms->removeSpeciality ($params['speciality_id']);
			
			$this->flash (
				'Направление успешно удалено',
				'/education_programs/index',
				3
			);
		}
		
		public function action_remove_discipline ($params) {
			$educationPrograms = Model_Education_Programs::create ();
			$educationPrograms->removeDiscipline ($params['discipline_id']);
			
			$this->flash (
				'Дисциплина успешно удалена',
				'/education_programs/index',
				3
			);
		}
		
		public function action_remove_section ($params) {
			$educationPrograms = Model_Education_Programs::create ();
			$educationPrograms->removeSection ($params['section_id']);
			
			$this->flash (
				'Раздел успешно удален',
				'/education_programs/index',
				3
			);
		}
		
		public function action_edit_speciality ($params) {
			$this->set ('buttonCaption', 'Сохранить');
			
			$request = $this->getRequest ();
			$request->set ('speciality', $params['speciality_id']);
			
			$form = Form_Speciality_Edit::create('/edit_speciality/' . $params['speciality_id']);
			$this->set ('form', $form);			
			$method = $form->method ();
			
			$educationPrograms = Model_Education_Programs::create ();
			
			if (empty ($request->$method)) {
				if (! $form->validateID ($educationPrograms, $request)) {
					$this->render ('education_programs/speciality_form');
				}
				
				$educationPrograms->getSpeciality ($params['speciality_id'], $title, $labourIntensive);
				
				$form->setValue ('title', $title);
				$form->setValue ('labourIntensive', $labourIntensive);
				
				$this->render ('education_programs/speciality_form');
			}
			
			if (! $form->validate ($request, $educationPrograms)) {
                $this->render ("education_programs/speciality_form");
            }			

			$educationPrograms->editSpeciality (
				$params['speciality_id'],
				$form->title->value,
				$form->labourIntensive->value
			);
			
			$this->flash (
				'Данные по направлению успешно изменены',
				'/education_programs/index',
				3
			);		
		}

		public function action_edit_discipline ($params) {			
			$this->set ('buttonCaption', 'Сохранить');
			
			$request = $this->getRequest ();
			$request->set ('discipline', $params['discipline_id']);
			
			$form = Form_Discipline_Edit::create ('/edit_discipline/' . $params['discipline_id']);
			$this->set ('form', $form);
			$method = $form->method ();
						
			$educationPrograms = Model_Education_Programs::create ();
			
			if (empty ($request->$method)) {
				if (! $form->validateID ($educationPrograms, $request)) {
					$this->render ('education_programs/discipline_form');
				}
				
				$educationPrograms->getDiscipline ($params['discipline_id'], $title, $labourIntensive, $coef);
				
				$form->setValue ('title', $title);
				$form->setValue ('labourIntensive', $labourIntensive);
				$form->setValue ('coef', $coef);				
				
				$this->render ('education_programs/discipline_form');
			}
						
			if (! $form->validate ($request, $educationPrograms)) {
				$this->render ('education_programs/discipline_form');
			}
			
			$educationPrograms->editDiscipline (
				$params['discipline_id'],
				$form->title->value,
				$form->labourIntensive->value,
				$form->coef->value
			);
			
			$this->flash (
				'Данные по дисциплине успешно изменены',
				'/education_programs/index',
				3
			);		
		}
		
		public function action_edit_section ($params) {
			$this->set ('buttonCaption', 'Сохранить');
			
			$request = $this->getRequest ();
			$request->set ('section', $params['section_id']);
			
			$form = Form_Section_Edit::create ('/edit_section/' . $params['section_id']);
			$this->set ('form', $form);
			$method = $form->method ();

			$educationPrograms = Model_Education_Programs::create ();
						
			if (empty ($request->$method)) {				
				if (! $form->validateID ($educationPrograms, $request)) {
					$this->render ('education_programs/section_form');
				}				
				
				$educationPrograms->getSection ($params['section_id'], $title, $number);
				
				$form->setValue ('title', 	$title);
				$form->setValue ('number', 	$number);
				
				$this->render ('education_programs/section_form');
			}
			
			if (! $form->validate ($request, $educationPrograms)) {
				$this->render ('education_programs/section_form');
			}
			
			$educationPrograms->editSection (
				$params['section_id'],
				$form->title->value,
				$form->number->value
			);
			
			$this->flash (
				'Данные по разделу успешно изменены',
				'/education_programs/index',
				3
			);			
		}		
	}
?>