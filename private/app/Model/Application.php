<?php

    /* $Id$ */

    class Model_Application extends Model_Base implements Interface_ObjectForObserve {

        /**
        * Кол-во заявок на странице
        *
        * @var const
        */
        const APPS_ON_PAGE = 10;

        /**
        * Тип заявки: на программу.
        *
        * @var const
        */
        const TYPE_PROGRAM = 'program';

        /**
        * Тип заявки: на дисциплину.
        *
        * @var const
        */
        const TYPE_DISCIPLINE = 'discipline';

        /**
        * Статус заявки: подана, на рассмотрении.
        *
        * @var const
        */
        const STATUS_APPLIED = 'applied';

        /**
        * Статус заявки: отклонена.
        *
        * @var const
        */
        const STATUS_DECLINED = 'declined';

        /**
        * Статус заявки: принята, ожидаем подписания договора.
        *
        * @var const
        */
        const STATUS_ACCEPTED = 'accepted';

        /**
        * Статус заявки: договор подписан, ожидаем оплаты.
        *
        * @var const
        */

        const STATUS_SIGNED = 'signed';

        /**
        * Статус заявки: оплата внесена, ожидаем окончание обучения для выдачи сертификата.
        *
        * Большая часть уже реализованных функций не знает про эту штуку и проверяет доступность 
        * путем сравнения стоимости дисциплины и ее оплаченной части возможно опираясь на то, что 
        * она signed. Наверное. Кто ж его знает.
        * Наверно надо что-то переделать...
        *
        * @var const
        */
        const STATUS_PREPAID = 'prepaid';

        /**
        * Статус заявки: Обучение окончано. Сертификат выдан/не выдан.

        * Большая часть уже реализованных функций не знает про эту штуку. 
        *
        * @var const
        */
        const STATUS_FINISHED = 'finished';

        /**
        * Статус программы: платная.
        *
        * @var const
        */
        const PROGRAMM_PAID = 'paid';

        /*
        * Список событий
        *
        * @var array
        */
        public $APP_EVENTS = array (
            'EVENT_SIGNED_APP' => 10,
            'EVENT_FINISHED_APP' => 20
        );

        /**
        * Карта соответствия обозначений статусов заявок названиям статусов
        * заявок.
        *
        * @var array
        */
        protected static $_status_map = array(
            self::STATUS_APPLIED  => 'подана',
            self::STATUS_DECLINED => 'отклонена',
            self::STATUS_ACCEPTED => 'принята',
            self::STATUS_SIGNED   => 'подписана',
            self::STATUS_PREPAID  => 'оплачена',
            self::STATUS_FINISHED => 'окончена'
        );

        function __construct() {
            parent::__construct ();
            $this->attachObserver(Model_OpenSection::create());
        }

        /**
        * Создание нового экземпляра класса.
        *
        * @return Model_Application Fluent interface.
        */
        public static function create() {
            return new self();
        }

        /**
        * Возвращает карту статусов из внутренних обозначений в нормальные
        * названия.
        *
        * @return array
        */
        public static function getStatusMap() {
            return self::$_status_map;
        }

        //==============================
        // Методы реализующие возможность наблюдения за объектом
        // Куда б их деть??????????

        protected $_observerList = array();

        /**
        * Прикрепляет объект-наблюдатель
        *
        * @param object:Interface_Observer 
        * @return void
        */
        public function attachObserver(Interface_Observer $object) {
            array_push($this->_observerList,$object);
        }

        /**
        * Открепляет объект-наблюдатель
        *
        * @param object:Interface_Observer 
        * @return void
        */
        public function detachObserver(Interface_Observer $object) {
            foreach ($this->_observerList as &$a) {
                if ($a == $object) {
                    unset($a);
                }
            }
        }

        /**
        * Оповещает объекты-наблюдатели о событии
        *
        * @param $event Имя события
        * @param $data Подробная информация о событии
        * @return void
        */
        protected function notifyObservers($event, $data) {
            foreach ($this->_observerList as $a) {
                $a->updateObjectState(get_class($this), $this, $event, $data);
            }
        }

        //==============================
        // Внесение изменений в заявки

        /**
        * Подача заявки.
        *
        * @param  int $user_id   Идентификатор пользователя.
        * @param  int $object_id Идентификатор объекта.
        * @param  int $type      Тип объекта (программа/дисциплины).
        * @return bool              Подана ли успешно. Если не успешно, то причиной считается факт дубликата
        */
        public function apply($user_id, $object_id, $type) {

            // Проверка на дубликаты
            // !!!Здесь не учитывается тот факт, что студент может хотеть подписаться 
            // на программу целиком, при том, что он уже подписан на отдельные ее дисциплины
            // Считается, что это разные вещи
            if ($this->getAppInfoByData ($user_id, $object_id, $type)) 
                return false;

            $sql = '
                INSERT INTO ' . $this->_tables['applications'] . '
                (user_id, object_id, type, status, date_app)
                VALUES (:uid, :oid, :type, :status, NOW())
            ';

            $status = self::STATUS_APPLIED;

            $values = array(
                ':uid'    => $user_id,
                ':oid'    => $object_id,
                ':type'   => $type,
                ':status' => $status
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            /* Добавляем запись в историю прохождения заявки */
            $app_id = $this->lastInsertId();
            $this->_addHistory($app_id, $status);
            return true;
        }

        /**
        * Изменение статуса заявки на переданный.
        *
        * @param  string $new_status Новый статус
        * @param  int $app_id Идентификатор заявки.
        * @return bool
        */
        public function setAppStatus($new_status,$app_id)
        {
            // занесение нового статуса в БД
            $sql = '
                UPDATE ' . $this->_tables['applications'] . ' a
                SET status = :new_status
                WHERE app_id = :app_id
            ';

            $values = array(
                ':app_id'     => $app_id,
                ':new_status' => $new_status
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            $row_count = $stmt->rowCount();

            // запись истории
            $this->_addHistory($app_id, $new_status);

            if ($row_count) {
                // сигналы об изменении состояния другим классам    
                if (self::STATUS_SIGNED == $new_status) {
                    $this->notifyObservers($this->APP_EVENTS['EVENT_SIGNED_APP'], compact ('app_id'));
                } elseif (self::STATUS_FINISHED == $new_status) {
                    $this->notifyObservers($this->APP_EVENTS['EVENT_FINISHED_APP'], compact ('app_id'));
                }
            }

            return $row_count > 0;
        }

        /**
        * Удаление заявки с указанным идентификатором.
        *
        * @param  int $app_id Идентификатор заявки.
        * @return boolean
        */
        public function delete($app_id) {
            $sql = '
                DELETE
                FROM ' . $this->_tables['applications'] . '
                WHERE app_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($app_id));

            $this->_deleteHistory($app_id);

            $affected = $stmt->rowCount();
            return ($affected > 0);
        }


        //==============================
        // Чтение различных данных

        /**
        * Поиск неотклоненной заявки по идентификатору пользоталя, объекта, и типу объекта.
        *
        * @param  int $user_id   Идентификатор пользователя.
        * @param  int $object_id Идентификатор объекта.
        * @param  int $type      Тип объекта (программа/дисциплины).
        * @return  array         Массив с данными о заявке
        */
        public function getAppInfoByData($user_id, $object_id, $type) {
            $sql = 'SELECT * FROM ' . $this->_tables['applications'] .
            ' WHERE user_id = :uid AND object_id = :oid AND type = :type AND status <> :declined LIMIT 1';
            
            $values = array (
                ':uid'    => $user_id,
                ':oid'    => $object_id,
                ':type'   => $type,
                ':declined' => $this::STATUS_DECLINED
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            return $stmt->fetchAll(Db_PdO::FETCH_ASSOC);
        }

        
        /**
        * Получение информации о заявке пользователя с текущими статусами и названиями
        * программ/дисциплин.
        *
        * @param  int $app_id Идентификатор заявки.
        * @return array
        */
        public function getAppInfo($app_id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['applications'] . ' a
                WHERE app_id = :app_id
            ';

            $values = array(
                ':app_id'          => $app_id
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetch(Db_PdO::FETCH_ASSOC);
        }

	    /**
		* Получить статусы заявок по дисциплине и имени пользователя
		*
        * @param int disciplineId
        * @param int userId
		* @return array
	    */
        public function getAppsStatus($disciplineId, $userId) {
            $sql = '
                SELECT a.status, a.app_id 
                FROM applications a 
                LEFT JOIN disciplines d 
                    ON d.discipline_id = a.object_id AND a.type = \'discipline\' 
                WHERE a.user_id = :user_id AND
                    d.discipline_id = :discipline_id AND a.type = \'discipline\'
                UNION DISTINCT
                SELECT a.status, a.app_id 
                FROM applications a 
                WHERE  a.user_id = :user_id AND 
                    a.object_id = (
                                    SELECT program_id 
                                    FROM disciplines 
                                    WHERE discipline_id = :discipline_id
                                    ) AND 
                    a.type = \'program\'';

            $values = array(
                ':discipline_id' => $disciplineId,
                ':user_id'       => $userId,
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            return $stmt->fetchAll(Db_PdO::FETCH_COLUMN|Db_PdO::FETCH_GROUP);
        }

        /**
        * Получение списка заявок пользователя с текущими статусами и названиями
        * программ/дисциплин.
        *
        * @param  int $user_id Идентификатор пользователя.
        * @return array
        */
        public function getAppsInfo($user_id) {
            $sql = '
                SELECT a.app_id, a.status, a.object_id,
                       contract_filename,
                       p.title AS program_title,
                       d.title AS discipline_title
                FROM ' . $this->_tables['applications'] . ' a

                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON a.type = :type_program
                    AND
                    p.program_id = a.object_id

                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON a.type = :type_discipline
                    AND
                    d.discipline_id = a.object_id

                WHERE user_id = :uid
            ';

            $values = array(
                ':uid'             => $user_id,
                ':type_program'    => self::TYPE_PROGRAM,
                ':type_discipline' => self::TYPE_DISCIPLINE
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $apps = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);
            return $apps;
        }

        /**
        * Получение информации о направлении
        *
        * @param  int $program_id Идентификатор направления.
        * @return array
        */
        // и что? ей здесь разве место?
        public function getProgram($program_id)
        {
            $sql = '
            SELECT `title`,`labour_intensive`,`paid_type`,`cost`
            FROM '. $this->_tables['programs'] .' p
            WHERE
                `program_id`=:pid
            ';

            $values = array(
                ':pid'             => $program_id
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $prog = $stmt->fetch(Db_PdO::FETCH_ASSOC);
            return $prog;
        }

        /**
        * Получение информации о дисциплине
        *
        * @param  int $disc_id Идентификатор дисциплины.
        * @return array
        */
        // и ей??!
        public function getDiscipline($disc_id)
        {
            $sql = '
            SELECT `program_id`,`serial_number`,`title`,`labour_intensive`,`coef`,`responsible_teacher`
            FROM '. $this->_tables['disciplines'] .' d
            WHERE
                `discipline_id`=:did
            ';

            $values = array(
                ':did'             => $disc_id
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $disc = $stmt->fetch(Db_PdO::FETCH_ASSOC);
            return $disc;
        }


        /**
        * Получение обработанных заявок на программы, т.е. заявок, которые были
        * приняты админом, либо которые были приняты админом и по которым
        * подписаны договоры, либо уже оплаченные, либо оконченные.
        *
        * @param  int $user_id Идентификатор пользователя.
        * @return array
        */
        // здесь подразумевается то, что после окончания заявки студенту становятся доступны все разделы
        public function getProcessedAppsForPrograms($user_id) {
            $sql = '
                SELECT a.app_id, a.object_id, a.status, p.title, p.paid_type, p.cost, SUM( pm.amount ) AS total_sum
                FROM ' . $this->_tables['applications'] . ' a
                
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON p.program_id = a.object_id
                
                LEFT JOIN ' . $this->_tables['payments'] . ' pm
                    ON pm.app_id = a.app_id
                    
                WHERE
                    a.type =  :program
                    AND
                    a.status IN (:signed, :prepaid, :finished)
                    AND
                    a.user_id = :uid
                
                GROUP BY a.app_id, a.object_id, a.status, p.paid_type, p.cost
            ';
            //echo $sql; die();
            $values = array(
                ':uid'      => $user_id,
                ':program'  => Model_Application::TYPE_PROGRAM,
                //':accepted' => self::STATUS_ACCEPTED,
                ':signed'   => self::STATUS_SIGNED,
                ':prepaid'  => self::STATUS_PREPAID,
                ':finished' => self::STATUS_FINISHED
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            
            
            $apps = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            return $apps;
        }

        /**
        * Получение обработанных заявок на дисциплины, т.е. заявок, которые были
        * приняты админом, либо которые были приняты админом и по которым
        * подписаны договоры, либо уже оплаченные, либо оконченные.
        *
        * @param  int $user_id Идентификатор пользователя.
        * @return array
        */
        public function getProcessedAppsForDisciplines($user_id) {
            $sql = '
                SELECT a.app_id, a.object_id, a.status, p.program_id,
                       p.paid_type, p.cost, d.title, d.coef, SUM(pm.amount) AS total_sum
                FROM ' . $this->_tables['applications'] . ' a

                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON d.discipline_id = a.object_id

                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON p.program_id = d.program_id
                    
                LEFT JOIN ' . $this->_tables['payments'] . ' pm
                    ON pm.app_id = a.app_id

                WHERE a.type = :discipline AND
                      a.status IN (:signed, :prepaid, :finished) AND
                      a.user_id = :uid
                GROUP BY a.app_id, a.object_id, a.status, p.program_id,
                      p.paid_type, p.cost, d.title, d.coef
            ';

            $values = array(
                ':uid'        => $user_id,
                ':discipline' => Model_Application::TYPE_DISCIPLINE,
                //':accepted'   => self::STATUS_ACCEPTED,
                ':signed'     => self::STATUS_SIGNED,
                ':prepaid'    => self::STATUS_PREPAID,
                ':finished'   => self::STATUS_FINISHED
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($values);

            $apps = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $apps;
        }


        /**
         * Получение списка программ и дисциплин (номер, тип: дисциплина/программа, имя), по которым существуют заявки
         *
         * @return array
         */
        public function getListObjectsApps () {
            $sql = 'SELECT DISTINCT a.object_id, a.type, p.title AS program_title, d.title AS discipline_title 
                FROM ' . $this->_tables['applications'] . ' a
                LEFT JOIN ' . $this->_tables['programs'] . ' p ON a.type = "program" AND a.object_id = p.program_id 
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d ON a.type = "discipline" AND a.object_id = d.discipline_id
                ORDER BY program_title, discipline_title';

            $stmt = $this->prepare($sql);
            $stmt->execute();

            $apps = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);
            return $apps;
        }

        /**
         * Получение списка имен пользователей, которые подавали заявки 
         *
         * @return array
         */
        public function getListUsersApps () {
            $sql = 'SELECT DISTINCT u.user_id, u.surname, u.name, u.patronymic, u.login 
                FROM ' . $this->_tables['applications'] . ' a
                LEFT JOIN ' . $this->_tables['users'] . ' u ON a.user_id = u.user_id
                ORDER BY u.surname ASC, u.name ASC, u.patronymic ASC';

            //echo '<pre>';var_dump($sql );echo '</pre>';
            $stmt = $this->prepare($sql);
            $stmt->execute();

            $apps = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);
            return $apps;
        }

        /**
        * Получение списка всех поданных заявок и их количества с текущими статусами и названиями
        * программ/дисциплин.
        *
        * @param string $sortField Естественное обобщение понятия поля, для осуществления сортировки
        * @param string $sortDirection Направление сортировки 
        * @param string $filterStatus Фильтр по статусу ('all' - все; 'work' - поданные, принятые, 
        *     подписанные, нужно ли сюда оплаченные???; остальные по значению в БД)
        * @param string $filterUserId Фильтр по идентификатору пользователя
        * @param string $filterObjectType Фильтр: программа/дисциплина (испольхуется совместно с $filterObjectId)
        * @param string $filterObjectId Фильтр по идентификатору программы/дисциплины
        * @param int $page Номер странмцы (нумерация с нуля)
   
        * @return array
        */
        public function getAllAppsInfo($sortField, $sortDirection, $filterStatus, $filterUserId, $filterObjectType, $filterObjectId, $page) {
            $values = array(
                //':sort_direction' => $sort_direction,
                //':sort_field'    => $sort_field,
                ':type_program'    => self::TYPE_PROGRAM,
                ':type_discipline' => self::TYPE_DISCIPLINE,
            );

            // селективная часть запроса для выбора данных
            $selectData = '
                SELECT a.app_id, a.status, a.object_id, a.date_app, a.type,
                       u.user_id, u.name, u.surname, u.patronymic, u.login,
                       contract_filename, 
                       p.title AS program_title,
                       d.title AS discipline_title';

            //селективная часть запроса для выбора количества
            $selectCount = 'SELECT COUNT(*)';

            // основная часть запроса
            $sql = '
                FROM ' . $this->_tables['applications'] . ' a

                LEFT JOIN ' . $this->_tables['programs'] . ' p
                    ON a.type = :type_program
                    AND
                    p.program_id = a.object_id

                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                    ON a.type = :type_discipline
                    AND
                    d.discipline_id = a.object_id

                LEFT JOIN ' . $this->_tables['users'] . ' u
                    ON u.user_id = a.user_id
            ';

            $whereWas = false;

            // добавляем условие отбора по статусу заяки
            if ($filterStatus == 'work') {
                    $whereWas = true;
                    $sql .= ' WHERE (a.status = "'. $this::STATUS_APPLIED . '" OR 
                        a.status = "' . $this::STATUS_ACCEPTED . '" OR a.status = "' . $this::STATUS_SIGNED . '")';
            } else 
                if (($filterStatus === $this::STATUS_APPLIED) || ($filterStatus === $this::STATUS_ACCEPTED) ||
                    ($filterStatus === $this::STATUS_SIGNED) || ($filterStatus === $this::STATUS_PREPAID) ||
                    ($filterStatus === $this::STATUS_DECLINED) || ($filterStatus === $this::STATUS_FINISHED)) {
                        $whereWas = true;
                        $sql .= ' WHERE a.status = "' . $filterStatus . '"';
            }

            // добавляем условие отбора по пользователю
            if ($filterUserId != 'all') {
                $values[':user_id'] = $filterUserId;
                if (!$whereWas) {
                       $whereWas = true;
                    $sql .= ' WHERE u.user_id = :user_id';
                } else {
                    $sql .= ' AND u.user_id = :user_id';
                }
            }

            // добаляем условие отбора по предмету заявки
            if ($filterObjectType == 'program') {
                $s = 'p.program_id';
            } else if ($filterObjectType == 'discipline') {
                $s = 'd.discipline_id';
            }

            if ($filterObjectId != 0) {
                $values[':object_id'] = $filterObjectId;
                if (!$whereWas) {
                    $whereWas = true;
                    $sql .= ' WHERE ' . $s . ' = :object_id';
                } else {
                    $sql .= ' AND ' . $s . ' = :object_id';
                }
            }

            // формируем напраление сортировки
            switch ($sortField) {
                case 'status': $orderBy = ' ORDER BY a.status'; break;
                case 'date_app': $orderBy = ' ORDER BY a.date_app'; break;
            }

            if ($sortField == 'fio') {
                if ($sortDirection == 'asc') $orderBy = " ORDER BY u.surname ASC, u.name ASC, u.patronymic ASC";
                else if ($sortDirection == 'desc') $orderBy = " ORDER BY u.surname DESC, u.name DESC, u.patronymic DESC";
            } else {
                if ($sortDirection == 'asc') $orderBy .= " ASC";
                else if ($sortDirection == 'desc') $orderBy .= " DESC";
            }

            // формируем условие отбора по номеру страницы
            $limit = ' LIMIT ' . self::APPS_ON_PAGE . ' OFFSET ' . intval($page) * self::APPS_ON_PAGE;

            //echo($sql); die();
            
            // и наконец - выполнение запроса получения количества записей
            $stmt = $this->prepare($selectCount . $sql);
            $stmt->execute($values);
            $appsCount = $stmt->fetchColumn();

            // и самих записей
            $stmt = $this->prepare($selectData . $sql . $orderBy . $limit);
            $stmt->execute($values);
            $apps = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);

            return array('data' => $apps, 'count' => $appsCount);
        }

        //==============================
        // Работа с историей

        /**
        * Добавление записи об истории обработки заявки. Сохраняет новый статус
        * заявки и текущую дату.
        *
        * @param  int    $app_id Идентификатор заявки.
        * @param  string $status Статус, который получила заявка.
        * @return boolean
        */
        protected function _addHistory($app_id, $status) {
            $sql = '
                INSERT INTO ' . $this->_tables['apps_history'] . '
                VALUES (:app_id, :status, NOW())
            ';

            $values = array(
                ':app_id' => $app_id,
                ':status' => $status
            );

            return $this->prepare($sql)
                        ->execute($values);
        }

        /**
        * Удаление истории обработки заявки.
        *
        * @param int $app_id Идентификатор заявки.
        * @return boolean
        */
        protected function _deleteHistory($app_id) {
            $sql = '
                DELETE
                FROM ' . $this->_tables['apps_history'] . '
                WHERE app_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($app_id));

            $affected = $stmt->rowCount();
            return ($affected > 0);
        }

        //==============================
        // Формирование данных
    
        /**
        * Добавление записи в кортеж о состояние оплаты завки: поля rest (остаток в рублях), rest_rate (остаток в длолях)
        *
        * @param array Кортеж описания  заявки: должен обязательно содержать поля 
        *     app_id, object_id, program_title/discipline_title
        *
        * @return array
        */
        public function addInfoIntoKortegAboutPaidState ($a) {
            $paym = Model_Payment::create();
            if ($a['program_title'])
            {
                //товарищ учится по всему направлению
                $prog = $this->getProgram($a['object_id']);
                if ($prog['paid_type'] == 'paid')
                {
                    $paid_money = $paym->getTotal($a['app_id']);
                    $rest = $prog['cost'] - $paid_money; // (program price - paid already)
                    $rest_rate = $rest/$prog['cost']; // how many cost's parts to pay
                    $app = array_merge($a,array('rest' => $rest, 'rest_rate' => $rest_rate));
                }else
                {
                    $app = array_merge($a,array('rest' => 'free', 'rest_rate' => 'free'));
                }
            }elseif ($a['discipline_title'])
            {
                //учится по дисциплине
                $disc = $this->getDiscipline($a['object_id']);
                $upper_prog = $this->getProgram($disc['program_id']);
                if ($upper_prog['paid_type'] == 'paid')
                {
                    $paid_money = $paym->getTotal($a['app_id']);
                    $rest = ($upper_prog['cost']*$disc['coef'])/100 - $paid_money; // (program price - paid already)
                    $rest_rate = $rest/(($upper_prog['cost']*$disc['coef'])/100); // how many cost's parts to pay
                    $app = array_merge($a,array('rest' => $rest, 'rest_rate' => $rest_rate));
                }else
                {
                    $app = array_merge($a,array('rest' => 'free', 'rest_rate' => 'free'));
                }
            }
        return $app;
        }
    }
