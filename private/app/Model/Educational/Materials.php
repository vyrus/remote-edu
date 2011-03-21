<?php
    class Model_Educational_Materials extends Model_Base {
        const MATERIAL_TYPE_LECTURE = 'lecture';
        const MATERIAL_TYPE_PRACTICE = 'practice';
        const MATERIAL_TYPE_CONTROL = 'control';

        public static $MATERIAL_TYPES_CAPTIONS = array(
            self::MATERIAL_TYPE_LECTURE => 'Лекционный материал',
            self::MATERIAL_TYPE_PRACTICE => 'Практическое занятие',
            self::MATERIAL_TYPE_CONTROL => 'Контрольный материал',
        );

        private $storage;

        public function __construct () {
            parent::__construct ();

            /**
            * @todo Oh my... This breaks up all the fun about dynamic directory
            * structure and dependency injection through Resources class.
            */
            $this->storage = new Storage ('../private/materials');
        }

        public static function create() {
            return new self();
        }

        public function addMaterial ($description, $section, $type, $originalFileInfo) {
            $filename = $this->storage->storeFile($originalFileInfo['tmp_name']);
            $sql = 'INSERT INTO ' . $this->_tables['materials'] . ' (`description`, `original_filename`, `mime_type`, `filename`, `section`, `type`, `uploader`) VALUES (:description, :original_filename, :mime_type, :filename, :section, :type, :uploader)';
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $params = array (
                ':description' => $description,
                ':original_filename' => $originalFileInfo['name'],
                ':mime_type' => $originalFileInfo['type'],
                ':filename' => $filename,
                ':section' => $section,
                ':type' => $type,
                ':uploader' => $udata->user_id
            );

            $this->prepare ($sql)
                ->execute ($params);
        }

        public function removeMaterial ($materialID) {
            //var_dump($materialID);
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $sql = 'SELECT `filename` FROM `materials` WHERE `id`=:material_id' . ($udata->role != Model_User::ROLE_ADMIN ? ' AND `uploader`=:uploader_id' : '');
            $stmt = $this->prepare($sql);
            $params = array(
                ':material_id' => $materialID,
            );

            if ($udata->role != Model_User::ROLE_ADMIN) {
                $params[':uploader_id'] = $udata->user_id;
            }

            $stmt->execute($params);

            if (($filename = $stmt->fetch(PDO::FETCH_ASSOC)) === FALSE) {
                return FALSE;
            }

            $this->storage->removeFile($filename['filename']);

            $sql = 'DELETE FROM `materials` WHERE `id`=?';
            $this->prepare($sql)
                ->execute(array($materialID));

            return TRUE;
        }
        
        public function editMaterialNumber($ID, $number) {
            $sql = "UPDATE `materials` SET `number`=:number WHERE `id`=:id";

            $params = array(
                ':number'   => $number,
                ':id'       => $ID
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }

        // без 100 грамм не разберешься
        public function getMaterials ($filter) {
            $sql = 'SELECT DISTINCT `materials`.`id` as `id`,`materials`.`description` as `description`,`materials`.`type` AS `type`,
                `materials`.`section` AS `section` FROM `materials`';
            /*$sql = 'SELECT `materials`.`id` as `id`,`materials`.`description` as `description`,`materials`.`type` AS `type`,
                `materials`.`section` AS `section` FROM `materials` ORDER BY `materials`.`number`';*/
                
            $user = Model_User::create();
            $udata = (object) $user->getAuth();

            $queryParams = array();
            $condition = '';
            $tables = '';

            $isAdminUser = $udata->role == Model_User::ROLE_ADMIN;

            if (!$isAdminUser) {
                $tables = ',`disciplines`,`sections`';
                
                $queryParams = array(
                    ':permitted_user' => $udata->user_id,
                );
                $condition = ' WHERE (materials.uploader=:permitted_user OR materials.section=sections.section_id AND sections.discipline_id=disciplines.discipline_id AND disciplines.responsible_teacher=:permitted_user)';
            }

            do {
                if ((empty ($filter)) || ($filter['programsSelect'] == -1)) {
                    $tables = $isAdminUser ? '' : $tables;
                    break;
                }

                if ($filter['disciplinesSelect'] == -1) {
                    $tables = $isAdminUser ? ',`disciplines`,`sections`' : $tables;
                    $condition .= ($condition == '' ? ' WHERE ' : ' AND ') . '`materials`.`section`=`sections`.`section_id` AND `sections`.`discipline_id`=`disciplines`.`discipline_id` AND `disciplines`.`program_id`=:program_id';
                    $queryParams = array_merge($queryParams, array(
                            ':program_id' => $filter['programsSelect'],
                        )
                    );
                    break;
                }

                if ($filter['sectionsSelect'] == -1) {
                    $tables = $isAdminUser ? ',`sections`' : $tables;
                    $condition .= ($condition == '' ? ' WHERE ' : ' AND ') . '`materials`.`section`=`sections`.`section_id` AND `sections`.`discipline_id`=:discipline_id';
                    $queryParams = array_merge($queryParams, array(
                            ':discipline_id' => $filter['disciplinesSelect'],
                        )
                    );
                    break;
                }

                $tables = $isAdminUser ? '' : $tables;
                $condition .= ($condition == '' ? ' WHERE ' : ' AND ') . '`materials`.`section`=:section_id';
                $queryParams = array_merge($queryParams, array(
                        ':section_id' => $filter['sectionsSelect'],
                    )
                );
            } while(0);

            $order = ' ORDER BY `materials`.`number`'; //!
            
            $sql .= $tables . $condition . $order;

            $stmt = $this->prepare ($sql);
            $stmt->execute ($queryParams);
            
            $a = ($stmt->fetchAll (PDO::FETCH_ASSOC));
            //var_dump($a);

            return $a;
        }
        
        public function getMaterialsByAdmin() {
            $arNoFilter = array (
                'programsSelect' => -1,
                'disciplinesSelect' => -1,
                'sectionsSelect' => -1
            );
            $matPrim = $this->getMaterials($arNoFilter);
            $matAll = array();
            
            //var_dump($matPrim);
            
            foreach ($matPrim as $rec) {
                $matAll[$rec['section']] = array();
            }
            
            foreach ($matPrim as $rec) {
                $len =count($matAll[$rec['section']]);
                $matAll[$rec['section']][$len]['id'] = $rec['id'];
                $matAll[$rec['section']][$len]['description'] = $rec['description'];
                $matAll[$rec['section']][$len]['type'] = $rec['type'];                
            }
            return $matAll;
        }

        public function getMaterialInfo($materialId) {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $sql = 'SELECT `description`,`type` FROM `materials` WHERE `id`=:material_id' . ($udata->role != Model_User::ROLE_ADMIN ? ' AND `uploader`=:uploader_id' : '');
            $params = array(
                ':material_id' => $materialId,
            );

            if ($udata->role != Model_User::ROLE_ADMIN) {
                $params[':uploader_id'] = $udata->user_id;
            }

            $stmt = $this->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        public function updateMaterialInfo($materialInfo) {
            $user = Model_User::create();
            $udata = (object) $user->getAuth();
            $sql = 'UPDATE `materials` SET `description`=:description,`type`=:type WHERE `id`=:material_id' . ($udata->role != Model_User::ROLE_ADMIN ? ' AND `uploader`=:uploader_id' : '');
            $params = array(
                ':material_id' => $materialInfo['id'],
                ':description' => $materialInfo['description'],
                ':type' => $materialInfo['type'],
            );

            if ($udata->role != Model_User::ROLE_ADMIN) {
                $params[':uploader_id'] = $udata->user_id;
            }

            $this->prepare($sql)->execute($params);
        }

        public function getMaterial ($material_id) {
            $sql = '
                SELECT  `original_filename`, `mime_type`, `filename`
                FROM    ' . $this->_tables['materials'] . '
                WHERE   `id` = ?
            ';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($material_id));
            $fileInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = '
                SELECT  `state`
                FROM    ' . $this->_tables['materials_states'] . '
                WHERE   material_id = :material_id AND
                        student_id = :student_id
            ';
            $udata = (object) Model_User::create()->getAuth();
            $params = array(
                ':material_id' => $material_id,
                ':student_id' => $udata->user_id
            );
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $material_state = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($material_state[0]['state'])) {
                $sql = '
                    UPDATE  ' . $this->_tables['materials_states'] . '
                    SET     `state` = :new_state
                    WHERE   `student_id` = :student_id AND
                            `state` = :old_state
                ';
                $params = array(
                    ':student_id' => $udata->user_id,
                    ':old_state' => 'last',
                    ':new_state' => 'downloaded'
                );
                $this->prepare($sql)
                    ->execute($params);

                $sql = '
                    INSERT INTO ' . $this->_tables['materials_states'] . '
                    (`student_id`, `material_id`, `state`)
                    VALUES (:student_id, :material_id, :state)
                ';
                $params = array(
                    ':student_id' => $udata->user_id,
                    ':material_id' => $material_id,
                    ':state' => 'last'
                );
                $this->prepare($sql)
                    ->execute($params);
            }

            header('Content-Disposition: attachment; filename="' . $fileInfo[0]['original_filename']) . '"';
            header('Content-Type: ' . $fileInfo[0]['mime_type']);

            echo $this->storage->getFileContent($fileInfo[0]['filename']);
        }

        /**
        * Получение всех материалов по идентификаторам разделов.
        *
        * @param  array $section_ids Список идентификаторов разделов.
        * @return array Список вида array($section_id => array($material, ...)).
        */
        public function getAllBySections(array $section_ids) {
            $sql = '
                SELECT  *
                FROM    ' . $this->_tables['materials'] . ' m
                LEFT JOIN ' . $this->_tables['materials_states'] . ' ms
                ON      m.id = ms.material_id AND
                        ms.student_id = :student_id
                WHERE   m.section = :section_id
            ';

            $materials = array();
            $udata = (object) Model_User::create()->getAuth();

            foreach ($section_ids as $id) {
                    $params = array(
                        ':section_id' => $id,
                        ':student_id' => $udata->user_id
                    );
                    $stmt = $this->prepare($sql);
                    $stmt->execute($params);
                    $materials[$id] = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            }

            return $materials;
        }

        /**
        * Получение списка доступных материалов по идентификатору дисциплины.
        *
        * @param  int $discipline_id Идентификатор дисциплины.
        * @return array
        */
        public function getAllByDiscipline($discipline_id) {
            $sql = '
                SELECT DISTINCT m.section, s.number, m.id, m.description, ms.state, m.type
                FROM ' . $this->_tables['sections'] . ' s
                LEFT JOIN ' . $this->_tables['checkpoints_students'] . ' cs
                    ON s.section_id = cs.section_id
                LEFT JOIN ' . $this->_tables['materials'] . ' m
                    ON s.section_id = m.section
                LEFT JOIN ' . $this->_tables['materials_states'] . ' ms
                    ON m.id = ms.material_id AND ms.student_id = :student_id
                WHERE
                    s.discipline_id = :discipline_id AND
                    cs.student_id = :student_id
                ORDER BY number ASC
            ';

            $udata = Model_User::create()->getAuth();
            if ($udata) {
                $params = array(
                    ':discipline_id' => $discipline_id,
                    ':student_id' => $udata['user_id']
                );
                $stmt = $this->prepare($sql);
                $stmt->execute($params);

                $materials = $stmt->fetchAll(Db_Pdo::FETCH_GROUP|Db_Pdo::FETCH_ASSOC);
                return $materials;
            } else {
                return false;
            }
        }
    
        
    }
    
?>