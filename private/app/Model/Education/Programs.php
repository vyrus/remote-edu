<?php

    class Model_Education_Programs extends Model_Base {

        const CHECK_BY_PARENT_ID = 0;
        const CHECK_BY_OWN_ID = 1;

        /**
        * Тип программы: бесплатная.
        *
        * @var const
        */
        const PAID_TYPE_FREE = 'free';

        /**
        * Тип программы: платная.
        *
        * @var const
        */
        const PAID_TYPE_PAID = 'paid';

        private $_cache = array();

        public static function create() {
            return new self();
        }

        public function programIDExists($id, $type) {
            $sql =
<<<QUERY
SELECT `program_id`
FROM `programs`
WHERE
    `program_id`=? AND
    `edu_type`=?
QUERY;

            $stmt = $this->prepare($sql);
            $stmt->execute(array($id, $type));

            return($stmt->fetch() !== FALSE);
        }

        public function programExists($title, $type) {
            $sql =
<<<QUERY
SELECT `program_id`
FROM `programs`
WHERE
    `title`=? AND
    `edu_type`=?
QUERY;

            $stmt = $this->prepare($sql);
            $stmt->execute(array($title, $type));

            return($stmt->fetch() !== FALSE);
        }

        public function disciplineIDExists($id) {
            $sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE `discipline_id`=?
QUERY;
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));

            return($stmt->fetch() !== FALSE);
        }

        public function disciplineExists($id, $title, $checkType = Model_Education_Programs::CHECK_BY_PARENT_ID) {
            if($checkType == Model_Education_Programs::CHECK_BY_OWN_ID) {
                $sql =
<<<QUERY
SELECT `program_id`
FROM `disciplines`
WHERE `discipline_id`=?
QUERY;
                $getProgramIDStmt = $this->prepare($sql);
                $getProgramIDStmt->bindColumn('program_id', $id);
                $getProgramIDStmt->execute(array($id));
                $getProgramIDStmt->fetch(PDO::FETCH_BOUND);
            }

            $sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE
    `program_id`=:program_id AND
    `title`		=:title
QUERY;
            $params = array(
                ':program_id'	=> $id,
                ':title'		=> $title,
            );

            $disciplineExistsStmt = $this->prepare($sql);
            $disciplineExistsStmt->execute($params);

            return($disciplineExistsStmt->fetch() !== FALSE);
        }

        public function sectionExists($id, $title, $checkType = Model_Education_Programs::CHECK_BY_PARENT_ID) {
            if($checkType == Model_Education_Programs::CHECK_BY_OWN_ID) {
                $sql =
<<<QUERY
SELECT `discipline_id`
FROM `sections`
WHERE `section_id`=?
QUERY;
                $getDisciplineIDStmt = $this->prepare($sql);
                $getDisciplineIDStmt->bindColumn('discipline_id', $id);
                $getDisciplineIDStmt->execute(array($id));
                $getDisciplineIDStmt->fetch(PDO::FETCH_BOUND);
            }

            $sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE
    `discipline_id`=:discipline_id AND
    `title`=:title
QUERY;
            $params = array(
                ':discipline_id'=> $id,
                ':title'		=> $title,
            );

            $sectionExistsStmt = $this->prepare($sql);
            $sectionExistsStmt->execute($params);

            return($sectionExistsStmt->fetch() !== FALSE);
        }

        public function sectionIDExists($id) {
            $sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE `section_id`=?
QUERY;
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));

            return($stmt->fetch() !== FALSE);
        }

        public function sectionNumberExists($id, $number, $checkType = Model_Education_Programs::CHECK_BY_PARENT_ID) {
            if($checkType == Model_Education_Programs::CHECK_BY_OWN_ID) {
                $sql =
<<<QUERY
SELECT `discipline_id`
FROM `sections`
WHERE `section_id`=?
QUERY;
                $getDisciplineIDStmt = $this->prepare($sql);
                $getDisciplineIDStmt->bindColumn('discipline_id', $id);
                $getDisciplineIDStmt->execute(array($id));
                $getDisciplineIDStmt->fetch(PDO::FETCH_BOUND);
            }

            $sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE
    `discipline_id`=:discipline_id AND
    `number`=:number
QUERY;
            $params = array(
                ':discipline_id'=> $id,
                ':number'		=> $number,
            );

            $sectionExistsStmt = $this->prepare($sql);
            $sectionExistsStmt->execute($params);

            return($sectionExistsStmt->fetch() !== FALSE);
        }

        public function createProgram($title, $labourIntensive, $type, $paidType, $cost) {
            $sql =
<<<QUERY
INSERT INTO `programs`(`title`,`labour_intensive`,`edu_type`,`paid_type`,`cost`)
VALUES(:title,:labour_intensive,:edu_type,:paid_type,:cost)
QUERY;
            $params = array(
                ':title'			=> $title,
                ':labour_intensive'	=> $labourIntensive,
                ':edu_type'			=> $type,
                ':paid_type'		=> $paidType,
                ':cost'				=>($cost) ?($cost) :(NULL),
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }

        public function createDiscipline($programID, $title, $coef, $labourIntensive, $serial_number) {
            $sql =
<<<QUERY
INSERT INTO `disciplines`(`program_id`,`serial_number`,`title`,`coef`,`labour_intensive`)
VALUES(:program_id,:serial_number,:title,:coef,:labour_intensive)
QUERY;

            $params = array(
                ':program_id'		=> $programID,
                ':serial_number'	=> $serial_number,
                ':title'			=> $title,
                ':coef'				=> $coef,
                ':labour_intensive'	=> $labourIntensive,
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }

        public function createSection($disciplineID, $title, $number) {
            $sql =
<<<QUERY
INSERT INTO `sections`(`discipline_id`,`title`,`number`)
VALUES(:discipline_id,:title,:number)
QUERY;

            $params = array(
                ':discipline_id'	=> $disciplineID,
                ':title'			=> $title,
                ':number'			=> $number
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }

        public function getDirections() {
            if(! isset($this->_cache['directions'])) {
                $sql =
<<<QUERY
SELECT *
FROM `programs`
WHERE `edu_type`='direction'
ORDER BY `number`
QUERY;

                $this->_cache['directions'] = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            }

            return $this->_cache['directions'];
        }

        public function getCourses() {
            $sql =
<<<QUERY
SELECT *
FROM `programs`
WHERE `edu_type`='course'
ORDER BY `number`
QUERY;

            return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getDisciplines() {
            if(! isset($this->_cache['disciplines'])) {
                $sql =
<<<QUERY
SELECT *
FROM `disciplines`
ORDER BY `program_id`,`serial_number`
QUERY;
            $this->_cache['disciplines'] = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            }

            return $this->_cache['disciplines'];
        }

        public function getDirectionsDisciplines() {
            if(! isset($this->_cache['directionsDisciplines'])) {
                $disciplines = $this->getDisciplines();
                $result = array();
                if(count($disciplines)) {
                    $programID = $disciplines[0]['program_id'];
                    foreach($disciplines as $i => $discipline) {
                        $result[$programID][] = $discipline;
                        if(
                           ($i + 1 < count($disciplines)) &&
                           ($disciplines[$i + 1]['program_id'] != $programID)
                        ) {
                            $programID = $disciplines[$i + 1]['program_id'];
                        }
                    }
                }

                $this->_cache['directionsDisciplines'] = $result;
            }

            return $this->_cache['directionsDisciplines'];
        }

        public function getSections() {

            if(! isset($this->_cache['sections'])) {
                $sql =
<<<QUERY
SELECT *
FROM `sections`
ORDER BY `number`, `section_id`, `discipline_id`
QUERY;
                $this->_cache['sections'] = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            }

            return $this->_cache['sections'];
        }

        public function getDisciplinesSections() {
            if(! isset($this->_cache ['disciplinesSections'])) {
                $sections = $this->getSections();
                $result = array();
                if(count($sections)) {
                    $disciplineID = $sections[0]['discipline_id'];
                    foreach($sections as $i => $section) {
                        $result[$disciplineID][] = $section;
                        if(
                           ($i + 1 < count($sections)) &&
                           ($sections[$i + 1]['discipline_id'] != $disciplineID)
                        ) {
                            $disciplineID = $sections[$i + 1]['discipline_id'];
                        }
                    }
                }

                $this->_cache['disciplinesSections'] = $result;
            }

            return $this->_cache['disciplinesSections'];
        }

        public function removeProgram($programID) {
            $this->removeDisciplines($programID);

            $sql =
<<<QUERY
DELETE FROM `programs`
WHERE `program_id`=?
QUERY;

            $this->prepare($sql)->execute(array($programID));
        }

        private function removeDisciplines($programID) {
            $sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE `program_id`=?
QUERY;

            $disciplinesStmt = $this->prepare($sql);
            $disciplinesStmt->execute(array($programID));
            $disciplines = $disciplinesStmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($disciplines)) {
                foreach($disciplines as $i => $discipline) {
                    $this->removeSections($discipline['discipline_id']);
                }

                $sql =
<<<QUERY
DELETE FROM `disciplines`
WHERE `program_id`=?
QUERY;
                $this->prepare($sql)->execute(array($programID));
            }
        }

        public function removeDiscipline($disciplineID) {
            $this->removeSections($disciplineID);

            $sql =
<<<QUERY
DELETE FROM `disciplines`
WHERE `discipline_id`=?
QUERY;
            $this->prepare($sql)->execute(array($disciplineID));
        }

        private function removeSections($disciplineID) {
            $sql =
<<<QUERY
DELETE FROM `sections`
WHERE `discipline_id`=?
QUERY;

            $this->prepare($sql)->execute(array($disciplineID));
        }

        public function removeSection($sectionID) {
            $sql =
<<<QUERY
DELETE FROM `sections`
WHERE `section_id`=?
QUERY;

            $this->prepare($sql)->execute(array($sectionID));
        }

        public function getProgram($programID, $type, &$title, &$labourIntensive, &$paidType, &$cost) {
            $sql =
<<<QUERY
SELECT `title`,`labour_intensive`,`paid_type`,`cost`
FROM `programs`
WHERE
    `program_id`=? AND
    `edu_type`=?
QUERY;
            $getProgramStmt = $this->prepare($sql);
            $getProgramStmt->bindColumn('title', $title);
            $getProgramStmt->bindColumn('labour_intensive', $labourIntensive);
            $getProgramStmt->bindColumn('paid_type', $paidType);
            $getProgramStmt->bindColumn('cost', $cost);
            $getProgramStmt->execute(array($programID, $type));

            $getProgramStmt->fetch(PDO::FETCH_BOUND);
        }

        public function editProgram($programID, $type, $title, $labourIntensive, $paidType, $cost) {
            $sql =
<<<QUERY
UPDATE `programs`
SET
    `title`=:title,
    `labour_intensive`=:labour_intensive,
    `paid_type`=:paid_type,
    `cost`=:cost
WHERE
    `program_id`=:program_id AND
    `edu_type`=:program_type
QUERY;

            $params = array(
                ':title'		=> $title,
                ':labour_intensive'	=> $labourIntensive,
                ':paid_type'		=> $paidType,
                ':program_id'		=> $programID,
                ':program_type'		=> $type,
                ':cost'			=>($cost) ?($cost) :(NULL),
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }
        
        public function editProgramNumber($programID, $number) {
            $sql =
<<<QUERY
UPDATE `programs`
SET
    `number`=:number
WHERE
    `program_id`=:program_id
QUERY;

            $params = array(
                ':number'    => $number,
                ':program_id'    => $programID
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }
        
        public function getDiscipline($disciplineID, &$title, &$labourIntensive, &$coef) {
            $sql =
<<<QUERY
SELECT `title`,`labour_intensive`,`coef`
FROM `disciplines`
WHERE
    `discipline_id`=?
QUERY;
            $getDisciplineStmt = $this->prepare($sql);
            $getDisciplineStmt->bindColumn 	('title', 				$title);
            $getDisciplineStmt->bindColumn 	('labour_intensive', 	$labourIntensive);
            $getDisciplineStmt->bindColumn 	('coef', 				$coef);
            $getDisciplineStmt->execute 	(array($disciplineID));

            $getDisciplineStmt->fetch(PDO::FETCH_BOUND);
        }

        public function editDiscipline($disciplineID, $title, $labourIntensive, $coef) {
            $sql =
<<<QUERY
UPDATE `disciplines`
SET
    `title`=:title,
    `labour_intensive`=:labour_intensive,
    `coef`=:coef
WHERE
    `discipline_id`=:discipline_id
QUERY;

            $params = array(
                ':title'			=> $title,
                ':labour_intensive'	=> $labourIntensive,
                ':coef'				=> $coef,
                ':discipline_id'	=> $disciplineID
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }

        public function editDisciplineSerialNumber($disciplineID, $serial_number) {
            $sql =
<<<QUERY
UPDATE `disciplines`
SET
    `serial_number`=:serial_number
WHERE
    `discipline_id`=:discipline_id
QUERY;

            $params = array(
                ':serial_number'    => $serial_number,
                ':discipline_id'    => $disciplineID
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }
        
        public function getSection($sectionID, &$title, &$number) {
            $sql =
<<<QUERY
SELECT `title`,`number`
FROM `sections`
WHERE `section_id`=?
QUERY;
            $getSectionStmt = $this->prepare($sql);
            $getSectionStmt->bindColumn('title', 	$title);
            $getSectionStmt->bindColumn('number', 	$number);
            $getSectionStmt->execute 	(array($sectionID));

            $getSectionStmt->fetch(PDO::FETCH_BOUND);
        }

/*
        public function editSection($sectionID, $title, $number) {
            $sql =
<<<QUERY
UPDATE `sections`
SET
    `title`=:title,
    `number`=:number
WHERE
    `section_id`=:section_id
QUERY;

            $params = array(
                ':title'		=> $title,
                ':number'		=> $number,
                ':section_id'	=> $sectionID,
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }
*/
        
        public function editSection($sectionID, $title) {
            $sql =
<<<QUERY
UPDATE `sections`
SET
    `title`=:title
WHERE
    `section_id`=:section_id
QUERY;

            $params = array(
                ':title'	=> $title,
                ':section_id'	=> $sectionID
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }
        
        public function editSectionNumber($sectionID, $number) {
            $sql =
<<<QUERY
UPDATE `sections`
SET
    `number`=:number
WHERE
    `section_id`=:section_id
QUERY;

            $params = array(
                ':number'       => $number,
                ':section_id'   => $sectionID
            );

            $this
                ->prepare($sql)
                ->execute($params);
        }

        /**
        * Возвращает список всех дисциплин, входящих в образовательную
        * программу.
        *
        * @param  int $program_id Идентификатор программы.
        * @return array
        */
        public function getDisciplinesByProgramId($program_id) {
            $sql = '
                SELECT discipline_id, title, coef
                FROM ' . $this->_tables['disciplines'] . '
                WHERE program_id = ?
            ';

            $stmt = $this->prepare($sql);
            $stmt->execute(array($program_id));

            $discs = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            return $discs;
        }

        /**
        * Получение всей информации об образовательной программе.
        *
        * @param  int $program_id Идентификатор программы.
        * @return array|false
        */
        public function getProgramInfo($program_id) {
            $sql = '
                SELECT *
                FROM ' . $this->_tables['programs'] . '
                WHERE program_id = ?
            ';
            /*$sql = '
                SELECT pr.*, SUM(pm.amount) AS total_sum
                FROM ' . $this->_tables['programs'] . ' pr
                LEFT JOIN ' . $this->_tables['payments'] . ' pm
                     ON pm.app_id = pr.program_id
                
                WHERE program_id = :program_id
            ';*/

            $stmt = $this->prepare($sql);
            $stmt->execute(array($program_id));

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Возвращает список всех дисциплин в совокупности с ответсвенными
        * за них преподавателями
        *
        * @return array
        */
        public function getDisciplinesResponsibleTeachersList() {
            $sql = 'SELECT `programs`.`title`,
                `disciplines`.`discipline_id`,
                `disciplines`.`title`,
                `disciplines`.`responsible_teacher`
            FROM `programs`,`disciplines`
            WHERE `disciplines`.`program_id`=`programs`.`program_id`';
            $stmt = $this->prepare($sql);
            $stmt->execute(array());
            $retval = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);

            return $retval;
        }

        /**
        * Возвращает список всех курсов в совокупности с ответсвенными
        * за них преподавателями
        *
        * @return array
        */
        public function getCoursesResponsibleTeachersList() {
            $sql = 'SELECT `title`,
                `program_id`,
                `responsible_teacher`
            FROM `programs`
            WHERE `edu_type`=\'course\'';

            $stmt = $this->prepare($sql);
            $stmt->execute(array());
            $retval = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);

            return $retval;
        }

        /**
        * Возвращает список дисциплин за которые ответсвенным
        * назначен указанный преподаватель
        *
        * @param  int $responsible_teacher Идентификатор преподавателя.
        * @return array
        */
        public function getDisciplinesByResponsibleTeacher($responsible_teacher) {
            $sql = 'SELECT d.discipline_id, d.title
            FROM ' . $this->_tables['disciplines'] . ' d
            LEFT JOIN ' . $this->_tables['programs'] . ' p
            ON p.program_id = d.program_id
            WHERE(d.responsible_teacher = :responsible_teacher) OR(p.responsible_teacher = :responsible_teacher)';
            $sql_params = array(
                ':responsible_teacher' => $responsible_teacher
            );
            $stmt = $this->prepare($sql);
            $stmt->execute($sql_params);

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Возвращает список курсов за которые ответсвенным
        * назначен указанный преподаватель
        *
        * @param  int $teacherId Идентификатор преподавателя.
        * @return array
        */
        public function getCoursesByResponsibleTeacher($teacher_id) {
            $sql = 'SELECT `program_id`, `title`
            FROM ' . $this->_tables['programs'] . '
            WHERE `edu_type` = \'course\' AND `responsible_teacher` = ?';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($teacher_id));
            $retval = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);

            return $retval;
        }

        /**
        * Назначает ответвенного за курсы.
        *
        * @param  int $courcesId Идентификатор курсов.
        * @param  int $teacherId Идентификатор преподавателя.
        * @return void
        */
        public function setCoursesResponsibleTeacher($courcesId, $teacherId) {
            $sql = 'UPDATE `programs` SET `responsible_teacher`=:responsible_teacher
                WHERE `program_id`=:courcesId AND `edu_type`=\'course\'';
            $params = array(
                ':responsible_teacher' => $teacherId,
                ':courcesId' => $courcesId,
            );
            $this->prepare($sql)->execute($params);
        }

        /**
        * Назначает ответсвенного за дисциплину.
        *
        * @param  int $disciplineId Идентификатор дисциплины.
        * @param  int $teacherId Идентификатор преподавателя.
        * @return void
        */
        public function setDisciplineResponsibleTeacher($disciplineId, $teacherId) {
            $sql = 'UPDATE `disciplines` SET `responsible_teacher`=:responsible_teacher
                WHERE `discipline_id`=:discipline_id';
            $params = array(
                ':responsible_teacher' => $teacherId,
                ':discipline_id' => $disciplineId,
            );
            $this->prepare($sql)->execute($params);
        }

        /**
        * Возвращает первую дисциплину программы.
        *
        * @param  int $program_id Идентификатор программы.
        * @return array
        */
        public function getFirstDisciplineOfProgram($program_id) {
            $sql = 'SELECT *
            FROM ' . $this->_tables['disciplines'] . ' d
            WHERE d.program_id = ? AND d.serial_number = 0';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($program_id));

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Возвращает первый раздел дисциплины.
        *
        * @param  int $discipline_id Идентификатор дисциплины.
        * @return array
        */
        public function getFirstSectionOfDiscipline($discipline_id) {
            $sql = 'SELECT *
            FROM ' . $this->_tables['sections'] . ' s
            WHERE s.discipline_id = ? AND s.number = 1';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($discipline_id));

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Возвращает список разделов дисциплины.
        *
        * @param  int $discipline_id Идентификатор дисциплины.
        * @return array
        */
        public function getSectionsByDiscipline($discipline_id) {
            $sql = 'SELECT *
            FROM ' . $this->_tables['sections'] . ' s
            WHERE s.discipline_id = ?
            ORDER BY s.number';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($discipline_id));

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);;
        }

        /**
        * Возвращает список студентов, изучающих дисциплину.
        *
        * @param  int $discipline_id Идентификатор дисциплины.
        * @return array
        */
        public function getStudentsByDiscipline($discipline_id) {
            $sql = 'SELECT u.user_id, u.name, u.surname, u.patronymic
            FROM ' . $this->_tables['users'] . ' u
            LEFT JOIN ' . $this->_tables['applications'] . ' a
            ON u.user_id = a.user_id
            LEFT JOIN ' . $this->_tables['disciplines'] . ' d
            ON d.program_id = a.object_id
            WHERE(a.type = \'discipline\' AND a.object_id = :discipline_id) OR(a.type = \'program\' AND d.discipline_id = :discipline_id)';
            $sql_params = array(
                ':discipline_id' => $discipline_id
            );
            $stmt = $this->prepare($sql);
            $stmt->execute($sql_params);

            return $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
        }

        /**
        * Возвращает идентификатор дисциплины, к которой относится раздел.
        *
        * @param  int $section_id Идентификатор дисциплины.
        * @return array
        */
        public function getDisciplineNumberBySection($section_id) {
            $sql = 'SELECT s.discipline_id
            FROM ' . $this->_tables['sections'] . ' s
            WHERE s.section_id = ?';
            $stmt = $this->prepare($sql);
            $stmt->execute(array($section_id));

            return $stmt->fetch(Db_Pdo::FETCH_ASSOC);
        }

        /*
         
        В ЖОПУ!!! Это не си, посему работа с деревьями в жопу!!!
        
         static function buildTreeFromArrays($a,$b) {
            $c = array();
            foreach ($a as $i => $ar) {
                if (!isset($c[$ar['val']])) {
                    $c[$ar['val']['val']] = $ar['val'];
                    $c[$ar['val']['sons']] = array();
                }
                $t = each($c);
                array_push ($c[$ar['val']['sons']], $t['value']);
            }
            return $c;
        }
        */
        
       
        /*
        Возвращает дерево Программа->Дисциплина->Секция
        */
        
        /*
        public function getMapsPDS() {
            Все-таки, придется разбить этот запрос на два
             $sql = 'SELECT DISTINCT program_id, discipline_id, section_id
                FROM programs LEFT JOIN (disciplines LEFT JOIN sections USING (discipline_id)) USING (program_id);';
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $a = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            $b = array();
            for ($i = 0; $i < count($a); $i++) {
                foreach ($a[$i] as $key => $val) {
                    $b[$key][$i] = $val;
                }
            }
            $c = $this->buildMapFromArrays($b['discipline_id'],$b['section_id']);
            $d = $this->buildMapFromArrays($b['program_id'], $b['discipline_id']);
            //$d = array_unique($d);
            print "discipline -> section\n";
            print_r($c);
            print "program -> discipline\n";
            print_r($d);
            return $d;
        }
        */
        
        static function buildMapFromArrays($a,$b) {
            $c = array();
            reset($b);
            foreach ($a as $val) {
                if (!isset($c[$val])) {
                    $c[$val] = array();
                }
                $t = each($b);
                array_push($c[$val], $t['value']);
            }
            return $c;
        }
        
        public function getMapProgramDiscipline() {
            $sql = 'SELECT DISTINCT program_id, discipline_id
                FROM programs LEFT JOIN disciplines USING (program_id);';
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $a = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            $b = array();
            for ($i = 0; $i < count($a); $i++) {
                foreach ($a[$i] as $key => $val) {
                    $b[$key][$i] = $val;
                }
            }
            $c = $this->buildMapFromArrays($b['program_id'], $b['discipline_id']);
            /*print "discipline -> section\n";
            print_r($c);*/
            return $c;
        }
        
        public function getMapDisciplineSection() {
            $sql = 'SELECT DISTINCT discipline_id, section_id
                FROM disciplines LEFT JOIN sections USING (discipline_id);';
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $a = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            $b = array();
            for ($i = 0; $i < count($a); $i++) {
                foreach ($a[$i] as $key => $val) {
                    $b[$key][$i] = $val;
                }
            }
            $c = $this->buildMapFromArrays($b['discipline_id'],$b['section_id']);
            /*print "program -> discipline\n";
            print_r($c);*/
            return $c;
        }
    }