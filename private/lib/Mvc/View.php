<?php
  
    /* $Id$ */
    
    /**
    * Класс для отображения шаблонов.
    */
    class Mvc_View {
        const DEFAULT_TPL_EXTENSION = 'tpl';
        
        protected $_templates_path;
        protected $_layouts_path;
        protected $_tpl_extension;
        protected $_layout = false;
        protected $_template;
        protected $_vars = array();
        
        /**
        * //
        * 
        * @param
        */
        public function __construct
        (
            $templates_path, $layouts_path = null,
            $tpl_extension = self::DEFAULT_TPL_EXTENSION
        )
        {
            $this->setTemplatesPath($templates_path);
            $this->setTemplatesExtension($tpl_extension);
            
            if (null !== $layouts_path) {
                $this->setLayoutsPath($layouts_path);
            }
        }
        
        /**
        * Статичный метод для создания экземпляра класса
        * 
        * @param string $tpl_name Имя файла шаблона без расширения
        * @return object
        */
        public static function create
        (
            $templates_path, $layouts_path = null,
            $tpl_extension = self::DEFAULT_TPL_EXTENSION
        )
        {
            return new self($templates_path, $layouts_path, $tpl_extension);
        }
        
        public function setTemplatesPath($path) {
            if (!file_exists($path))
            {
                throw new Mvc_View_Exception(
                    sprintf('Не найдена директория шаблонов "%s"', $path)
                );
            }
            
            $this->_templates_path = $path;
            
            return $this;
        }
        
        public function setTemplatesExtension($ext) {
            $this->_tpl_extension = $ext;
        }
        
        public function setLayoutsPath($path) {
            if (!file_exists($path))
            {
                throw new Mvc_View_Exception(
                    sprintf('Не найдена директория макетов "%s"', $path)
                );
            }
            
            $this->_layouts_path = $path;
            
            return $this;
        }
        
        public function setLayout($layout) {
            $this->_layout = $layout;
            
            return $this;
        }
        
        public function setTemplate($tpl) {
            $tpl_path = $this->_templates_path .
                        $tpl .
                        '.' . $this->_tpl_extension;
                        
            $tpl_path = realpath($tpl_path);
            if (!file_exists($tpl_path))
            {
                throw new Mvc_View_Exception(
                    sprintf('Не найден файл шаблона "%s"', $tpl_path)
                );
            }
            
            $this->_template = $tpl_path;
            
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
                include $this->_template;
            $out = ob_get_clean();
                                     
            if (false !== $this->_layout)
            {
                $layout = self::create(
                    $this->_layouts_path, null, $this->_tpl_extension
                );
                
                $out = $layout->setTemplate($this->_layout)
                              ->set('content', $out)
                              ->render(false, true);
            }
            
            if ($return) {
                return $out;
            } else {
                echo $out;
            }
            
            $exit = $exit && !ob_get_level();
            if($exit) exit();
        }
    }
  
?>