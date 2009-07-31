<?php

    /* $Id$ */
           
    require_once '../../init.php';

    $dbf = './KLADR.dbf';
    $db  = $config['db'];
    
    $conv = Converter_Kladr::create($dbf, $db['dsn'], $db['user'], $db['passwd'])
            ->convert();
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
                                      ([0-9]{2}) # СС – код субъекта Российской Федерации (региона)
                                      ([0-9]{3}) # РРР – код района
                                      ([0-9]{3}) # ГГГ – код города
                                      ([0-9]{3}) # ППП – код населенного пункта
                                      ([0-9]{2}) # АА – признак актуальности наименования адресного объекта
                                  $/x';
                                  
        protected $_regions_lookup = array();
        
        protected $_region_abbrs = array();
        
        protected $_local_abbrs = array();
        
        public function __construct($file_name, $dsn, $user, $passwd) {
            if (false === ($this->_link = dbase_open($file_name, 0))) {
                throw new Exception('Не удалось открыть файл ' . $file_name);
            }
            
            $options = array(
                /* Будет кидать исключения при ошибках */
                Db_Pdo::ATTR_ERRMODE => Db_Pdo::ERRMODE_EXCEPTION
            );

            $this->_db = Db_Pdo::create($dsn, $user, $passwd, $options);
            
            $sql = '
                INSERT INTO regions
                (code, name, type)
                VALUES (:code, :name, :type)
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
        }
        
        protected function _insertObject(stdClass $obj, $throw_e = false) {
            $data = array(
                ':code' => $obj->code,
                ':name' => $obj->name,
                ':type' => $obj->abbr
            );
            
            if (self::OBJ_TYPE_REGION === $obj->type)
            {
                //$this->_region_abbrs[$obj->abbr] = true;
                
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
                
                $data[':region_id'] = $region_id;
                
                $this->_insert_locality->execute($data);
            }
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
        
        protected function _convertEncoding($string, $to = 'Windows-1251', $from = 'CP866') {
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