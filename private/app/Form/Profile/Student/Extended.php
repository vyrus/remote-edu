<?php
    
    /* $Id$ */

    class Form_Profile_Student_Extended extends Form_Profile_Student_Abstract {
        const REGEX_SYMBOLIC = '/^[0-9а-яёa-z,.-\s]+$/ixu';
        
        public function __construct($action) {
            $this
                /* Параметры формы */
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                
                /* Поля */
                ->_addSurname()
                ->_addName()
                ->_addPatronymic()
                ->_addBirthday()
                ->_addPassportSeries()
                ->_addPassportNumber()
                ->_addPassportGivenBy()
                ->_addPassportGivenDate()
                ->_addRegion()
                ->_addRegionId()
                ->_addCity()
                ->_addCityId()
                ->_addStreet()
                ->_addHouse()
                ->_addFlat()
                ->_addEduDocType()
                ->_addEduDocCustomType()
                ->_addEduDocNumber()
                ->_addExitYear()
                ->_addSpeciality()
                ->_addQualification()
                ->_addPhoneMobile()
                ->_addPhoneStationary()
            ;
        }
        
        public static function create($action) {
            return new self($action);
        }
        
        public function validate(Http_Request $request, Model_Region $region, Model_Locality $locality) {
            $result = parent::validate($request);
            
//            $result &= $this->_validateRegionId('region_id', $region, 'Выберите регион из списка');
//            $result &= $this->_validateCityId('city_id', $locality, 'Выберите город из списка');
            $result &= $this->_validateOptionalField('flat', '/^[0-9]+$/ixu', 'Укажите номер квартиры (целое число)');
            
            $regex = sprintf('/^(%s|%s|%s)$/xu', Model_User::DOC_TYPE_DIPLOMA_HIGH,
                                                 Model_User::DOC_TYPE_DIPLOMA_MEDIUM,
                                                 Model_User::DOC_TYPE_CUSTOM);
            
            $result &= $this->_validateEduDocType('doc_type', $regex, 'Выберите вид документа или оставьте поле пустым');
            
            $doc_type = $this->doc_type->value;
            
            if (Model_User::DOC_TYPE_EMPTY !== $doc_type)
            {
                if (Model_User::DOC_TYPE_CUSTOM == $doc_type) {
                    $result &= $this->_validateManual('doc_custom_type', self::REGEX_SYMBOLIC, 'Введите вид документа');
                }
                
                $result &= $this->_validateManual('doc_number', '/^[0-9а-яёa-z-_]+$/ixu', 'Введите номер документа');
                $result &= $this->_validateManual('exit_year', '/^[0-9]{4}$/ixu', 'Введите год окончания в формате гггг');
                $result &= $this->_validateManual('speciality', self::REGEX_SYMBOLIC, 'Введите специальность');
                $result &= $this->_validateManual('qualification', self::REGEX_SYMBOLIC, 'Введите квалификацию');
            }
            
            $result &= $this->_validateOptionalField('phone_mobile', '/^\+7[\s]+\([0-9]{3}\)[\s]+[0-9]{3}-[0-9]{2}-[0-9]{2}$/ixu', 'Введите номер мобильного в формате +7 (xxx) xxx-xx-xx');
            $result &= $this->_validateOptionalField('phone_stationary', '/^[0-9-]+$/ixu', 'Введите номер телефона');
            
            return $result;
        }
    }

?>