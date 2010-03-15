<?php

    /* $Id$ */

    /**
    * Класс для рассылки почтовых сообщений.
    */
    class Postman {
        /**
        * Тип письма: регистрация слушателя.
        *
        * @var const
        */
        const TYPE_REG_STUDENT = 'reg-student';

        /**
        * Тип письма: регистрация сотрудника.
        *
        * @var const
        */
        const TYPE_REG_EMPLOYEE = 'reg-employee';

        /**
        * Тип письма: договор об обучении.
        *
        * @var const
        */
        const TYPE_CONTRACT = 'contract';

        /**
        * Тип прикреплённого файла: документ word.
        *
        * @var const
        */
        const TYPE_ATTACHMENT = 'application/msword';

        /**
        * Имя прикреплённого файла.
        *
        * @var const
        */
        const ATTACHMENT_NAME = 'Договор об оказании образовательных услуг';

        /**
        * Транспорт для отправки: функция mail().
        *
        * @var const
        */
        const TRANSPORT_SENDMAIL = 'sendmail';

        /**
        * Транспорт для отправки: SMTP-сервер.
        *
        * @var const
        */
        const TRANSPORT_SMTP = 'smtp';

        /**
        * Заголовки писем по типу.
        *
        * @var array
        */
        protected $_subjects = array(
            self::TYPE_REG_STUDENT => 'Регистрация',
            self::TYPE_REG_EMPLOYEE => 'Регистрация',
            self::TYPE_CONTRACT => 'Договор об оказании услуг по дистанционному обучению'
        );

        /**
        * Тексты сообщений по типу.
        *
        * @var array
        */
        protected $_messages = array(
            self::TYPE_REG_STUDENT => 'Вы зарегистрированы под логином %s. Для активации аккаунта пройдите по ссылке %s.',
            self::TYPE_REG_EMPLOYEE => 'Вы зарегистрированы под логином %s. Для активации аккаунта и получения пароля пройдите по ссылке %s.',
            self::TYPE_CONTRACT => 'Во вложении находиться договор об оказании услуг по дистанционному обучению. Для начала обучения договор необходимо распечатать, подписать и доставить в центр обучения.'
        );

        /**
        * Объект для отправки писем.
        *
        * @var Zend_Mail
        */
        protected $_mail;

        /**
        * Транспорт, который будет использован для отправки писем.
        *
        * @var Zend_Mail_Transport_Abstract
        */
        protected $_transport;

        /**
        * Кодировка сообщений.
        *
        * @var string
        */
        protected $_charset = 'UTF-8';

        /**
        * Метод-конструктор класса.
        *
        * @param  string $config Настройки отправки почты.
        * @return void
        */
        public function __construct($config)
        {
            $this->_mail = new Zend_Mail($this->_charset);
            $this->_mail->setFrom($config['from_email'], $config['from_name']);

            /* Выбираем, какой транспорт для отправки почты использовать */
            switch ($config['transport'])
            {
                /* Через заданны SMTP-сервер */
                case self::TRANSPORT_SMTP:
                    $smtp = $config['smtp'];
                    $trans = new Zend_Mail_Transport_Smtp($smtp['host'],
                                                          $smtp['config']);
                    break;

                /* Через стандартную функцию mail() */
                case self::TRANSPORT_SENDMAIL:
                default:
                    $trans = new Zend_Mail_Transport_Sendmail();
                    break;
            }

            $this->_transport = $trans;
        }

        /**
        * Создание экземпляра класса.
        *
        * @param  string $config Настройки отправки почты.
        * @return void
        */
        public static function create($config)
        {
            return new self($config);
        }

        /**
        * Отправка сообщения о регистрации новому слушателю.
        *
        * @param  int    $id    Идентификатор пользователя.
        * @param  string $login Логин на сайте.
        * @param  string $email Email пользователя.
        * @param  string $code  Код активации.
        * @return
        */
        public function sendRegLetterStudent($id, $login, $email, $code) {
            /* Создаём ссылку для активации */
            $links = Resources::getInstance()->links;
            $link = $links->getSiteUrl() . 
                    $links->get('student.activate', array('user_id' => $id,
                                                          'code'    => $code));
            
            /* Получаем заголовок и текст письма */
            $subject = $this->_getSubject(self::TYPE_REG_STUDENT);

            $params = array($login, $link);
            $message = $this->_getMessage(self::TYPE_REG_STUDENT, $params);

            return $this->_send($email, $subject, $message);
        }

        /**
        * Отправка сообщения о регистрации новому cотруднику.
        *
        * @param  int    $id    Идентификатор пользователя.
        * @param  string $login Логин на сайте.
        * @param  string $email Email пользователя.
        * @param  string $code  Код активации.
        * @return
        */
        public function sendRegLetterEmployee($id, $login, $email, $code) {
            /* Создаём ссылку для активации */
            $links = Resources::getInstance()->links;
            $link = $links->getSiteUrl() .
                    $links->get('employee.activate', array('user_id' => $id,
                                                           'code'    => $code));

            /* Получаем заголовок и текст письма */
            $subject = $this->_getSubject(self::TYPE_REG_EMPLOYEE);

            $params = array($login, $link);
            $message = $this->_getMessage(self::TYPE_REG_EMPLOYEE, $params);

            return $this->_send($email, $subject, $message);
        }


        /**
        * Отправка договора слушателю.
        *
        * @param  string $user_email      Email пользователя.
        * @param  string $attachFilePath  Путь прикрепляемого файла.
        * @return
        */
        public function sendContractStudent($user_email,$attachFilePath) {
            /* Получаем заголовок и текст письма */
            $subject = $this->_getSubject(self::TYPE_CONTRACT);

            $message = $this->_getMessage(self::TYPE_CONTRACT);

            if (!empty($attachFilePath) && file_exists($attachFilePath))
            {
                $attachment = $this->_mail->createAttachment(file_get_contents($attachFilePath));
                $attachment->type        = self::TYPE_ATTACHMENT;
                $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->filename    = self::ATTACHMENT_NAME;
            }

            return $this->_send($user_email, $subject, $message);
        }

        /**
        * Отправка письма.
        *
        * @param  string $to      Адрес получателя.
        * @param  string $subject Заголовок письма.
        * @param  string $message Текст сообщения.
        * @return
        */
        protected function _send($to, $subject, $message) {
            $this->_mail->setBodyText($message)
                        ->addTo($to)
                        ->setSubject($subject);

            return $this->_mail->send($this->_transport);
        }

        /**
        * Получение заголовка письма по его типу с подстановкой параметров.
        *
        * @param  mixed $type   Тип письма.
        * @param  array $params Параметры.
        * @return
        */
        protected function _getSubject($type, array $params = array()) {
            return vsprintf($this->_subjects[$type], $params);
        }

        /**
        * Получение текста письма по его типу с подстановкой параметров.
        *
        * @param  mixed $type   Тип письма.
        * @param  array $params Параметры.
        * @return
        */
        protected function _getMessage($type, array $params = array()) {
            return vsprintf($this->_messages[$type], $params);
        }
    }

?>