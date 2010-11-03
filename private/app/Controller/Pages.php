<?php

    /* $Id$ */

    /**
    * Контроллер для отображения статичных страниц.
    */
    class Controller_Pages extends Mvc_Controller_Abstract {

        /**
        * Отображение страницы. Название файла страницы передаётся в параметре
        * 'page'.
        */
        public function action_display(array $params) {
            /* Если страница не задана, бросаем эксепшн */
            if (!isset($params['page'])) {
                throw new InvalidArgumentException('Не указана страница');
            }

            /* Составляем полное имя шаблона */
            $handler = $this->getRequest()->_router['handler'];
            $template = sprintf(
                '%s/%s',
                $handler['controller'],
                $params['page']
            );

            $this->render($template);
        }

    }