<?php

    /**
    * Контроллер для управления тестами.
    */
    class Controller_Tests extends Mvc_Controller_Abstract {
        /**
        * Создание нового теста.
        */
        public function action_create() {
            $request = $this->getRequest();
            $method = 'post';

            $data = & $request->$method;

            if (!empty($data)) {
                header('Content-Type: text/plain; charset=utf-8');
                print_r($data);

                /**
                * @todo Magic setter?
                */
                $q = Model_Question_PickOne::create();
                $q->question = $data['question'];
                $q->answers  = $data['answers'];
                $q->correct_answer  = $data['correct_answer'];

                $tid = 1;

                $test = Model_Test::create();
                $test->addQuestions($tid, array($q));

                return;
            }

            $this->render();
        }

        /**
        * Редактирование существующего теста.
        */
        public function action_edit() {
            $tid = 1;

            $test = Model_Test::create();
            $questions = $test->getQuestions($tid);

            header('Content-Type: text/plain; charset=utf-8');
            print_r($questions);
        }

        /**
        * Удаление теста.
        */
        public function action_delete() {
            //
        }

        public function action_ajax_save() {
            $request = $this->getRequest();
            $questions = $request->post['questions'];

            if (get_magic_quotes_gpc()) {
                $questions = stripslashes($questions);
            }

            $questions = json_decode($questions);

            /**
            * @todo Detect question type.
            */

            $pick_ones = array();

            foreach ($questions as $q) {
                $pick_one = Model_Question_PickOne::create();
                $pick_one->question = $q->text;
                $pick_one->answers = $q->answers;
                $pick_one->correct_answer = $q->correct_answer;

                $pick_ones[] = $pick_one;
            }

            $test = Model_Test::create();
            $test->addQuestions(1, $pick_ones);

            print_r($pick_ones);
        }
    }

?>