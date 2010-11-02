<?php

    /* $Id: $ */

    class Controller_Checkpoints extends Mvc_Controller_Abstract {

        /**
        * Создание/редактирование контрольной точки.
        */
        public function action_edit() {
            $request = $this->getRequest();
            $params = $request->post;

            if (!isset($params['active'])) {
                $params['active'] = false;
            }

            $model = Model_Checkpoint::create();

            if (!$params['active']) {
                $model->setCheckpointInactive(
                    $params['section_id']
                );
            } else {
                $model->setCheckpoint(
                    $params['section_id'],
                    $params['active'],
                    $params['title'],
                    $params['text'],
                    $params['type'],
                    $params['test_id']
                );
            }

            $links = Resources::getInstance()->links;
            $link = $links->get('sections.edit', array('section_id' => $params['section_id']));
            $this->flash('Контрольная точка успешно изменена', $link);
        }

        /**
        * Открывает доступ к разделу.
        */
        public function action_set_pass($params) {
            $model_checkpoint = Model_Checkpoint::create();
            $model_education_programs = Model_Education_Programs::create();

            $model_checkpoint->setCheckpointPass($params);
            $discipline_id = $model_education_programs->getDisciplineNumberBySection($params['section_id']);

            $links = Resources::getInstance()->links;
            $link = $links->get('teacher.discipline', array('discipline_id' => $discipline_id['discipline_id']));
            $this->flash('Доступ к разделу успешно открыт', $link);
        }

        /**
        * Закрывает доступ к разделу.
        */
        public function action_remove_pass($params) {
            $model_checkpoint = Model_Checkpoint::create();
            $model_education_programs = Model_Education_Programs::create();

            $model_checkpoint->removeCheckpointPass($params);
            $discipline_id = $model_education_programs->getDisciplineNumberBySection($params['section_id']);

            $links = Resources::getInstance()->links;
            $link = $links->get('teacher.discipline', array('discipline_id' => $discipline_id['discipline_id']));
            $this->flash('Доступ к разделу закрыт', $link);
        }

    }