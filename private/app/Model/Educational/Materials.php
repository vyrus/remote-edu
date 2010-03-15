<?php
    class Model_Educational_Materials extends Model_Base {
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

        public function addMaterial ($description, $section, $originalFileInfo) {
            $filename = $this->storage->storeFile ($originalFileInfo['tmp_name']);

            $sql =
<<<QUERY
INSERT INTO `materials`(`description`,`original_filename`,`mime_type`,`filename`,`section`)
VALUES (:description,:original_filename,:mime_type,:filename,:section)
QUERY;
            $params = array (
                'description'		=> $description,
                'original_filename'	=> $originalFileInfo['name'],
                'mime_type'			=> $originalFileInfo['type'],
                'filename'			=> $filename,
                'section'			=> $section,
            );

            $this->prepare ($sql)
                ->execute ($params);
        }

        public function removeMaterial ($materialID) {
            $sql =
<<<QUERY
SELECT `filename`
FROM `materials`
WHERE `id`=?
QUERY;
            $stmt = $this->prepare ($sql);
            $stmt->execute (array ($materialID));
            $filename = $stmt->fetchAll (PDO::FETCH_ASSOC);

            $this->storage->removeFile ($filename[0]['filename']);

            $sql =
<<<QUERY
DELETE FROM `materials`
WHERE `id`=?
QUERY;
            $this->prepare ($sql)
                ->execute (array ($materialID));
        }

        public function getMaterials ($filter) {
            $sql =
<<<QUERY
SELECT
    `materials`.`id` as `id`,`materials`.`description` as `description`
FROM
    `materials`
QUERY;

            do {
                if ((empty ($filter)) || ($filter['programsSelect'] == -1)) {
                    $tables			= '';
                    $condition		= '';
                    $queryParams	= array ();

                    break;
                }

                if ($filter['disciplinesSelect'] == -1) {
                    $tables 		= ',`disciplines`,`sections`';
                    $condition		= '`materials`.`section`=`sections`.`section_id` AND `sections`.`discipline_id`=`disciplines`.`discipline_id` AND `disciplines`.`program_id`=?';
                    $queryParams	= array ($filter['programsSelect']);

                    break;
                }

                if ($filter['sectionsSelect'] == -1) {
                    $tables 		= ',`sections`';
                    $condition		= '`materials`.`section`=`sections`.`section_id` AND `sections`.`discipline_id`=?';
                    $queryParams	= array ($filter['disciplinesSelect']);

                    break;
                }

                $tables 		= '';
                $condition		= '`materials`.`section`=?';
                $queryParams	= array ($filter['sectionsSelect']);
            } while (0);

            $sql .= $tables . (($condition != '') ? (' WHERE ' . $condition) : (''));

            $stmt = $this->prepare ($sql);
            $stmt->execute ($queryParams);

            return $stmt->fetchAll (PDO::FETCH_ASSOC);
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
                $stmt = $this->prepare($sql);
                $stmt->execute($params);

                $sql = '
                    UPDATE  ' . $this->_tables['materials_states'] . '
                    SET     `state` = :new_state
                    WHERE   material_id = :material_id AND
                            student_id = :student_id
                ';
                $params = array(
                    ':material_id' => $material_id,
                    ':student_id' => $udata->user_id,
                    ':new_state' => 'last'
                );
                $stmt = $this->prepare($sql);
                $stmt->execute($params);
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
                FROM    ' . $this->_tables['materials'] . ' m, 
                        ' . $this->_tables['materials_states'] . ' ms
                WHERE   m.section = :section_id AND
                        ms.student_id = :student_id AND
                        ms.material_id = m.id
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
    }
?>