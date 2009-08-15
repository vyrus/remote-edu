<?php
	class Model_Education_Programs extends Mvc_Model_Abstract {
		private $_cache = array ();
		
		public static function create() {
            return new self();
        }
		
		public function specialityIDExists ($id) {
			$sql =
<<<QUERY
SELECT `program_id`
FROM `programs`
WHERE `program_id`=?
QUERY;

			$stmt = $this->prepare($sql);
			$stmt->execute(array($id));

			return ($stmt->fetch () !== FALSE);
		}
		
		public function specialityExists ($title) {
			$sql =
<<<QUERY
SELECT `program_id`
FROM `programs`
WHERE `title`=?
QUERY;
			
			$stmt = $this->prepare($sql);
			$stmt->execute(array($title));

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
		
		public function disciplineExists ($specialityID, $title) {
			$sql =
<<<QUERY
SELECT `discipline_id`
FROM `disciplines`
WHERE 
	`program_id`=:program_id AND
	`title`		=:title
QUERY;
			$params = array (
				':program_id'	=> $specialityID,
				':title'		=> $title,
			);
			
			$disciplineExistsStmt = $this->prepare ($sql);
			$disciplineExistsStmt->execute ($params);
			
			return ($disciplineExistsStmt->fetch () !== FALSE);
		}
		
		public function sectionExists ($disciplineID, $title) {
			$sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE 
	`discipline_id`=:discipline_id AND
	`title`=:title
QUERY;
			$params = array (
				':discipline_id'=> $disciplineID,
				':title'		=> $title,
			);

			$sectionExistsStmt = $this->prepare ($sql);
			$sectionExistsStmt->execute ($params);

			return ($sectionExistsStmt->fetch () !== FALSE);
		}
		
		public function sectionNumberExists  ($disciplineID, $number) {
			$sql =
<<<QUERY
SELECT `section_id`
FROM `sections`
WHERE 
	`discipline_id`=:discipline_id AND
	`number`=:number
QUERY;
			$params = array (
				':discipline_id'=> $disciplineID,
				':number'		=> $number,
			);

			$sectionExistsStmt = $this->prepare ($sql);
			$sectionExistsStmt->execute ($params);

			return ($sectionExistsStmt->fetch () !== FALSE);
		}
				
		public function createSpeciality ($title, $labourIntensive) {
			$sql =
<<<QUERY
INSERT INTO `programs` (`title`,`labour_intensive`,`edu_type`,`paid_type`)
VALUES (:title,:labour_intensive,:edu_type,:paid_type)
QUERY;
			$params = array (
				':title'			=> $title,
				':labour_intensive'	=> $labourIntensive,
				':edu_type'			=> 'direction',
				':paid_type'		=> 'free',
			);
			
			$this
				->prepare ($sql)
				->execute ($params);
		}
		
		public function createDiscipline ($specialityID, $title, $coef, $labourIntensive) {
			$sql =
<<<QUERY
INSERT INTO `disciplines` (`program_id`,`title`,`coef`,`labour_intensive`)
VALUES (:program_id,:title,:coef,:labour_intensive)
QUERY;

			$params = array (
				':program_id'		=> $specialityID,
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
		
		public function getSpecialities () {
			if (! isset ($this->_cache['specialities'])) {
				$sql =
<<<QUERY
SELECT *
FROM `programs`
QUERY;
			
				$this->_cache['specialities'] = $this->query ($sql)->fetchAll (PDO::FETCH_ASSOC);
			}
			
			return $this->_cache['specialities'];
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
		
		public function getSpecialitiesDisciplines () {
			if (! isset ($this->_cache['specialitiesDisciplines'])) {
				$disciplines = $this->getDisciplines ();
				$result = array ();
				if (count ($disciplines)) {
					$programID = $disciplines[0]['program_id'];
					for ($i = 0; $i < count ($disciplines); $i++) {
						$result[$programID][] = $disciplines[$i];
						if (
							($i + 1 < count ($disciplines)) &&
							($disciplines[$i + 1]['program_id'] != $programID)
						) {
							$programID = $disciplines[$i + 1]['program_id'];
						}
					}
				}
				
				$this->_cache['specialitiesDisciplines'] = $result;
			}	
			
			return $this->_cache['specialitiesDisciplines'];
		}
	}
?>