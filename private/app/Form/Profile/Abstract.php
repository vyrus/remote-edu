<?php

    /* $Id$ */

    abstract class Form_Profile_Abstract extends Form_Abstract {
        const PASSWD_REGEX_REQUIRED = '/^[a-z0-9_]+$/ixu';

        const PASSWD_REGEX_NOT_REQUIRED = '/^(?:[a-z0-9_]+)?$/ixu';

        const DATE_REGEX = '/^(?:[0-9]{2}\.){2}[0-9]{4}$/ixu';

        protected function _validateRegistration(Http_Request $request, Model_User $user) {
            $result = parent::validate($request);
            $result &= $this->_validateLogin($user);

            return $result;
        }

        protected function _validateLogin(Model_User $user) {
            /* C помощью модели проверяем наличие пользователя с таким логином */
            if ($user->exists($this->login->value))
            {
                $this->invalidate();
                $error = 'Указанное имя пользователя уже занято';
                $this->setValidationError('login', $error);

                return false;
            }

            return true;
        }

        protected function _addLogin() {
            return
            $this->addField('login')
                 ->setValidator('/^[a-z0-9_]{3,}$/ixu')
                 ->setError(
                     'Имя пользователя должно состоять из букв латинского ' .
                     'алфавита, цифр и символа подчёркивания (минимальная' .
                     'длина - 3 символа)'
                 );
        }

        protected function _addPasswd() {
            return
            $this->addField('passwd')
                 ->setValidator(self::PASSWD_REGEX_REQUIRED)
                 ->setError(
                     'Пароль должен состоять из латинских букв, цифр и ' .
                     'символа подчёркивания'
                 );
        }

        protected function _addPasswdCheck() {
            return $this->addField('passwd_check');
        }

        protected function _addOldPasswd() {
            return
            $this->addField('old_passwd')
                 ->setValidator(self::PASSWD_REGEX_NOT_REQUIRED)
                 ->setError(
                     'Пароль должен состоять из латинских букв, цифр и ' .
                     'символа подчёркивания'
                 );
        }

        protected function _addNewPasswd() {
            return
            $this->addField('new_passwd')
                 ->setValidator(self::PASSWD_REGEX_NOT_REQUIRED)
                 ->setError(
                     'Пароль должен состоять из латинских букв, цифр и ' .
                     'символа подчёркивания'
                 );
        }

        protected function _addEmail() {
            return
            $this->addField('email')
                 ->setValidator('|[0-9a-z-]+@[0-9a-z-^\.]+\.[a-z]{2,6}|i')
                 ->setError(
                     'Некорректный адрес электронной почты'
                 );
        }

        protected function _addRole() {
            return
            $this->addField('role')
                 ->setValidator('/^(?:teacher|admin)$/ixu')
                 ->setError(
                     'Некорректно задана роль пользователя'
                 );
        }

        protected function _addSurname() {
            return
            $this->addField('surname')
                 ->setValidator('/^[А-ЯЁ]{1}[а-яё]{1,}$/xu')
                 ->setError(
                    'Фамилия должна состоять минимум из 2-х букв русского алфавита (первая - заглавная)'
                 )
                 ->setHint(
                    '*Фамилия должна состоять минимум из 2-х букв русского алфавита'
                 );
        }

        protected function _addName() {
            return
            $this->addField('name')
                 ->setValidator('/^[А-ЯЁ]{1}[а-яё]{1,}$/xu')
                 ->setError(
                     'Имя должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                 )
                 ->setHint(
                      '*Имя должно состоять минимум из 2-х букв кириллицы'
                 );
        }

        protected function _addPatronymic() {
            return
            $this->addField('patronymic')
                 ->setValidator('/^[А-ЯЁ]{1}[а-яё]{1,}$/xu')
                 ->setError(
                     'Отчество должно состоять минимум из 2-х букв кириллицы (первая - заглавная)'
                 )
                 ->setHint(
                    '*Отчество должно состоять минимум из 2-х букв кириллицы'
                 );
        }

        protected function _addBirthday() {
            return
            $this->addField('birthday')
                 ->setValidator(self::DATE_REGEX)
                 ->setError('Введите дату в формате дд.мм.гггг')
                 ->setHint('*Формат - дд.мм.гггг');
        }

        protected function _addPassportSeries() {
            return
            $this->addField('passport_series')
                 ->setValidator('/^[0-9]{4}$/ixu')
                 ->setError('Серия паспорта должна состоять из 4-х цифр')
                 ->setHint('*4 цифры');
        }

        protected function _addPassportNumber() {
            return
            $this->addField('passport_number')
                 ->setValidator('/^[0-9]{6}$/ixu')
                 ->setError('Номер паспорта должен состоять из 6-х цифр')
                 ->setHint('*6 цифр');
        }

        protected function _addPassportGivenBy() {
            return
            $this->addField('passport_given_by')
                 ->setValidator('/^[0-9а-яё.,-\s]+$/ixu')
                 ->setError('Введите, кем выдан паспорт')
                 ->setHint('*Введите, кем выдан паспорт');
        }

        protected function _addPassportGivenDate() {
            return
            $this->addField('passport_given_date')
                 ->setValidator(self::DATE_REGEX)
                 ->setError('Введите дату в формате дд.мм.гггг')
                 ->setHint('*Формат - дд.мм.гггг');
        }

        protected function _addRegion() {
            return
            $this->addField('region')
                  ->setHint('*Введите Ваш регион');
        }

        protected function _addRegionId() {
            return
            $this->addField('region_id');
        }

        protected function _addCity() {
            return
            $this->addField('city')
                  ->setHint('*Введите Ваш город');
        }

        protected function _addCityId() {
            return
            $this->addField('city_id');
        }

        protected function _addStreet() {
            return
            $this->addField('street')
                 ->setValidator('/^[0-9а-яё.,-\\/\s]+$/ixu')
                 ->setError('Введите улицу')
                 ->setHint('*Введите Вашу улицу');
        }

        protected function _addHouse() {
            return
            $this->addField('house')
                 ->setValidator('/^[0-9а-яё.,-\\/\s]+$/ixu')
                 ->setError('Неверный формат ввода')
                 ->setHint('*Введите номер Вашего дома');
        }

        protected function _addFlat() {
            return
            $this->addField('flat')
                 ->setHint('*Введите номер Вашей квартиры');
        }

        protected function _addEduDocType() {
            return
            $this->addField('doc_type')
                 ->setHint('*Выберите тип документа о Вашем образовании');
        }

        protected function _addEduDocCustomType() {
            return
            $this->addField('doc_custom_type');
        }

        protected function _addEduDocNumber() {
            return
            $this->addField('doc_number')
                 ->setHint('*Введите номер документа');
        }

        protected function _addExitYear() {
            return
            $this->addField('exit_year')
                 ->setHint('*Год Вашего выпуска из учебного заведения');
        }

        protected function _addSpeciality() {
            return
            $this->addField('speciality')
                 ->setHint('Введите Вашу специальность');
        }

        protected function _addQualification() {
            return
            $this->addField('qualification')
                 ->setHint('Введите Вашу квалификацию');
        }

        protected function _addPhoneMobile() {
            return
            $this->addField('phone_mobile')
                 ->setHint('Формат ввода - +7 (xxx) xxx-xx-xx');
        }

        protected function _addPhoneStationary() {
            return
            $this->addField('phone_stationary');
        }

        protected function _validateRegionId($field_id, Model_Region $region, $error) {
            $id = $this->$field_id->value;

            if (empty($id) || !$region->exists($id)) {
                $this->invalidate();
                $this->setValidationError($field_id, $error);

                $this->setValue('region', '');
                $this->setValue($field_id, '');

                return false;
            }

            return true;
        }

        protected function _validateCityId($field_id, Model_Locality $locality, $error) {
            $id = $this->$field_id->value;

            if (empty($id) || !$locality->exists($id)) {
                $this->invalidate();
                $this->setValidationError($field_id, $error);

                $this->setValue('city', '');
                $this->setValue($field_id, '');

                return false;
            }

            return true;
        }

        protected function _validateFlat($field_id, $regex, $error) {
            $flat = $this->$field_id->value;

            if (empty($flat)) {
                return true;
            }

            if (!$this->_validateRegex($flat, $regex))
            {
                $this->setValue($field_id, '');
                $this->setValidationError($field_id, $error);

                return false;
            }

            return true;
        }

        protected function _validateEduDocType($field_id, $regex, $error) {
            $type = $this->$field_id->value;

            if (empty($type)) {
                return true;
            }

            if (!$this->_validateRegex($type, $regex)) {
                $this->setValue($field_id, '');
                $this->setValidationError($field_id, $error);

                return false;
            }

            return true;
        }

        /**
        * @todo Refactor: move it to Form_Abstract and use from parent::validate.
        */
        protected function _validateManual($field_id, $regex, $error) {
            $value = $this->$field_id->value;

            if (!$this->_validateRegex($value, $regex)) {
                $this->setValue($field_id, '');
                $this->setValidationError($field_id, $error);

                return false;
            }

            return true;
        }

        /**
        * @todo Refactor: support for optional fields.
        */
        protected function _validateOptionalField($field_id, $regex, $error) {
            $value = $this->$field_id->value;

            if (empty($value)) {
                return true;
            }

            if (!$this->_validateRegex($value, $regex)) {
                $this->setValue($field_id, '');
                $this->setValidationError($field_id, $error);

                return false;
            }

            return true;
        }
    }

?>