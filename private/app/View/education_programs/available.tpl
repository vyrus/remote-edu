<?php if (!empty($this->programs)): ?>
<h3>Доступные программы:</h3>
<ul>
    <?php foreach ($this->programs as $p): ?>
    <li>
        <?php echo $p['title'] ?>
        <?php
            /* Если программа бесплатная или оплачена полностью - выводим сообщение */
            if (Model_Education_Programs::PAID_TYPE_FREE == $p['paid_type']
                ||
                ($p['cost'] - $p['total_sum']) <= 0)
            {
              echo '(доступна)';
            }
            else {
              echo '(внесено ' . $p['total_sum'] . 'р. из ' .  $p['cost'] . ')';
            }
        ?>
        <ul>
            <?php foreach ($p['disciplines'] as $d): ?>
              <?php $d = (object) $d ?>
              <li>
              <?php if (true == $d->active) : ?>
                <a href="<?php echo $this->_links->get('materials.show', array('discipline_id' => $d->discipline_id,
                                                                                'app_id'        => $p['app_id'])) ?>">
                  <?php echo $d->title ?>
                </a>
              <?php else : ?>
                <?php echo $d->title.' (оплачено '.$d->disc_paid.'р. из '.$d->disc_cost.')' ?>
                
              <?php endif; ?>
              </li>
            <?php endforeach; ?>
        </ul>
    </li>
    </br>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($this->disciplines)): ?>
<h3>Доступные дисциплины:</h3>
<ul>
    <?php foreach ($this->disciplines as $d): ?>
      <li>
        <?php if (true === $d['active']) : ?>
          <a href="<?php echo $this->_links->get('materials.show', array('discipline_id' => $d['discipline_id'],
                                                                        'app_id'        => $d['app_id'])) ?>"><?php echo $d['title'] ?></a>&nbsp;(доступна)
        <?php else : ?>
          <?php echo $d['title'].' (оплачено ' . $d['total_sum'] . 'р. из '.$d['disc_sum'] .')' ?>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (empty($this->programs) && empty($this->disciplines)): ?>
<b>Вами не выбрано ни одной программы для обучения:</b>
<ol>
    <li>В разделе <a href="<?php echo $this->_links->get('student.apply') ?>" target=blank>"Новый курс"</a> выберите интересующую Вас учебную программу;</li>
    <li>Нажмите кнопку "Подать заявку", и подайте заявку на обучение по выбранной демонстрационной программе.<br>Статус заявки на обучение по выбранному направлению, можно посмотреть в разделе <a href="<?php echo $this->_links->get('student.applications') ?>" target=blank>"Мои заявки"</a>;</li>
    <li>После этого Вы сможете получить доступ к интересующим Вас материалам в разделе <a href="<?php echo $this->_links->get('student.programs') ?>" target=blank>"Мои курсы"</a>.*</li>
</ol>
<?php endif; ?>
