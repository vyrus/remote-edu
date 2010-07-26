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

        /**
        * Сохранение теста.
        */
        public function action_ajax_save() {
            /* Берём список вопросов теста */
            $request = $this->getRequest();
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
            $test->addQuestions(1, $q_objs);

            /* Возвращаем ответ, что всё прошло успешно */
            $response = array('result' => true);
            echo json_encode($response);
        }
    }

?>