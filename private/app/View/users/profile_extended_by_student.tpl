<?php $this->title = 'Расширенный профиль слушателя' ?>
<?php $form = $this->form ?>

<script type="text/javascript">
    function time() {
        return new Date().getTime();
    }
    
    function getRegionId() {
        return $('#region_id').attr('value');
    }
                        
    var custom_type_idx = 3;
                               
    $(function() {
        if (undefined == $('#region_id').attr('value')) {
            $('#city').attr('disabled', 'disabled');
        }
        
        if (custom_type_idx != $('#doc_type')[0].selectedIndex) {
            $('#doc_custom_field').hide();
        }
        
        $('#birthday').datepicker({
            dateFormat: 'dd.mm.yy',
            maxDate: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        });
        
        $('#passport_given_date').datepicker({
            dateFormat: 'dd.mm.yy',
            maxDate: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        });
        
        $('#region').autocomplete({
            serviceUrl: '/ajax/autocomplete/region/',
            params: {
                timestamp: time
            },
            onSelect: function(value, data) {
                $('#region_id').attr('value', data);
                $('#city').attr('disabled', false);
            }
        });
        
        $('#city').autocomplete({
            serviceUrl: '/ajax/autocomplete/city/',
            params: {
                region_id: getRegionId,
                timestamp: time
            },
            onSelect: function(value, data) {
                $('#city_id').attr('value', data);
            }
        });
        
        $('#doc_type').change(function () {
            if (custom_type_idx == $('#doc_type')[0].selectedIndex) {
                $('#doc_custom_field').show();
            } else {
                $('#doc_custom_field').hide();
            }
        });
    });
</script>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php /* Фамилия */ $field = $form->surname ?>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Фамилия:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
        
    <?php /* Имя */ $field = $form->name ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Имя:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
        
    <?php /* Отчество */ $field = $form->patronymic ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Отчество:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Дата рождения */ $field = $form->birthday ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Дата рождения:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Серия паспорта */ $field = $form->passport_series ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Серия паспорта:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>

    <?php /* Номер паспорта */ $field = $form->passport_number ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Номер паспорта:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Кем выдан паспорт */ $field = $form->passport_given_by ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Кем выдан паспорт:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>

    <?php /* Дата выдачи паспорта */ $field = $form->passport_given_date ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Дата выдачи паспорта:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>

    <?php /* Область/край */ $field = $form->region ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>"/> 
      <label for="<?php echo $field->name ?>">Область/край:</label>
      
      <?php $field = $form->region_id ?> 
      <input type="hidden" name="<?php echo $field->name ?>" id="region_id" value="<?php echo $field->value ?>" />
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Город */ $field = $form->city ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Город:</label>
      
      <?php $field = $form->city_id ?> 
      <input type="hidden" name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" />
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Улица */ $field = $form->street ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Улица:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>        
    
    <?php /* Дом/корпус */ $field = $form->house ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Дом/корпус:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>        
    
    <?php /* Квартира/комната */ $field = $form->flat ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Квартира/комната:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>        
    
    <?php /* Вид документа об образовании */ $field = $form->doc_type ?>
    <br>
    <div class="field">
      <select name="<?php echo $field->name ?>" id="<?php echo $field->name ?>">
        <?php $values = array(
                    Model_User::DOC_TYPE_EMPTY          => '',
                    Model_User::DOC_TYPE_DIPLOMA_HIGH   => 'Диплом о высшем образовании',
                    Model_User::DOC_TYPE_DIPLOMA_MEDIUM => 'Диплом о среднем образовании', 
                    Model_User::DOC_TYPE_CUSTOM         => 'Иное'
              )
        ?>
        <?php foreach ($values as $value => $title): ?>
            <option value="<?php echo $value ?>"<?php echo ($value == $field->value ? ' selected' : '') ?>>
                <?php echo $title ?>
            </option>
        <?php endforeach ?>
      </select> 
      <label for="<?php echo $field->name ?>">Вид документа:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Иной вид документа */ $field = $form->doc_custom_type ?>
    <br>
    <div class="field" id="doc_custom_field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Иное:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Номер документа */ $field = $form->doc_number ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Номер документа:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Год окончания */ $field = $form->exit_year ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Год окончания:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Специальность */ $field = $form->speciality ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Специальность:</label>
    </div>                      
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Квалификация */ $field = $form->qualification ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Квалификация:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Мобильный телефон */ $field = $form->phone_mobile ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Мобильный телефон:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Стационарный телефон */ $field = $form->phone_stationary ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      <label for="<?php echo $field->name ?>">Стационарный телефон:</label>
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <br>
    <div class="field">
        <input type="submit" value="Сохранить" />
    </div>
</div>
</form>