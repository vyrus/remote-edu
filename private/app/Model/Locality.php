<?php
    
    /* $Id$ */

    /**
    * Модель для работы со списком населённых пунктов.
    */
    class Model_Locality extends Model_Base {
        /**
        * Список используемых в базе сокращений и их расшифровка.
        * 
        * @var array
        */
        protected static $_abbrs_map = array(
            'аал'        => 'аал',
            'арбан'      => 'арбан',
            'аул'        => 'аул',
            'волость'    => 'волость',
            'высел'      => 'высел',
            'г'          => 'город',
            'городок'    => 'городок',
            'д'          => 'деревня',
            'дп'         => 'дачный поселок',
            'ж/д_будка'  => 'железнодорожная будка',
            'ж/д_казарм' => 'железнодорожная казарма',
            'ж/д_оп'     => 'ж/д остановочный (обгонный) пункт',
            'ж/д_платф'  => 'железнодорожная платформа',
            'ж/д_пост'   => 'железнодорожный пост',
            'ж/д_рзд'    => 'железнодорожный разъезд',
            'ж/д_ст'     => 'железнодорожная станция',
            'заимка'     => 'заимка',
            'казарма'    => 'казарма',
            'кв-л'       => 'квартал',
            'кордон'     => 'кордон',
            'кп'         => 'курортный поселок',
            'м'          => 'местечко',
            'мкр'        => 'микрорайон',
            'нп'         => 'населенный пункт',
            'остров'     => 'остров',
            'п'          => 'поселок сельского типа',
            'п/о'        => 'почтовое отделение',
            'п/р'        => 'планировочный район',
            'п/ст'       => 'поселок и(при) станция(и)',
            'пгт'        => 'поселок городского типа',
            'погост'     => 'погост',
            'починок'    => 'починок',
            'промзона'   => 'промышленная зона',
            'рзд'        => 'разъезд',
            'рп'         => 'рабочий (заводской) поселок',
            'с'          => 'село',
            'с/а'        => 'сельская администрация',
            'с/о'        => 'сельский округ',
            'с/п'        => 'сельское поселение',
            'с/с'        => 'сельсовет',
            'сл'         => 'слобода',
            /**
            * @todo What's this? o_O
            */
            'снт'        => 'снт',
            'ст'         => 'станция',
            'ст-ца'      => 'станица',
            'тер'        => 'территория',
            'у'          => 'улус',
            'х'          => 'хутор'
        );
        
        /**
        * Создание экземпляра модели.
        * 
        * @return Model_Locality
        */
        public static function create() {
            return new self();
        }
        
        /**
        * Нахождение населённых пунктов по части названия.
        * 
        * @param  string $name      Название (может быть неполным).
        * @param  int    $region_id Идентификатор родительского региона.
        * @return array Список найденных названий.
        */
        public function findLike($name, $region_id) {
             $sql = '
                SELECT locality_id AS id, name, type
                FROM ' . $this->_tables['localities']. ' 
                WHERE name LIKE :name AND
                      region_id = :region_id
                ORDER BY CHAR_LENGTH(name) ASC
                LIMIT 10
            ';
            
            $values = array(
                ':name'      => '%' . $name . '%',
                ':region_id' => $region_id
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            
            $localities = $stmt->fetchAll(Db_Pdo::FETCH_ASSOC);
            
            return $localities;
        }
        
        /**
        * Проверка существования записи о населённом пункте по идентификатору.
        * 
        * @param  int $id
        * @return boolean
        */
        public function exists($id) {
            $sql = '
                SELECT COUNT(*)
                FROM ' . $this->_tables['localities']. '
                WHERE locality_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($id));
            
            $count = $stmt->fetchColumn();
            return $count > 0;
        }
        
        /**
        * Получение полного названия населённого пункта (с расшифровкой 
        * сокращения, если это не город).
        * 
        * @param  int $locality_id Идентификатор населённого пункта.
        * @return string
        */
        public function getFullName($locality_id) {
            $sql = '
                SELECT name, type
                FROM ' . $this->_tables['localities']. '
                WHERE locality_id = ?
            ';
            
            $stmt = $this->prepare($sql);
            $stmt->execute(array($locality_id));
            
            if (!$stmt->rowCount()) {
                return false;
            }
            
            $loc = $stmt->fetch(Db_Pdo::FETCH_ASSOC);
            
            $name = $this->expandName($loc['name'], $loc['type']);
            return $name;
        }
        
        /**
        * Расшифровка сокращений, используемых в названия населённых пунктов. 
        * Если тип населённого пункта не город, то к названию будет добавлена 
        * расшифрока сокращения.
        * 
        * @param  $name Название.
        * @param  $type Тип (сокращение).
        * @return string
        */
        public static function expandName($name, $type) {
            if (!isset(self::$_abbrs_map[$type])) {
                return $name;
            }
            
            $name .= (
                'г' == $type ? 
                    '' : 
                    ' (' . self::$_abbrs_map[$type] . ')'
            );
            
            return $name;
        }
    }

?>