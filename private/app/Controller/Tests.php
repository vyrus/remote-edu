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
        public function action_edit(array $params) {
            if (empty($params)) {
                $this->flash('Не указан идентификатор теста', '#', false);
            }

            /**
            * @todo Сделать нормальный маршрут.
            */
            $test_id = array_shift($params);

            $this->set('test_id', $test_id);
            $this->render('tests/create');
        }

        /**
        * Удаление теста.
        */
        public function action_delete() {
            //
        }

        public function action_examination(array $params) {
            /**
            * @todo Check if 'test_id' param is set.
            */
            $test_id = array_shift($params);

            $test = Model_Test::create();
            $data = $test->get($test_id);

            $attempts_remaining = $test->attemptsRemains(
                $test_id, $data['attempts_limit']
            );

            /**
            * @todo Нормальная ссылка.
            */
            if (!$attempts_remaining) {
                $msg = 'У вас больше не осталось попыток для сдачи этого теста';
                $this->flash($msg, '#', false);
            }

            $this->set('test', $data);
            $this->set('attempts_remaining', $attempts_remaining);
            $this->render();
        }

        public function action_results(array $params = array()) {
            if (null === ($test_id = array_shift($params))) {
                /**
                * @todo Insert real link here.
                */
                $msg = 'Не указан идентификатор теста';
                $this->flash($msg, '#', false);
            }

            /**
            * @todo Allow only ints in test_id with approriate route.
            */

            $test = Model_Test::create();
            $results = $test->getResults($test_id);

            $this->set('results', $results);
            $this->render();
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
            $test_id   = $request->post['test_id'];
            $questions = $request->post['questions'];

            /* Если включено автоматическое экрание данных, */
            if (get_magic_quotes_gpc()) {
                /* убираем лишние слеши */
                $questions = stripslashes($questions);
            }

            /* Парсим JSON-формат */
            $questions = json_decode($questions);

            /* Массив объектов вопросов */
            $add_questions  = array();
            $edit_questions = array();

            $return_errors = array();

            /* Перебираем присланные вопросы */
            foreach ($questions as $q)
            {
                $obj = null;

                switch ($q->type)
                {
                    /* Если вопрос с одним ответом */
                    case Model_Question::TYPE_PICK_ONE:
                        /* Создаём и наполняем контейнер вопроса */
                        $obj = Model_Question_PickOne::create();
                        $obj->question = $q->question;
                        $obj->answers = $q->answers;
                        $obj->correct_answer = $q->correct_answer;
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

                if (isset($q->question_id)) {
                    $id_key = 'question_id';
                    $id_val = $q->question_id;
                }
                elseif (isset($q->tmp_id)) {
                    $id_key = 'tmp_id';
                    $id_val = $q->tmp_id;
                }

                $errors = $obj->validate();

                if (!empty($errors))
                {
                    foreach ($errors as $target => $error) {
                        $errors[$target] = $error->getErrorMessage();
                    }

                    $return_errors[] = array(
                        $id_key  => $id_val,
                        'errors' => $errors
                    );

                    continue;
                }

                if (isset($q->question_id))
                {
                    $edit_questions[] = array(
                        $id_key => $id_val,
                        'obj'   => $obj
                    );
                }
                elseif (isset($q->tmp_id))
                {
                    $add_questions[] = array(
                        $id_key => $id_val,
                        'obj'   => $obj
                    );
                }
            }

            if (!empty($return_errors))
            {
                $response = array(
                    'result'       => false,
                    'field_errors' => $return_errors
                );

                echo json_encode($response);
                return;
            }

            $response = array();
            /* Инициализируем модель теста и добавляем вопросы */
            $test = Model_Test::create();

            if (!empty($add_questions)) {
                $new_ids = $test->addQuestions($test_id, $add_questions);
                $response['new_ids'] = $new_ids;
            }

            if (!empty($edit_questions)) {
                $test->editQuestions($edit_questions);
            }

            /* Возвращаем ответ, что всё прошло успешно */
            $response['result'] = true;
            echo json_encode($response);
        }

        public function action_ajax_load_test() {
            $request = $this->getRequest();

            /**
            * @todo Check whether 'test_id' key exists.
            */

            $test_id = $request->post['test_id'];

            /**
            * @todo Проверить, возврашают ли запросы записи.
            *
            * @var Model_Test
            */
            $test = Model_Test::create();
            $options   = $test->get($test_id);
            $questions = $test->getQuestions($test_id, false);

            $response = array(
                'result'    => true,
                'options'   => $options,
                'questions' => $questions
            );

            echo json_encode($response);
        }

        public function action_ajax_get_exam_questions() {
            $request = $this->getRequest();
            $test_id = (int) $request->post['test_id'];

            $test = Model_Test::create();
            $data = $test->get($test_id);

            try {
                $questions = $test->start($test_id, $data['num_questions']);
            }
            catch(Model_Test_Exception $e) {
                $response = array(
                    'result' => false,
                    'error'  => $e->getMessage()
                );

                echo json_encode($response);
                return;
            }

            $questions = (object) $questions;
            $response = array('result' => true, 'questions' => $questions);
            echo json_encode($response);
        }

        public function action_ajax_check_exam_answers() {
            $request = $this->getRequest();
            $test_id = (int) $request->post['test_id'];
            $answers = $request->post['answers'];

            /* Если включено автоматическое экрание данных, */
            if (get_magic_quotes_gpc()) {
                /* убираем лишние слеши */
                $answers = stripslashes($answers);
            }

            $answers = json_decode($answers, true);

            try {
                $test = Model_Test::create();
                $results = $test->stop($test_id, $answers);
            }
            catch(Model_Test_Exception $e) {
                $response = array(
                    'result' => false,
                    'error'  => $e->getMessage()
                );

                echo json_encode($response);
                return;
            }

            $response = array(
                'result'  => true,
                'results' => $results,
            );
            echo json_encode($response);
        }

        public function action_ajax_delete_question() {
            $request = $this->getRequest();

            /**
            * @todo Check whether 'question_id' key exists.
            */
            $question_id = $request->post['question_id'];

            $test = Model_Test::create();
            $result = $test->deleteQuestion($question_id);

            $response = (
                $result ?
                    array('result' => true) :
                    array(
                        'result' => false,
                        'error' => 'Неизвестная ошибка при удалении вопроса'
                    )
            );

            echo json_encode($response);
        }
    }

?>