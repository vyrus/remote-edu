<?php
    class Form_Checkpoint_Abstract extends Form_Abstract {

        private function addActive() {
            $this
                ->addField('active');
            return $this;
        }

        private function addTitle() {
            $this
                ->addField('title')
                ->setValidator('/^.{3,256}$/ixu')
                ->setError ('Заголовок контрольной точки -- последовательность символов длиной не менее 3 символов и не более 256');
            return $this;
        }

        private function addText() {
            $this
                ->addField('text')
                ->setValidator('/^.{3,256}$/ixu')
                ->setError ('Текст контрольной точки -- последовательность символов длиной не менее 3 символов и не более 256');
            return $this;
        }

        private function addType() {
            $this
                ->addField('type');
            return $this;
        }

        private function addTestId() {
            $this
                ->addField('test_id');
            return $this;
        }

        public function __construct($action) {
            $this
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->addActive()
                ->addTitle()
                ->addText()
                ->addType()
                ->addTestId();
        }

    }
?>