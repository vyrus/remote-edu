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

        public function action_ajax_save_options() {
            $request = $this->getRequest();

            $form = Form_Test_Options::create();
            $form->validate($request);

            $fields = array('theme', 'num_questions', 'errors_limit',
                            'attempts_limit');

            $form_errors = array();

            foreach ($fields as $field)
            {
                if (isset($form->$field->error)) {
                    $form_errors[$field] = $form->$field->error;
                }
            }

            if (!empty($form_errors))
            {
                $response = array(
                    'result'     => false,
                    'error'      => 'Ошибки при заполнении формы.',
                    'formErrors' => $form_errors
                );

                echo json_encode($response);
                return;
            }

            $theme          = $form->theme->value;
            $num_questions  = $form->num_questions->value;
            $errors_limit   = $form->errors_limit->value;
            $attempts_limit = $form->attempts_limit->value;

            $test = Model_Test::create();

            /**
            * @todo Переписать для использования контейнера.
            */

            $test_id = & $request->post['test_id'];

            if (!isset($test_id) || !is_numeric($test_id)) {
                /**
                * @todo Check if $test_id is set.
                */
                $test_id = $test->add($theme, $num_questions, $errors_limit,
                                      $attempts_limit);
                $response = array('result' => true, 'testId' => $test_id);
            } else {
                $test_id = (int) $test_id;

                $test->update($test_id, $theme, $num_questions,
                              $errors_limit, $attempts_limit);
                $response = array('result' => true);
            }

            echo json_encode($response);
        }

        /**
        * Сохранение вопросов теста.
        */
        public function action_ajax_save_questions() {
            /* Берём идентификатор теста и список вопросов */
            $request = $this->getRequest();
            $test_id   = $request->post['testId'];
            $questions = $request->post['questions'];

            /* Если включено автоматическое экрание данных, */
            if (get_magic_quotes_gpc()) {
                /* убираем лишние слеши */
                $questions = stripslashes($questions);
            }

            /* Парсим JSON-формат */
            $questions = json_decode($questions);

            /* Массив объектов вопросов */
            $q_objs = array();

            /* Перебираем присланные вопросы */
            foreach ($questions as $q)
            {
                switch ($q->type)
                {
                    /* Если вопрос с одним ответом */
                    case Model_Question::TYPE_PICK_ONE:
                        /* Создаём и наполняем контейнер вопроса */
                        $obj = Model_Question_PickOne::create();
                        $obj->question = $q->text;
                        $obj->answers = $q->answers;
                        $obj->correct_answer = $q->correct_answer;

                        /* Сохраняем вопрос в списке */
                        $q_objs[] = $obj;
                        break;

                    /* Если вопрос неизвестного типа, возвращаем ошибку */
                    default:
                        $response = array(
                            'result' => false,
                            'error'  => 'Неизвестный тип вопроса: ' . $q->type
                        );

                        echo json_encode($response);
                        return;
                        break;
                }
            }

            /* Инициализируем модель теста и добавляем вопросы */
            $test = Model_Test::create();
            $test->addQuestions($test_id, $q_objs);

            /* Возвращаем ответ, что всё прошло успешно */
            $response = array('result' => true);
            echo json_encode($response);
        }
    }

?>