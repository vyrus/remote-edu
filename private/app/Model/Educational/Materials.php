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

        public function getMaterial ($materialID) {
            $sql =
<<<QUERY
SELECT
    `original_filename`,`mime_type`,`filename`
FROM
    `materials`
WHERE
    `id`=?
QUERY;

            $stmt = $this->prepare ($sql);
            $stmt->execute (array ($materialID));
            $fileInfo = $stmt->fetchAll (PDO::FETCH_ASSOC);

            header('Content-Disposition: attachment; filename="' . $fileInfo[0]['original_filename']);
            header('Content-Type: ' . $fileInfo[0]['mime_type']);

            echo $this->storage->getFileContent ($fileInfo[0]['filename']);
        }

        /**
        * Получение всех материалов по идентификатором разделов.
        *
        * @param  array $section_ids Список идентификаторов разделов.
        * @return array Список вида array($section_id => array($material, ...)).
        */
        public function getAllBySections(array $section_ids) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['materials'] . '
                WHERE section = ?';

            $stmt = $this->prepare($sql);

            $sql2 = '
                SELECT `state`
                FROM ' . $this->_tables['materials_states'] . '
                WHERE `student_id`=:student_id AND `material_id`=:material_id';

            $materials = array();

            foreach ($section_ids as $id) {
                $stmt->execute(array($id));
                $material = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);

                $udata = (object) Model_User::create()->getAuth();
                $i = 0;
                foreach ($material as $material_data) {
                    $params = array(
                        ':material_id' => $material_data['id'],
                        ':student_id' => $udata->user_id
                    );
                    $stmt2 = $this->prepare($sql2);
                    $stmt2->execute($params);
                    $material_state = $stmt2->fetchAll(Db_Pdo::FETCH_ASSOC);

                    $materials[$id][] = $material[$i] + $material_state[$i];
                    $i++;
                }
            }

            return $materials;
        }
    }
?>