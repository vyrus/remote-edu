<?php $this->title = $this->discipline['title'] ?>
<h1 class="title_discipline"><?php echo $this->discipline['title'] ?></h1>
<p style="margin: 1em 0;">
    <a href="<?php echo $this->_links->get('messages.send') . $this->discipline['responsible_teacher']; ?>">Написать сообщение преподавателю</a>
</p>
<?php
    foreach ($this->sections as $s):
        $s = (object) $s;
?>
    <h2 class="title_section">Раздел <?php echo $s->number . '. ' . $s->title ?></h2>
<?php
        $materials = array();
        if (!empty($this->materials[$s->section_id])) :
            foreach ($this->materials[$s->section_id] as $m) {
                $materials[$m['type']][] = $m;
            }
            foreach ($materials as $m_title => $m_data) {
                switch ($m_title) {
                case 'lecture':
                    $title = 'Лекционный материал';
                    break;
                case 'practice':
                    $title = 'Практические задания';
                    break;
                case 'control':
                    $title = 'Контрольные задания';
                    break;
                }
?>
    <h3 class="title_materials"><?php echo $title; ?></h3>
    <ul class="materials">
<?php
                foreach ($m_data as $m) {
                    switch ($m['state']) {
                        case 'last':
                            $state = 'state_last';
                            break;
                        case 'downloaded':
                            $state = 'state_downloaded';
                            break;
                        default:
                            $state = 'state_non_downloaded';
                            break;
                    }
?>
        <li class="<?php echo $state; ?>">
            <a href="<?php echo $this->_links->get('materials.download', array('material_id' => $m['id'])) ?>"><?php echo $m['description'] ?></a>
        </li>
<?php
                }
?>
    </ul>
<?php
            }
            if (count($this->tests)): 
                $auth = Resources::getInstance()->auth;
                $was_test_caption = false;
                foreach ($this->tests as $test): 
                    if ($test['section_id'] == $s->section_id) :
                        if (!$was_test_caption): 
                            $was_test_caption = true; ?>
                            <h3 class="title_materials">Тестовые задания</h3>
                        <?php endif; ?>
                        <ul class="materials">
                            <li class="test">
                                <a href="<?php echo $this->_links->get('tests.examination', 
                                    array('test_id'    => $test['test_id'],
                                          'section_id' => $test['section_id'],
                                          'code' => $auth->getTestSecurityCode($this->user_id, $test['test_id'], $test['section_id'])
                                    )
                                ) ?>"><?php echo $test['title_test'] ?></a>
                            </li>
                        </ul>
                                  
                   <?php endif;
                endforeach;
            endif; 
        endif;  // проверка есть ли материалы в разделе
    endforeach; // итерация по разделам
?>
