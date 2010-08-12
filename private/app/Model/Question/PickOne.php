<?php

    /**
    * Контейнер для данных вопроса с одним правильным ответом.
    */
    class Model_Question_PickOne extends Model_Question_Abstract {
        /**
        * Тип вопроса.
        *
        * @var mixed
        */
        protected $_type = Model_Question::TYPE_PICK_ONE;

        /**
        * Текст вопроса.
        *
        * @var string
        */
        public $question;

        /**
        * Список ответов.
        *
        * @var array
        */
        public $answers;

        /**
        * Ключ правильного ответа из массива self::$answers;
        *
        * @var mixed
        */
        public $correct_answer;

        /**
        * Создание нового экзепляра контейнера.
        *
        * @return Model_Question_PickOne
        */
        public static function create() {
            return new self();
        }

        public function validate() {
            $errors = array();

            $answer = & $this->answers[$this->correct_answer];
            if (!isset($answer))
            {
                $code = Model_Question_PickOne_Exception::NO_CORRECT_ANSWER;

                $errors['question'] =
                    new Model_Question_PickOne_Exception($code);
            }
            unset($answer);

            foreach ($this->answers as $key => $answer) {
                if (empty($answer)) {
                    unset($this->answers[$key]);
                }
            }

            $empty = empty($this->question) || sizeof($this->answers) < 4;
            if ($empty) {
                $code = Model_Question_PickOne_Exception::EMPTY_FIELDS;

                $errors['question'] =
                    new Model_Question_PickOne_Exception($code);
            }

            $jevix = $this->_getJevix();
            $j_errors = array();

            $this->question = $jevix->parse($this->question, $j_errors);
            if (!empty($j_errors)) {
                $code = Model_Question_PickOne_Exception::INVALID_HTML;

                $first_error = array_shift($j_errors);
                $first_error = '. ' . $first_error['message'];

                $errors['question'] =
                    new Model_Question_PickOne_Exception($code, $first_error);
            }

            /**
            * @todo Сделать error target для ответов.
            */
            foreach ($this->answers as $key => $a) {
                $this->answers[$key] = $jevix->parse($a, $j_errors);
            }

            return $errors;
        }

        public function getExamData() {
            return array(
                'question' => $this->question,
                'answers'  => $this->answers
            );
        }

        public function isCorrectAnswer($answer) {
            return ($this->correct_answer == $answer);
        }

        public function freeze() {
            return serialize(array(
                'question'       => $this->question,
                'answers'        => $this->answers,
                'correct_answer' => $this->correct_answer
            ));
        }

        public static function thaw($data) {
            $data = (object) unserialize($data);

            $q = self::create();

            $q->question        = $data->question;
            $q->answers         = $data->answers;
            $q->correct_answer  = $data->correct_answer;

            return $q;
        }

        protected function _getJevix() {
            require_once 'jevix.class.php';

            $jevix = new Jevix();

            /* Устанавливаем разрешённые теги (все остальные - запрещенные) */
            $jevix->cfgAllowTags(array('img', 'code'));

            /* Устанавливаем коротие теги (не имеющие закрывающего тега) */
            $jevix->cfgSetTagShort(array('img'));

            /* Устанавливаем преформатированные теги (в них всё будет заменятся
            на HTML-сущности) */
            $jevix->cfgSetTagPreformatted(array('code'));

            /* Устанавливаем теги, которые необходимо вырезать из текста вместе
            с контентом */
            $jevix->cfgSetTagCutWithContent(array('script', 'object', 'iframe',
                                                  'style'));

            /* Устанавливаем разрешённые параметры тегов и их допустимые
            значения */
            $jevix->cfgAllowTagParams('img',
                                      array('src',
                                            'alt'    => '#text',
                                            'title',
                                            'align'  => array('right', 'left',
                                                              'center'),
                                            'width'  => '#int',
                                            'height' => '#int',
                                            'hspace' => '#int',
                                            'vspace' => '#int'));

            /* Устанавливаем параметры тегов являющиеся обязяательными, без них
            вырезает тег, оставляя содержимое */
            $jevix->cfgSetTagParamsRequired('img', 'src');

            /* Включаем режим XHTML (по умолчанию включен) */
            $jevix->cfgSetXHTMLMode(true);

            /* Выключаем режим замены переноса строк на тег <br/>. (по умолчанию
            включен) */
            $jevix->cfgSetAutoBrMode(false);

            /* Выключаем режим автоматического определения ссылок. (по умолчанию
            включен) */
            $jevix->cfgSetAutoLinkMode(false);

            /* Отключаем типографирование в определенном теге */
            $jevix->cfgSetTagNoTypography('code');

            return $jevix;
        }
    }

?>
