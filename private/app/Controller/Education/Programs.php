<?php
	/**
	* Обработка действий, связанных с образовательными дисциплинами.
	* 
	* @param
	* @return
	*/
	
	class Controller_Education_Programs extends Mvc_Controller_Abstract {
		public function action_index () {
			$educationPrograms = Model_Education_Programs::create ();
			$this->set ('specialities', $educationPrograms->getSpecialities ());
			$this->set ('disciplines', $educationPrograms->getSpecialitiesDisciplines ());
			$this->set ('sections', $educationPrograms->getDisciplinesSections ());
			
			$this->render ("education_programs/index");
		}
		
		public function action_add_speciality () {			
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
			$request = $this->getRequest ();
			$request->set ('speciality', $params['speciality_id']);

			$form = Form_Discipline_Add::create ('/add_discipline/' . $params['speciality_id']);
			$this->set ('form', $form);
			$method = $form->method ();
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
	}
?>