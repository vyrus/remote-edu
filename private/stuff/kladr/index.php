<?php

    /* $Id$ */
           
    require_once '../../init.php';

    header('Content-Type: text/plain; charset=utf-8');
    
    $dbf = './KLADR.dbf';
    $db  = $config['db'];
    
    Converter_Kladr::create($dbf, $db['dsn'], $db['user'], $db['passwd'])
        ->convert()
        ->fixupRegions()
        ->fixupTwoCapitals()
    ;
    die;
    
    $region_abbrs = array(
        'АО'    => 'Автономный округ',
        'Аобл'  => 'Автономная область',
        'Респ'  => 'Республика',
        'г'     => 'Город',
        'край'  => 'Край',
        'обл'   => 'Область',
        /* округ -> АО */
        'округ' => 'Автономный округ'
    );
    
    $local_abbrs = array(
        'аал'        => 'Аал',
        'арбан'      => 'Арбан',
        'аул'        => 'Аул',
        'волость'    => 'Волость',
        'высел'      => 'Высел',
        'г'          => 'Город',
        'городок'    => 'Городок',
        'д'          => 'Деревня',
        'дп'         => 'Дачный поселок',
        'ж/д_будка'  => 'Железнодорожная будка',
        'ж/д_казарм' => 'Железнодорожная казарма',
        'ж/д_оп'     => 'Ж/д остановочный (обгонный) пункт',
        'ж/д_платф'  => 'Железнодорожная платформа',
        'ж/д_пост'   => 'Железнодорожный пост',
        'ж/д_рзд'    => 'Железнодорожный разъезд',
        'ж/д_ст'     => 'Железнодорожная станция',
        'заимка'     => 'Заимка',
        'казарма'    => 'Казарма',
        'кв-л'       => 'Квартал',
        'кордон'     => 'Кордон',
        'кп'         => 'Курортный поселок',
        'м'          => 'Местечко',
        'мкр'        => 'Микрорайон',
        'нп'         => 'Населенный пункт',
        'остров'     => 'Остров',
        'п'          => 'Поселок сельского типа',
        'п/о'        => 'Почтовое отделение',
        'п/р'        => 'Планировочный район',
        'п/ст'       => 'Поселок и(при) станция(и)',
        'пгт'        => 'Поселок городского типа',
        'погост'     => 'Погост',
        'починок'    => 'Починок',
        'промзона'   => 'Промышленная зона',
        'рзд'        => 'Разъезд',
        'рп'         => 'Рабочий (заводской) поселок',
        'с'          => 'Село',
        'с/а'        => 'Сельская администрация',
        'с/о'        => 'Сельский округ',
        'с/п'        => 'Сельское поселение',
        'с/с'        => 'Сельсовет',
        'сл'         => 'Слобода',
        /**
        * @todo What's this? o_O
        */
        'снт'        => 'Снт',
        'ст'         => 'Станция',
        'ст-ца'      => 'Станица',
        'тер'        => 'Территория',
        'у'          => 'Улус',
        'х'          => 'Хутор'
    );
    
    /*
    $_abbrs = array_keys($region_abbrs);
    $_abbrs = array_keys($local_abbrs);
    
    $abbrs  = 'ENUM (\'';
    $abbrs .= implode('\', \'', $_abbrs);
    $abbrs .= '\')';
    
    echo $abbrs;
    die;
    */
    
    /**
    * @link http://www.gnivc.ru/Document.aspx?id=80
    */
    class Converter_Kladr {
        const OBJ_TYPE_REGION   = 'region';
        const OBJ_TYPE_CITY     = 'city';
        const OBJ_TYPE_LOCALITY = 'locality';
        
        protected $_link;
        
        protected $_db;     
        
        protected $_insert_region;
        
        protected $_insert_locality;
        
        protected $_code_regex = '/^
                                      ([0-9]{2}) # СС  — код субъекта Российской Федерации (региона)
                                      ([0-9]{3}) # РРР — код района
                                      ([0-9]{3}) # ГГГ — код города
                                      ([0-9]{3}) # ППП — код населенного пункта
                                      ([0-9]{2}) # АА  — признак актуальности наименования адресного объекта
                                  $/x';
                                  
        protected $_regions_lookup = array();
        
        protected $_region_abbrs = array();
        
        protected $_local_abbrs = array();
        
        protected $_region_abbrs_map = array(
            'АО'    => 'автономный округ',
            'Аобл'  => 'автономная область',
            'Респ'  => 'Республика',
            'г'     => 'Город',
            'край'  => 'край',
            'обл'   => 'область',
            /* округ -> АО */
            'округ' => 'Автономный округ'
        );
        
        public function __construct($file_name, $dsn, $user, $passwd) {
            if (false === ($this->_link = dbase_open($file_name, 0))) {
                throw new Exception('Не удалось открыть файл ' . $file_name);
            }
            
            $options = array(
                /* Будет кидать исключения при ошибках */
                Db_Pdo::ATTR_ERRMODE => Db_Pdo::ERRMODE_EXCEPTION
            );

            $this->_db = Db_Pdo::create($dsn, $user, $passwd, $options);
            
            $this->_db->exec('SET NAMES utf8');
            
            $sql = '
                INSERT INTO regions
                (code, name)
                VALUES (:code, :name)
            ';
            $this->_insert_region = $this->_db->prepare($sql);
            
            $sql = '
                INSERT INTO localities
                (region_id, code, name, type)
                VALUES (:region_id, :code, :name, :type)
            ';
            $this->_insert_locality = $this->_db->prepare($sql);
        }
        
        public static function create($file_name, $dsn, $user, $passwd) {
            return new self($file_name, $dsn, $user, $passwd);
        }
        
        public function convert() {
            $delayed_objects = array();
            
            $num_records = dbase_numrecords($this->_link);
            
            for ($i = 1; $i <= $num_records; $i++)
            {
                $record = dbase_get_record($this->_link, $i);
                 
                if ('skip_record' === ($obj = $this->_parseRecord($record))) {
                    continue;
                }
                
                if ('delay_object' === $this->_insertObject($obj)) {
                    $delayed_objects[] = $obj;
                }
            }
            
            foreach ($delayed_objects as $obj)
            {
                $this->_insertObject($obj, true);
            }
            
            //var_export($this->_region_abbrs);
            //var_export($this->_local_abbrs);
            
            return $this;
        }
        
        public function fixupRegions() {
            /**
            * @link http://ru.wikipedia.org/wiki/Федеративное_устройство_России#.D0.98.D0.B7.D0.BC.D0.B5.D0.BD.D0.B5.D0.BD.D0.B8.D1.8F_.D0.B2_.D1.84.D0.B5.D0.B4.D0.B5.D1.80.D0.B0.D1.82.D0.B8.D0.B2.D0.BD.D0.BE.D0.BC_.D1.83.D1.81.D1.82.D1.80.D0.BE.D0.B9.D1.81.D1.82.D0.B2.D0.B5_.D0.A0.D0.BE.D1.81.D1.81.D0.B8.D0.B8_.D0.BF.D0.BE.D1.81.D0.BB.D0.B5_.D0.BF.D1.80.D0.B5.D0.BA.D1.80.D0.B0.D1.89.D0.B5.D0.BD.D0.B8.D1.8F_.D1.81.D1.83.D1.89.D0.B5.D1.81.D1.82.D0.B2.D0.BE.D0.B2.D0.B0.D0.BD.D0.B8.D1.8F_.D0.A1.D0.A1.D0.A1.D0.A0
            */
            $patch = array(
                '01' => 'Республика Адыгея',
                '02' => 'Республика Башкортостан',
                '04' => 'Республика Алтай',
                '06' => 'Республика Ингушетия',
                '07' => 'Кабардино-Балкарская Республика',
                '09' => 'Карачаево-Черкесская Республика',
                '10' => 'Республика Карелия',
                '11' => 'Республика Коми',
                '12' => 'Республика Марий Эл',
                '13' => 'Республика Мордовия',
                '14' => 'Республика Саха (Якутия)',
                '16' => 'Республика Татарстан',
                '17' => 'Республика Тыва',
                '18' => 'Удмуртская Республика',
                '19' => 'Республика Хакасия',
                '21' => 'Чувашская Республика',
                '22' => 'Алтайский край',
                '23' => 'Краснодарский край',
                '25' => 'Приморский край',
                '26' => 'Ставропольский край',
                '27' => 'Хабаровский край',
                '28' => 'Амурская область',
                '29' => 'Архангельская область',
                '30' => 'Астраханская область',
                '31' => 'Белгородская область',
                '32' => 'Брянская область',
                '33' => 'Владимирская область',
                '34' => 'Волгоградская область',
                '35' => 'Вологодская область',
                '36' => 'Воронежская область',
                '37' => 'Ивановская область',
                '38' => 'Иркутская область',
                '39' => 'Калининградская область',
                '40' => 'Калужская область',
                '41' => 'Камчатский край',
                '42' => 'Кемеровская область',
                '43' => 'Кировская область',
                '44' => 'Костромская область',
                '45' => 'Курганская область',
                '46' => 'Курская область',
                '47' => 'Ленинградская область',
                '48' => 'Липецкая область',
                '49' => 'Магаданская область',
                '50' => 'Московская область',
                '51' => 'Мурманская область',
                '52' => 'Нижегородская область',
                '54' => 'Новосибирская область',
                '55' => 'Омская область',
                '56' => 'Оренбургская область',
                '57' => 'Орловская область',
                '58' => 'Пензенская область',
                '59' => 'Пермский край',
                '60' => 'Псковская область',
                '61' => 'Ростовская область',
                '62' => 'Рязанская область',
                '63' => 'Самарская область',
                '64' => 'Саратовская область',
                '65' => 'Сахалинская область',
                '67' => 'Смоленская область',
                '68' => 'Тамбовская область',
                '69' => 'Тверская область',
                '70' => 'Томская область',
                '71' => 'Тульская область',
                '72' => 'Тюменская область',
                '73' => 'Ульяновская область',
                '74' => 'Челябинская область',
                '75' => 'Забайкальский край',
                '76' => 'Ярославская область',
                '79' => 'Еврейская автономная область',
                '83' => 'Ненецкий автономный округ',
                '86' => 'Ханты-Мансийский автономный округ — Югра',
                '87' => 'Чукотский автономный округ',
                '89' => 'Ямало-Ненецкий автономный округ',
                '20' => 'Чеченская Республика',
                '66' => 'Свердловская область',
                '53' => 'Новгородская область',
                '03' => 'Республика Бурятия',
                '24' => 'Красноярский край',
                '15' => 'Республика Северная Осетия — Алания',
                '08' => 'Республика Калмыкия',
                '05' => 'Республика Дагестан'
            );
            
            /**
            * Переименовываем регионы в нормальные названия
            */
            $sql = '
                UPDATE regions
                SET name = :name
                WHERE code = :code
            ';
            
            $stmt = $this->_db->prepare($sql);
            
            $report = array();
            foreach ($patch as $code => $new_name) {
                $values = array(
                    ':name' => $new_name,
                    ':code' => $code
                );
                    
                $stmt->execute($values);
                
                $affected = $stmt->rowCount();
                $region = array('code' => $code, 'new_name' => $new_name);
                $report[] = array(
                    'updating region name',
                    'region'        => $region,
                    'affected_rows' => $affected
                );
            }
            
            $array = array_keys($patch);
            $array = implode(', ', $array);
            
            /**
            * Удаляем старые регионы
            */
            $sql = '
                DELETE
                FROM regions
                WHERE code NOT IN (' . $array . ')
            ';
            
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
            
            $affected = $stmt->rowCount();
            $report[] = array('deleting deprecated regions', 'affected_rows' => $affected);
            
            /**
            * Удаляем дубликаты регионов и обновляем внешние ключи
            */
            $sql = '
                SELECT r1.region_id AS old_region_id, r2.region_id AS new_region_id,
                       r1.name, r1.code
                FROM regions r1
                INNER JOIN regions r2 ON (
                    r2.code = r1.code AND
                    r2.region_id != r1.region_id
                )
            ';
            $find_duplicates = $this->_db->prepare($sql);
            
            $sql = '
                DELETE
                FROM regions
                WHERE region_id = ?
            ';
            $delete_duplicate = $this->_db->prepare($sql);
            
            $sql = '
                UPDATE localities
                SET region_id = :new_region_id
                WHERE region_id = :old_region_id
            ';
            $update_localities = $this->_db->prepare($sql);
            
            $find_duplicates->execute();
            $find_duplicates->setFetchMode(Db_Pdo::FETCH_ASSOC);
            
            $processed_codes = array();
            $num_duplicates = 0;
            foreach ($find_duplicates as $duplicate) {
                /* Посколько находятся две записи-дубликата, то не обрабатываем вторую запись-дубликат */
                if (in_array($duplicate['code'], $processed_codes)) {
                    continue;
                }
                
                $processed_codes[] = $duplicate['code'];
                
                $old_id = $duplicate['old_region_id'];
                $new_id = $duplicate['new_region_id'];
                
                /**
                * Выбираем, какой из дубликатов удалить, а какой оставить.
                * Способ выбора не имеет значения - всё равно уже оба дубликата
                * переименованы в правильное название (code у обоих записей одинаков)
                */
                $del_id = max($old_id, $new_id);
                $let_id = min($old_id, $new_id);
                
                $delete_duplicate->execute(array($del_id));
                
                $affected = $delete_duplicate->rowCount();
                $report[] = array(
                    'deleting duplicate entry',
                    'duplicate'     => $duplicate,
                    'affected_rows' => $affected
                );
                
                $values = array(
                    ':new_region_id' => $let_id,
                    ':old_region_id' => $del_id
                );
                $update_localities->execute($values);
                
                $affected = $update_localities->rowCount();
                $report[] = array(
                    'updating foreign keys',
                    'duplicate'     => $duplicate,
                    'affected_rows' => $affected
                );
                
                $num_duplicates++;
            }
            
            $report[] = array('found duplicate entries', 'num' => $num_duplicates);
            
            echo 'Applying regions patch...' . PHP_EOL;
            print_r($report);
            
            return $this;
        }
        
        /**
        * Москва и Санкт-Петербург являеются городами федерального назначения и 
        * в структуре КЛАДР выделены на уровне регионов. После применения
        * self::fixupRegions() они удаляются из таблицы регионов
        * конвертированной базы, теперь их надо добавить в список городов.
        */
        public function fixupTwoCapitals() {
            $sql = '
                INSERT INTO localities
                (region_id, code, name, type)
                VALUES (:rid, :code, :name, :type)
            ';
            
            $stmt = $this->_db->prepare($sql);
            
            $moscow = array(
                /* Узнаем id записи Московской области по коду */
                ':rid'  => $this->_getRegionId(50),
                ':code' => 77,
                ':name' => 'Москва',
                ':type' => 'г'
            );
            
            $peter = array(
                ':rid'  => $this->_getRegionId(47),
                ':code' => 78,
                ':name' => 'Санкт-Петербург',
                ':type' => 'г'
            );
            
            echo 'Inserting two capitals in cities list...' . PHP_EOL;
            
            $stmt->execute($moscow);
            $stmt->execute($peter);
        }
        
        protected function _insertObject(stdClass $obj, $throw_e = false) {
            $data = array(':code' => $obj->code);
            
            if (self::OBJ_TYPE_REGION === $obj->type)
            {
                //$this->_region_abbrs[$obj->abbr] = true;
                
                $data[':name'] = $this->_expandAbbr($obj->abbr, $obj->name);
                
                $this->_insert_region->execute($data);
                
                $region_id = $this->_db->lastInsertId();
                $this->_setRegionId($obj->code, $region_id);
            } 
            else
            {
                //$this->_local_abbrs[$obj->abbr] = true;
                
                $region_id = $this->_getRegionId($obj->region_code);
                
                if (false === $region_id)
                {
                    if ($throw_e) {
                        throw new Exception('Не найден id для региона с кодом ' . $code);
                    }
                    
                    return 'delay_object';
                }
                
                $data[':name']      = $obj->name;
                $data[':type']      = $obj->abbr;
                $data[':region_id'] = $region_id;
                
                $this->_insert_locality->execute($data);
            }
        }
        
        protected function _expandAbbr($abbr, $name) {
            $map = & $this->_region_abbrs_map;
            
            switch ($abbr)
            {
                case 'Респ':
                    $name = $map[$abbr] . ' ' . $name;
                    break;
                    
                case 'край':
                case 'обл':
                case 'Аобл':
                case 'АО':
                    $name = $name . ' ' . $map[$abbr];
                    break;
                
                default:
                    $name = $name . ' (' . $abbr . ')';
            }
            
            return $name;
        }
        
        protected function _setRegionId($code, $id) {
            $this->_regions_lookup[$code] = $id;
        }
        
        protected function _getRegionId($code) {
            if (!isset($this->_regions_lookup[$code])) {
                return false;
            }
            
            return $this->_regions_lookup[$code];
        }
        
        protected function _parseRecord(array $record) {
            $name = $record[0];
            $name = $this->_convertEncoding($name);
            $name = trim($name);
            
            $abbr = $record[1];
            $abbr = $this->_convertEncoding($abbr);
            $abbr = trim($abbr);
            
            $code = $record[2];
            $code = $this->_explodeCode($code);
            
            if ('000' != $code['locality'])
            {
                $type = self::OBJ_TYPE_LOCALITY;
                $obj_code = $code['locality'];
            }
            elseif ('000' != $code['city'])
            {
                $type = self::OBJ_TYPE_CITY;
                $obj_code = $code['city'];
            }
            elseif ('000' != $code['district'])
            {
                /* Районы нам не нужны */
                return 'skip_record';
            }
            else
            {
                $type = self::OBJ_TYPE_REGION;
                $obj_code = $code['region'];
            }                                   
            
            $obj = array(
                'type' => $type,
                'code' => $obj_code,
                'name' => $name,
                'abbr' => $abbr
            );
            
            if (self::OBJ_TYPE_REGION != $type) {
                $obj['region_code'] = $code['region'];
            }
            
            return (object) $obj;
        }
        
        protected function _convertEncoding($string, $to = 'UTF-8', $from = 'CP866') {
            return mb_convert_encoding($string, $to, $from);
        }
        
        protected function _explodeCode($code) {
            $num_matches = preg_match($this->_code_regex, $code, $matches);
            
            if (!$num_matches) {
                throw new Exception('Не удалось разобрать код ' . $code);
            }
            
            $result = array(
                'region'   => $matches[1],
                'district' => $matches[2],
                'city'     => $matches[3],
                'locality' => $matches[4]
            );
            
            return $result;
        }
    }

?>