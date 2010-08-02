<?php

    /* $Id: $ */

    /**
    * Класс для измерения времени выполнения.
    */
    class Timer {
        private $_start_time,
                $_stop_time,
                $_diff_time;

        public static function create() {
            return new self();
        }

        /**
        * Запускает таймер.
        *
        * @return boolean
        */
        public function start() {
            $this->_start_time = $this->_now();

            return $this;
        }

        /**
        * Останавливает таймер и возвращает время выполнения.
        *
        * @param int $precision Точность округления.
        * @return float
        */
        public function stop($precision = 0) {
            $this->_stop_time = $this->_now();
            $this->_diff_time = $this->_stop_time - $this->_start_time;

            return $this->get($precision);
        }

        /**
        * Dозвращает время выполнения.
        *
        * @param int $precision Точность округления.
        * @return float
        */
        public function get($precision = 3) {
            if (null === $this->_stop_time) {
                $this->_diff_time = $this->now() - $this->_start_time;
            }

            return round($this->_diff_time, $precision);
        }

        /**
        * Возвращает текущее время в формате UNIX timestamp с микросекундами.
        *
        * @return float
        */
        protected function _now() {
           return microtime(1);
        }
    }

?>