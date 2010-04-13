<?php $b  = $this->base_profile ?>
<?php $p  = $this->ex_profile->passport ?>
<?php $ed = $this->ex_profile->edu_doc ?>
<?php $ph = $this->ex_profile->phones ?>

<?php

    function show($var, $delimiter = null) {
        if (is_array($var))
        {
            $empty = true;
            
            foreach ($var as $elem) {
                $empty &= empty($elem);
                
                if (!$empty) {
                    break;
                }
            }           
            
            $var = implode($delimiter, $var);
        } 
        else {
            $empty = empty($var);
        }
        
        echo (!$empty ? $var : '&mdash;');
    }

?>

<div class="student_profile">
    <dl>
        <dt>ФИО</dt>
        <dd><?php show(array($b->surname, $b->name, $b->patronymic), ' ') ?></dd>
        
        <dt>Дата рождения</dt>
        <dd><?php show($p->birthday) ?></dd>
    </dl>
</div>

<h3>Паспорт</h3>
<?php $region = Model_Region::create()->getName($p->regionId) ?>    
<?php $city = Model_Locality::create()->getFullName($p->cityId) ?>    

<div class="student_profile">
    <dl>
        <dt>Серия</dt>
        <dd><?php show($p->series) ?></dd>
        
        <dt>Номер</dt>
        <dd><?php show($p->number) ?></dd>
        
        <dt>Выдан</dt>
        <dd><?php show(array($p->givenDate, $p->givenBy), ', ') ?></dd>
        
        <dt>Прописка</dt>
        <dd><?php show(array($region, $city, $p->street, $p->house, $p->flat), ', ') ?></dd>
    </dl>
</div>

<h3>Образование</h3>
<?php $map = array(Model_User::DOC_TYPE_EMPTY          => '',
                   Model_User::DOC_TYPE_DIPLOMA_HIGH   => 'Диплом о высшем образовании',
                   Model_User::DOC_TYPE_DIPLOMA_MEDIUM => 'Диплом о среднем образовании', 
                   Model_User::DOC_TYPE_CUSTOM         => 'Иное') ?>
                   
<?php $custom = Model_User::DOC_TYPE_CUSTOM ?>
<?php $type = ($custom === $ed->type ?  $ed->customType : $map[$ed->type]) ?>

<div class="student_profile">
    <dl>
        <dt>Документ</dt>
        <dd><?php show($type) ?></dd>
        
        <dt>Номер документа</dt>
        <dd><?php show($ed->number) ?></dd>
        
        <dt>Год окончания</dt>
        <dd><?php show($ed->exitYear) ?></dd>
        
        <dt>Специальность</dt>
        <dd><?php show($ed->speciality) ?></dd>
        
        <dt>Квалификация</dt>
        <dd><?php show($ed->qualification) ?></dd>
    </dl>
</div>

<h3>Телефоны</h3>

<div class="student_profile">
    <dl>
        <dt>Мобильный</dt>
        <dd><?php show($ph->mobile) ?></dd>
        
        <dt>Стационарный</dt>
        <dd><?php show($ph->stationary) ?></dd>
    </dl>
</div>