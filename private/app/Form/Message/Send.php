<?php
    class Form_Message_Send extends Form_Abstract {
        private function addSubject () {
            $this->addField('subject')
                ->setValidator('/^.{1,255}$/ixu')
                ->setError ('Тема сообщения не может быть пустой или превышать 255 символов');
            return $this;
        }

        private function addMessage() {
            $this->addField('message')
                ->setValidator('/.+/ixu')
                ->setError ('Сообщение не может быть пустым');
            return $this;
        }

        public function __construct($action) {
            $this->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->addField('recipient')
                ->addSubject()
                ->addMessage();
        }
    }
?>