<?php
    
    /* $Id$ */

    /**
    * Класс формы регистрации слушателя.
    */
    class Form_Registration_Student extends Form_Registration {
        /**
        * Форма с полями первичной регистрации.
        * 
        * @var const
        */
        const TYPE_MINIMAL = 'minimal';
        
        /**
        * Форма с полями вторичной регистрации.
        * 
        * @var const
        */
        const TYPE_EXTENDED = 'extended';
        
        /**
        * Форма с полным набором полей.
        * 
        * @var
        */
        const TYPE_FULL = 'full';
        
        /**
        * Метод-конструктор класса. Заполняет структуру формы.
        * 
        * @param  string $action Значение атрибута "action".
        * @param  mixed  $type   Тип формы.
        * @return void
        */
        public function __construct($action, $type = self::TYPE_MINIMAl) {
            $this
                /* Устанавливаем параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
            ;
            
            /* Минимальный набор полей */
            if (self::TYPE_MINIMAL === $type || self::TYPE_FULL === $type) {
                $this   
                    /* Имя пользователя */
                    ->addField('login')
                    ->setValidator('/^[a-z0-9_]{3,}$/ixu')
                    ->setError(
                        'Имя пользователя должно состоять из букв латинского ' . 
                        'алфавита, цифр и символа подчёркивания (минимальная' .
                        'длина - 3 символа)'
                    )
                    
                    /* Пароль */
                    ->addField('passwd')
                    ->setValidator('/^[a-z0-9_]+$/ixu')
                    ->setError(
                        'Пароль должен состоять из латинских букв, цифр и ' .
                        'символа подчёркивания'
                    )
                    
                    /* Проверка пароля */
                    ->addField('passwd_check')
                                     
                    /* Email */
                    ->addField('email')
                    ->setValidator('|[0-9a-z-]+@[0-9a-z-^\.]+\.[a-z]{2,6}|i')
                    ->setError(
                        'Некорректный адрес электронной почты'
                    )
                ;
            }
        }
        
        /**
        * Создание экземпляра класса.
        * 
        * @param  string $action Значение атрибута "action".
        * @param  mixed  $type   Тип формы.
        * @return Form_Registration
        */
        public static function create($action, $type = self::TYPE_MINIMAl) {
            return new self($action, $type);
        }
        
        /**
        * Дополнительная проверка на совпадение паролей.
        * 
        * @param  Http_Request $request Объект запроса.
        * @param  Model_User   $user    Модель работы с пользователями.
        * @return boolean
        */
        public function validate(Http_Request $request, Model_User $user) {
            /* Выполняем базовую проверку */
            $result = parent::validate($request, $user);
            
            /* Если пароль прошёл проверку, то оба поля пароля на совпадение */
            if (!$this->hasError('passwd') &&
                $this->passwd->value !== $this->passwd_check->value)
            {
                $this->setValue('passwd', '');
                $this->setValidationError('passwd', 'Пароли должны совпадать');
                
                $result = false;
            }
            
            if ($this->hasError('passwd')) {
                $this->setValue('passwd_check', '');
            }
            
            if (false === $result) {
                return false;
            }
            
            return $result;
        }
    }

?>