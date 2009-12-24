<?php
    
    /* $Id$ */

    class Model_Application extends Model_Base {
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
        * Карта соответствия обозначений статусов заявок названиям статусов
        * заявок.
        * 
        * @var array
        */
        protected static $_status_map = array(
            self::STATUS_APPLIED  => 'подана',
            self::STATUS_DECLINED => 'отклонена',
            self::STATUS_ACCEPTED => 'принята',
            self::STATUS_SIGNED   => 'подписана'
        );
        
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
        
        /**
        * Подача заявки.
        * 
        * @param  int $user_id   Идентификатор пользователя.
        * @param  int $object_id Идентификатор объекта.
        * @param  int $type      Тип объекта (программа/дисциплины).
        * @return void
        */
        public function apply($user_id, $object_id, $type) {
            /**
            * @todo Проверять на дубликаты заявок.
            */
            
            $sql = '
                INSERT INTO ' . $this->_tables['applications'] . '
                (user_id, object_id, type, status)
                VALUES (:uid, :oid, :type, :status)
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
                SELECT a.app_id, a.status,
                       contract_filename,      
                       p.title AS program_title,
                       d.title AS discipline_title
                FROM ' . $this->_tables['applications'] . ' a
                
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                ON a.type = :type_program AND
                   p.program_id = a.object_id
                
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                ON a.type = :type_discipline AND
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
        * Получение обработанных заявок на программы, т.е. заявок, которые были
        * приняты админом, либо которые были приняты админом и по которым
        * подписаны договоры.
        * 
        * @param  int $user_id Идентификатор пользователя.
        * @return array
        */
        public function getProcessedAppsForPrograms($user_id) {
            $sql = '
                SELECT a.app_id, a.object_id, a.status, p.paid_type
                FROM ' . $this->_tables['applications'] . ' a
                
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                ON p.program_id = a.object_id
                
                WHERE a.type = :program AND
                      a.status IN (:accepted, :signed) AND
                      a.user_id = :uid
            ';
            
            $values = array(
                ':uid'      => $user_id,
                ':program'  => Model_Application::TYPE_PROGRAM,
                ':accepted' => self::STATUS_ACCEPTED,
                ':signed'   => self::STATUS_SIGNED
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            
            $apps = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $apps;
        }
        
        /**
        * Получение обработанных заявок на дисциплины, т.е. заявок, которые были
        * приняты админом, либо которые были приняты админом и по которым
        * подписаны договоры.
        * 
        * @param  int $user_id Идентификатор пользователя.
        * @return array
        */
        public function getProcessedAppsForDisciplines($user_id) {
            $sql = '
                SELECT a.app_id, a.object_id, a.status, p.program_id, 
                       p.paid_type, d.coef
                FROM ' . $this->_tables['applications'] . ' a
                
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                ON d.discipline_id = a.object_id
                
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                ON p.program_id = d.program_id
                
                WHERE a.type = :discipline AND
                      a.status IN (:accepted, :signed) AND
                      a.user_id = :uid
            ';
            
            $values = array(
                ':uid'        => $user_id,
                ':discipline' => Model_Application::TYPE_DISCIPLINE,
                ':accepted'   => self::STATUS_ACCEPTED,
                ':signed'     => self::STATUS_SIGNED
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            
            $apps = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $apps;
        }
        
        /**
        * Получение списка всех поданных заявок с текущими статусами и названиями
        * программ/дисциплин.
        * 
        * @return array
        */
        public function getAllAppsInfo()
        {
            $sql = '
                SELECT a.app_id, a.status, u.name, u.surname, u.patronymic, u.login,
                        contract_filename,
                       p.title AS program_title,
                       d.title AS discipline_title
                FROM ' . $this->_tables['applications'] . ' a
                
                LEFT JOIN ' . $this->_tables['programs'] . ' p
                ON a.type = :type_program AND
                   p.program_id = a.object_id
                
                LEFT JOIN ' . $this->_tables['disciplines'] . ' d
                ON a.type = :type_discipline AND
                   d.discipline_id = a.object_id
                
                LEFT JOIN ' . $this->_tables['users'] . ' u
                ON u.user_id = a.user_id 
            ';
            $values = array(
                ':type_program'    => self::TYPE_PROGRAM,
                ':type_discipline' => self::TYPE_DISCIPLINE
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            
            $apps = $stmt->fetchAll(Db_PdO::FETCH_ASSOC);
            return $apps;
        }


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
        
        /**
        * Изменение статуса заявки на переданный.
        * 
        * @return array
        */
        public function setAppStatus($new_status,$app_id)
        {
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
    }
?>