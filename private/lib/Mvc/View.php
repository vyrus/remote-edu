<?php
  
    /* $Id$ */
    
    /**
    * Класс для отображения шаблонов.
    */
    class Mvc_View {
        const TPL_EXT = '.tpl';
        
        protected $_tpl_path;
        protected $_layout = false;
        protected $_vars = array();
        
        /**
        * Проверка на существование файла шаблона
        * 
        * @param string $tpl_name Путь к файлу шаблона без расширения
        */
        function __construct($tpl_name) {
            $this->_tpl_path = VIEWS . DS . $tpl_name . self::TPL_EXT;
            
            if (!file_exists($this->_tpl_path))
            {
                throw new Mvc_View_Exception(
                    sprintf('Не найден шаблон "%s"', $this->_tpl_path)
                );
            }
        }
        
        /**
        * Статичный метод для создания экземпляра класса
        * 
        * @param string $tpl_name Имя файла шаблона без расширения
        * @return object
        */
        public static function create($tpl_name) {
            return new self($tpl_name);
        }
        
        public function setLayout($layout) {
            $this->_layout = 'layouts/' . $layout;
            
            return $this;
        }
        
        /**
        * Установка значения переменной шаблона
        * 
        * @param string $name  Имя переменной
        * @param mixed  $value Значение переменной
        * @return object
        */
        public function set($name, $value) {
            $this->_vars[$name] = $value;
            
            return $this;
        }
        
        public function setVars(array $vars = array()) {
            foreach ($vars as $name => $value) {
                $this->set($name, $value);
            }
                
            return $this;
        }
        
        /**
        * Отображение шаблона
        * 
        * @param boolean $exit Закончить ли работу скрипта после вывода шаблона
        *                      (из самих шаблонов этот параметр не действует)
        */
        public function render($exit = true, $return = false) {
            extract($this->_vars);
            
            ob_start();
                include $this->_tpl_path;
            $out = ob_get_clean();
                                     
            if (false !== $this->_layout) {
                $out = self::create($this->_layout)
                           ->set('content', $out)
                           ->render(false, true);
            }
            
            if ($return) {
                return $out;
            } else {
                echo $out;
            }
            
            if($exit && !ob_get_level()) exit();
        }
    }
  
?>