<?php
	class Model_Education_Programs extends Mvc_Model_Abstract {
		const	CHECK_BY_PARENT_ID 	= 0;
		const 	CHECK_BY_OWN_ID		= 1;
		
		private $_cache = array ();
		
		public static function create() {
            return new self();
        }
		
		public function programIDExists ($id, $type) {
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

			return ($stmt->fetch () !== FALSE);
		}
		
		public function programExists ($title, $type) {
			$sql =
<<<QUERY
SELECT `program_id`
FROM `programs`
WHERE 
	`title`=? AND
	`edu_type`=?
QUERY;
			
			$stmt = $this->prepare($sql);
			$stmt->execute (array ($title, $type));

			return ($stmt->fetch () !== FALSE);
		}
		
		public function disciplineIDExists ($id) {
			$sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE `discipline_id`=?
QUERY;
			$stmt = $this->prepare($sql);
			$stmt->execute(array($id));

			return ($stmt->fetch () !== FALSE);
		}
		
		public function disciplineExists ($id, $title, $checkType = Model_Education_Programs::CHECK_BY_PARENT_ID) {
			if ($checkType == Model_Education_Programs::CHECK_BY_OWN_ID) {
				$sql =
<<<QUERY
SELECT `program_id`
FROM `disciplines`
WHERE `discipline_id`=?
QUERY;
				$getProgramIDStmt = $this->prepare ($sql);
				$getProgramIDStmt->bindColumn ('program_id', $id);
				$getProgramIDStmt->execute (array ($id));
				$getProgramIDStmt->fetch (PDO::FETCH_BOUND);
			}
			
			$sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE 
	`program_id`=:program_id AND
	`title`		=:title
QUERY;
			$params = array (
				':program_id'	=> $id,
				':title'		=> $title,
			);
			
			$disciplineExistsStmt = $this->prepare ($sql);
			$disciplineExistsStmt->execute ($params);
			
			return ($disciplineExistsStmt->fetch () !== FALSE);
		}
		
		public function sectionExists ($id, $title, $checkType = Model_Education_Programs::CHECK_BY_PARENT_ID) {
			if ($checkType == Model_Education_Programs::CHECK_BY_OWN_ID) {
				$sql =
<<<QUERY
SELECT `discipline_id`
FROM `sections`
WHERE `section_id`=?
QUERY;
				$getDisciplineIDStmt = $this->prepare ($sql);
				$getDisciplineIDStmt->bindColumn ('discipline_id', $id);
				$getDisciplineIDStmt->execute (array ($id));
				$getDisciplineIDStmt->fetch (PDO::FETCH_BOUND);
			}

			$sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE 
	`discipline_id`=:discipline_id AND
	`title`=:title
QUERY;
			$params = array (
				':discipline_id'=> $id,
				':title'		=> $title,
			);

			$sectionExistsStmt = $this->prepare ($sql);
			$sectionExistsStmt->execute ($params);

			return ($sectionExistsStmt->fetch () !== FALSE);
		}
		
		public function sectionIDExists ($id) {
			$sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE `section_id`=?
QUERY;
			$stmt = $this->prepare($sql);
			$stmt->execute(array($id));

			return ($stmt->fetch () !== FALSE);		
		}
		
		public function sectionNumberExists  ($id, $number, $checkType = Model_Education_Programs::CHECK_BY_PARENT_ID) {
			if ($checkType == Model_Education_Programs::CHECK_BY_OWN_ID) {
				$sql =
<<<QUERY
SELECT `discipline_id`
FROM `sections`
WHERE `section_id`=?
QUERY;
				$getDisciplineIDStmt = $this->prepare ($sql);
				$getDisciplineIDStmt->bindColumn ('discipline_id', $id);
				$getDisciplineIDStmt->execute (array ($id));
				$getDisciplineIDStmt->fetch (PDO::FETCH_BOUND);
			}
			
			$sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE 
	`discipline_id`=:discipline_id AND
	`number`=:number
QUERY;
			$params = array (
				':discipline_id'=> $id,
				':number'		=> $number,
			);

			$sectionExistsStmt = $this->prepare ($sql);
			$sectionExistsStmt->execute ($params);

			return ($sectionExistsStmt->fetch () !== FALSE);
		}
				
		public function createProgram ($title, $labourIntensive, $type) {
			$sql =
<<<QUERY
INSERT INTO `programs` (`title`,`labour_intensive`,`edu_type`,`paid_type`)
VALUES (:title,:labour_intensive,:edu_type,:paid_type)
QUERY;
			$params = array (
				':title'			=> $title,
				':labour_intensive'	=> $labourIntensive,
				':edu_type'			=> $type,
				':paid_type'		=> 'free',
			);
			
			$this
				->prepare ($sql)
				->execute ($params);
		}
		
		public function createDiscipline ($programID, $title, $coef, $labourIntensive) {
			$sql =
<<<QUERY
INSERT INTO `disciplines` (`program_id`,`title`,`coef`,`labour_intensive`)
VALUES (:program_id,:title,:coef,:labour_intensive)
QUERY;

			$params = array (
				':program_id'		=> $programID,
				':title'			=> $title,
				':coef'				=> $coef,
				':labour_intensive'	=> $labourIntensive,				
			);
			
			$this
				->prepare ($sql)
				->execute ($params);
		}
		
		public function createSection ($disciplineID, $title, $number) {
			$sql =
<<<QUERY
INSERT INTO `sections` (`discipline_id`,`title`,`number`)
VALUES (:discipline_id,:title,:number)
QUERY;

			$params = array (
				':discipline_id'	=> $disciplineID,
				':title'			=> $title,
				':number'			=> $number
			);

			$this
				->prepare ($sql)
				->execute ($params);
		}
		
		public function getDirections () {
			if (! isset ($this->_cache['directions'])) {
				$sql =
<<<QUERY
SELECT *
FROM `programs`
WHERE `edu_type`='direction'
QUERY;
			
				$this->_cache['directions'] = $this->query ($sql)->fetchAll (PDO::FETCH_ASSOC);
			}
			
			return $this->_cache['directions'];
		}
		
		public function getCourses () {
			$sql =
<<<QUERY
SELECT *
FROM `programs`
WHERE `edu_type`='course'
QUERY;

			return $this->query ($sql)->fetchAll (PDO::FETCH_ASSOC);
		}
		
		public function getDisciplines () {
			if (! isset ($this->_cache['disciplines'])) {			
				$sql =
<<<QUERY
SELECT *
FROM `disciplines`
ORDER BY `program_id`
QUERY;

				$this->_cache['disciplines'] = $this->query ($sql)->fetchAll (PDO::FETCH_ASSOC);
			}

			return $this->_cache['disciplines'];
		}
		
		public function getDirectionsDisciplines () {
			if (! isset ($this->_cache['directionsDisciplines'])) {
				$disciplines = $this->getDisciplines ();
				$result = array ();
				if (count ($disciplines)) {
					$programID = $disciplines[0]['program_id'];
					foreach ($disciplines as $i => $discipline) {
						$result[$programID][] = $discipline;
						if (
							($i + 1 < count ($disciplines)) &&
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
		
		public function getSections () {
			
			if (! isset ($this->_cache['sections'])) {
				$sql =
<<<QUERY
SELECT *
FROM `sections`
ORDER BY `discipline_id`
QUERY;
				$this->_cache['sections'] = $this->query ($sql)->fetchAll (PDO::FETCH_ASSOC);
			}
			
			return $this->_cache['sections'];
		}
		
		public function getDisciplinesSections () {
			if (! isset ($this->_cache ['disciplinesSections'])) {
				$sections = $this->getSections ();
				$result = array ();
				if (count ($sections)) {
					$disciplineID = $sections[0]['discipline_id'];
					foreach ($sections as $i => $section) {
						$result[$disciplineID][] = $section;
						if (
							($i + 1 < count ($sections)) &&
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
		
		public function removeProgram ($programID) {
			$this->removeDisciplines ($programID);
			
			$sql =
<<<QUERY
DELETE FROM `programs`
WHERE `program_id`=?
QUERY;
			
			$this->prepare ($sql)->execute (array ($programID));
		}
		
		private function removeDisciplines ($programID) {
			$sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE `program_id`=?
QUERY;
			
			$disciplinesStmt = $this->prepare ($sql);
			$disciplinesStmt->execute (array ($programID));
			$disciplines = $disciplinesStmt->fetchAll (PDO::FETCH_ASSOC);
			if (count ($disciplines)) {
				foreach ($disciplines as $i => $discipline) {
					$this->removeSections ($discipline['discipline_id']);
				}
				
				$sql =
<<<QUERY
DELETE FROM `disciplines`
WHERE `program_id`=?
QUERY;
				$this->prepare ($sql)->execute (array ($programID));
			}
		}
		
		public function removeDiscipline ($disciplineID) {
			$this->removeSections ($disciplineID);
			
			$sql =
<<<QUERY
DELETE FROM `disciplines`
WHERE `discipline_id`=?
QUERY;
			$this->prepare ($sql)->execute (array ($disciplineID));
		}
		
		private function removeSections ($disciplineID) {
			$sql =
<<<QUERY
DELETE FROM `sections`
WHERE `discipline_id`=?
QUERY;
			
			$this->prepare ($sql)->execute (array ($disciplineID));
		}
		
		public function removeSection ($sectionID) {
			$sql =
<<<QUERY
DELETE FROM `sections`
WHERE `section_id`=?
QUERY;
			
			$this->prepare ($sql)->execute (array ($sectionID));
		}
		
		public function getProgram ($programID, $type, &$title, &$labourIntensive) {
			$sql =
<<<QUERY
SELECT `title`,`labour_intensive`
FROM `programs`
WHERE 
	`program_id`=? AND
	`edu_type`=?
QUERY;
			$getProgramStmt = $this->prepare ($sql);
			$getProgramStmt->bindColumn ('title', $title);
			$getProgramStmt->bindColumn ('labour_intensive', $labourIntensive);
			$getProgramStmt->execute (array ($programID, $type));
			
			$getProgramStmt->fetch (PDO::FETCH_BOUND);			
		}
		
		public function editProgram ($programID, $type, $title, $labourIntensive) {
			$sql =
<<<QUERY
UPDATE `programs`
SET
	`title`=:title,
	`labour_intensive`=:labour_intensive
WHERE
	`program_id`=:program_id AND
	`edu_type`=:program_type
QUERY;

			$params = array (
				':title'			=> $title,
				':labour_intensive'	=> $labourIntensive,
				':program_id'		=> $programID,
				':program_type'		=> $type,
			);

			$this
				->prepare ($sql)
				->execute ($params);
		}
		
		public function getDiscipline ($disciplineID, &$title, &$labourIntensive, &$coef) {
			$sql =
<<<QUERY
SELECT `title`,`labour_intensive`,`coef`
FROM `disciplines`
WHERE
	`discipline_id`=?
QUERY;
			$getDisciplineStmt = $this->prepare ($sql);
			$getDisciplineStmt->bindColumn 	('title', 				$title);
			$getDisciplineStmt->bindColumn 	('labour_intensive', 	$labourIntensive);
			$getDisciplineStmt->bindColumn 	('coef', 				$coef);
			$getDisciplineStmt->execute 	(array ($disciplineID));
			
			$getDisciplineStmt->fetch (PDO::FETCH_BOUND);
		}
		
		public function editDiscipline ($disciplineID, $title, $labourIntensive, $coef) {
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

			$params = array (
				':title'			=> $title,
				':labour_intensive'	=> $labourIntensive,
				':coef'				=> $coef,
				':discipline_id'	=> $disciplineID
			);

			$this
				->prepare ($sql)
				->execute ($params);
		}
		
		public function getSection ($sectionID, &$title, &$number) {
			$sql =
<<<QUERY
SELECT `title`,`number`
FROM `sections`
WHERE `section_id`=?
QUERY;
			$getSectionStmt = $this->prepare ($sql);
			$getSectionStmt->bindColumn ('title', 	$title);
			$getSectionStmt->bindColumn ('number', 	$number);
			$getSectionStmt->execute 	(array ($sectionID));
			
			$getSectionStmt->fetch (PDO::FETCH_BOUND);
		}
		
		public function editSection ($sectionID, $title, $number) {
			$sql =
<<<QUERY
UPDATE `sections`
SET
	`title`=:title,
	`number`=:number
WHERE
	`section_id`=:section_id
QUERY;
			
			$params = array (
				':title'		=> $title,
				':number'		=> $number,
				':section_id'	=> $sectionID,
			);
			
			$this
				->prepare ($sql)
				->execute ($params);
		}
	}
?>