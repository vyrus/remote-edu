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
        * Заголовки писем по типу.
        * 
        * @var array
        */         
        protected $_subjects = array(
            self::TYPE_REG_STUDENT => 'Регистрация',
            self::TYPE_REG_EMPLOYEE => 'Регистрация'
        );
        
        /**
        * Тексты сообщений по типу.
        * 
        * @var array
        */
        protected $_messages = array(
            self::TYPE_REG_STUDENT => 'Вы зарегистрированы под логином %s. Для активации аккаунта пройдите по ссылке %s.',
            self::TYPE_REG_EMPLOYEE => 'Вы зарегистрированы под логином %s. Для активации аккаунта и получения пароля пройдите по ссылке %s.'
        );
        
        /**
        * Объект для отправки писем.
        * 
        * @var Zend_Mail
        */
        protected $_mail;
        
        /**
        * Базовый адрес сайта (используется для построения правильных ссылок
        * в письме.
        * 
        * @var string
        */
        protected $_base_url;

        /**
        * Кодировка сообщений.
        * 
        * @var string
        */
        protected $_charset = 'UTF-8';
        
        /**
        * Метод-конструктор класса.
        * 
        * @param  string $base_url   Базовый адрес сайта.
        * @param  string $from_email Адрес отправителя писем.
        * @param  string $from_email Имя отправителя.
        * @return void
        */
        public function __construct($base_url, $from_email, $from_name) {
            $this->_mail = new Zend_Mail($this->_charset);
            $this->_mail->setFrom($from_email, $from_name);
            
            $this->_base_url = $base_url;
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @param  string $base_url   Базовый адрес сайта.
        * @param  string $from_email Адрес отправителя писем.
        * @param  string $from_email Имя отправителя.
        * @return void
        */
        public static function create($base_url, $from_email, $from_name) {
            return new self($base_url, $from_email, $from_name);
        }
        
        /**
        * Отправка сообщения о регистрации новому слушателю.
        * 
        * @param  int    $id              Идентификатор пользователя.
        * @param  string $login           Логин на сайте.
        * @param  string $email           Email пользователя.
        * @param  string $activation_code Код активации.
        * @return
        */
        public function sendRegLetterStudent($id, $login, $email, $activation_code) {
            /* Создаём ссылку для активации */
            $format = '%s/activate_student/%d/%s/';
            $link = sprintf($format, $this->_base_url, $id, $activation_code);
            
            /* Получаем заголовок и текст письма */
            $subject = $this->_getSubject(self::TYPE_REG_STUDENT);
            
            $params = array($login, $link);
            $message = $this->_getMessage(self::TYPE_REG_STUDENT, $params);
            
            return $this->_send($email, $subject, $message);
        }
        
        /**
        * Отправка сообщения о регистрации новому cотруднику.
        * 
        * @param  int    $id              Идентификатор пользователя.
        * @param  string $login           Логин на сайте.
        * @param  string $email           Email пользователя.
        * @param  string $activation_code Код активации.
        * @return
        */
        public function sendRegLetterEmployee($id, $login, $email, $activation_code) {
            /* Создаём ссылку для активации */
            $format = '%s/activate_employee/%d/%s/';
            $link = sprintf($format, $this->_base_url, $id, $activation_code);
            
            /* Получаем заголовок и текст письма */
            $subject = $this->_getSubject(self::TYPE_REG_EMPLOYEE);
            
            $params = array($login, $link);
            $message = $this->_getMessage(self::TYPE_REG_EMPLOYEE, $params);
            
            return $this->_send($email, $subject, $message);
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
                        
            return $this->_mail->send();
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