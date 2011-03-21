<?php
    echo 'asdasd';
    $directions     = $this->directions;
    $courses        = $this->courses;
    $disciplines    = $this->disciplines;
    $sections       = $this->sections;
    $materials      = $this->materials;
    
    //var_dump($materials);

    foreach ($sections as $i => $disciplineSections) {
        foreach ($disciplineSections as $j => $section) {
            $sections[$i][$j]['title'] = 'Раздел ' . $section['number'] . ': ' . $section['title'];
        }
    }
?>
<script type="text/javascript">
    var PROGRAMS            = [];
    PROGRAMS['direction']   = [<?php $delimiter = ''; foreach ($directions as $i => $direction): echo $delimiter; ?>{'id':<?php echo $direction['program_id']; ?>,'title':'<?php echo $direction["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];
    PROGRAMS['course']      = [<?php $delimiter = ''; foreach ($courses as $i => $course): echo $delimiter; ?>{'id':<?php echo $course['program_id']; ?>,'title':'<?php echo $course["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];

    var DISCIPLINES = [];
    <?php foreach ($disciplines as $i => $specialityDiscipline): ?>
    DISCIPLINES[<?php echo $specialityDiscipline[0]['program_id']; ?>] = [<?php $delimiter = ""; foreach ($specialityDiscipline as $j => $discipline): echo $delimiter ?>{"id":<?php echo $discipline['discipline_id']; ?>,"title":"<?php echo $discipline['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
    <?php endforeach; ?>

    var SECTIONS     = [];
    <?php foreach ($sections as $i => $disciplineSection): ?>
    SECTIONS[<?php echo $disciplineSection[0]['discipline_id']; ?>] = [<?php $delimiter = ""; foreach ($disciplineSection as $j => $section): echo $delimiter ?>{"id":<?php echo $section['section_id']; ?>,"title":"<?php echo $section['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
    <?php endforeach; ?>
    
    var MATERIALS   =   [];
    <?php foreach ($materials as $i => $sectionMaterial): ?>
    MATERIALS[<?php echo $i; ?>] =
        [<?php $delimiter = ""; foreach ($sectionMaterial as $j => $material): echo $delimiter ?>{"id":<?php echo $material['id']; ?>,"title":"<?php echo $material['description'].' ['.$material['type_rus'].']'; ?>"}<?php ; $delimiter =","; endforeach; ?>];
    <?php endforeach; ?>   

    function switchProgramsType () {
        clearSelect (programsSelect);
        clearSelect (disciplinesSelect);
        clearSelect (sectionsSelect);
        clearSelect (materialsSelect);

        for (var i = 0; i < PROGRAMS[programsTypeSelect.value].length; i++) {
            programsSelect.options[i] = new Option (PROGRAMS[programsTypeSelect.value][i].title, PROGRAMS[programsTypeSelect.value][i].id);
        }
    }
    
    // работа с блоком направлений/курсов

    function addProgram () {
        window.location = '<?php echo $this->_links->get('programs.add') ?>' +
                          programsTypeSelect.value + '/';
    }

    function removeProgram () {
        if (programsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать ' + ((programsTypeSelect.value == 'direction') ? ('направление') : ('курсы')));
            return;
        }

        window.location = '<?php echo $this->_links->get('programs.remove') ?>' + 
                          programsTypeSelect.value + '/' + 
                          programsSelect.options[programsSelect.selectedIndex].value + '/';
    }

    function editProgram () {
        if (programsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать ' + ((programsTypeSelect.value == 'direction') ? ('направление') : ('курсы')));
            return;
        }

        window.location = '<?php echo $this->_links->get('programs.edit') ?>' + 
                          programsTypeSelect.value + '/' + 
                          programsSelect.options[programsSelect.selectedIndex].value + '/';
    }
    
    function saveProgramOrder() {
        var programOrder = '';
        for (var i = 0; i < programsSelect.length; i++) {
            if (i > 0) {
                programOrder += ',';
            }
            programOrder += programsSelect.options[i].value;
        }
        document.forms[0].programOrderInfo.value = programOrder;
        document.forms[0].action = '<?php echo $this->_links->get('programs.save-order') ?>';
        document.forms[0].submit();
    }

    // работа с блоком дисциплин

    function addDiscipline () {
        if (programsTypeSelect.value == 'course') {
            alert ('Курсы не содержат дисциплин');
            return
        }

        if (programsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать направление');
            return;
        }

        window.location = '<?php echo $this->_links->get('disciplines.add') ?>' + 
                           programsSelect.options[programsSelect.selectedIndex].value + '/';
    }

    function removeDiscipline () {
        if (disciplinesSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать дисциплину');
            return;
        }

        window.location = '<?php echo $this->_links->get('disciplines.remove') ?>' + 
                          disciplinesSelect.options[disciplinesSelect.selectedIndex].value + '/';
    }

    function editDiscipline () {
        if (disciplinesSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать дисциплину');
            return;
        }

        window.location = '<?php echo $this->_links->get('disciplines.edit') ?>' + 
                          disciplinesSelect.options[disciplinesSelect.selectedIndex].value + '/';
    }
    
    function saveDisciplineOrder() {
        var disciplineOrder = '';
        for (var i = 0; i < disciplinesSelect.length; i++) {
            if (i > 0) {
                disciplineOrder += ',';
            }
            disciplineOrder += disciplinesSelect.options[i].value;
        }
        document.forms[0].disciplineOrderInfo.value = disciplineOrder;
        document.forms[0].action = '<?php echo $this->_links->get('disciplines.save-order') ?>';
        document.forms[0].submit();
    } 
    
     // работа с блоком разделов(секций) 

    function addSection () {
        if (disciplinesSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать дисциплину');
            return;
        }

        window.location = '<?php echo $this->_links->get('sections.add') ?>' + 
                          disciplinesSelect.options[disciplinesSelect.selectedIndex].value + '/';
    }

    function removeSection () {
        if (sectionsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать раздел');
            return;
        }

        window.location = '<?php echo $this->_links->get('sections.remove') ?>' + 
                          sectionsSelect.options[sectionsSelect.selectedIndex].value + '/';
    }

    function editSection () {
        if (sectionsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать раздел');
            return;
        }

        window.location = '<?php echo $this->_links->get('sections.edit') ?>' + 
                          sectionsSelect.options[sectionsSelect.selectedIndex].value + '/';
    }
    
    function saveSectionOrder() {
        var sectionOrder = '';
        for (var i = 0; i < sectionsSelect.length; i++) {
            if (i > 0) {
                sectionOrder += ',';
            }
            sectionOrder += sectionsSelect.options[i].value;
        }
        document.forms[0].sectionOrderInfo.value = sectionOrder;
        document.forms[0].action = '<?php echo $this->_links->get('sections.save-order') ?>';
        document.forms[0].submit();
    }
    
    // работа с блоком материалов
    
    // тут не понятно, как лучше-то реализовать
    function addMaterial() {
        /*
        if (sectionsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать дисциплину');
            return;
        }
        */
        window.location = '<?php echo $this->_links->get('materials.admin.upload') ?>' ;//+ 
                          //sectionsSelect.options[sectionsSelect.selectedIndex].value + '/';
    }
    
    function removeMaterial () {
        if (materialsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать один/несколько материалов');
            return;
        }
        
        var materialDelete = '';
        var first = true;
        for (var i = 0; i < materialsSelect.length; i++) {
            if (materialsSelect.options[i].selected) {
                if (first) {
                    first = false;
                } else {
                    materialDelete += ',';
                }
                materialDelete += materialsSelect.options[i].value;
            }
        }
        
        document.forms[0].materialDeleteInfo.value = materialDelete;
        document.forms[0].action = '<?php echo $this->_links->get('materials.admin.remove') ?>';
        document.forms[0].submit();

    }
    
    function editMaterial() {
        if (materialsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать материал');
            return;
        }

        window.location = '<?php echo $this->_links->get('materials.admin.edit') ?>' + 
                          materialsSelect.options[materialsSelect.selectedIndex].value + '/';
    }
    
    function saveMaterialOrder() {
        var materialOrder = '';
        for (var i = 0; i < materialsSelect.length; i++) {
            if (i > 0) {
                materialOrder += ',';
            }
            materialOrder += materialsSelect.options[i].value;
        }
        document.forms[0].materialOrderInfo.value = materialOrder;
        document.forms[0].action = '<?php echo $this->_links->get('materials.admin.save-order') ?>';
        document.forms[0].submit();
    }
    
    function downloadMaterial() {
        if (materialsSelect.selectedIndex == -1) {
            alert ('Необходимо выбрать материал');
            return;
        }
        
        /*for (var i=0; i < materialsSelect.length; i++) {
            if (materialsSelect.options[i].selected) {
                // что же тут дкелать???   
            }
        }*/
        
        window.location = '<?php echo $this->_links->get('materials.download') ?>' + 
                          materialsSelect.options[materialsSelect.selectedIndex].value + '/';      
    }

    // общие функции

    function clearSelect (select) {
        while (select.firstChild) {
            select.removeChild (select.firstChild);
        }
    }

    function updateSelect (parentSelect, select, items, clear) {
        for (var i = 0; i < clear.length; i++) {
            clearSelect (clear[i]);
        }

        var selectItems = items[parentSelect.options[parentSelect.selectedIndex].value];
        if (selectItems) {
            for (var i = 0; i < selectItems.length; i++) {
                select.options[i] = new Option (selectItems[i].title, selectItems[i].id);
            }
        }
    }
    
    // функции подъема строки в выборбоксе вверх и вниз
    
    function down(select) {
        if (select.selectedIndex < select.length) {
            var tempText = select.options[select.selectedIndex + 1].text;
            var tempValue = select.options[select.selectedIndex + 1].value;
            select.options[select.selectedIndex + 1].text = select.options[select.selectedIndex].text;
            select.options[select.selectedIndex + 1].value = select.options[select.selectedIndex].value;
            select.options[select.selectedIndex].text = tempText;
            select.options[select.selectedIndex].value = tempValue;
            select.selectedIndex = select.selectedIndex + 1;
        }
    }
    
    function up(select) {
        if (select.selectedIndex > 0) {
            var tempText = select.options[select.selectedIndex - 1].text;
            var tempValue = select.options[select.selectedIndex - 1].value;
            select.options[select.selectedIndex - 1].text = select.options[select.selectedIndex].text;
            select.options[select.selectedIndex - 1].value = select.options[select.selectedIndex].value;
            select.options[select.selectedIndex].text = tempText;
            select.options[select.selectedIndex].value = tempValue;
            select.selectedIndex = select.selectedIndex - 1;
        }
    }

</script>
<style type="text/css">
    input.addButton,
    input.editButton,
    input.removeButton,
    input.upButton,
    input.downButton {
        border                      : 1px solid #AAAAAA;
        background-color            : #FFFFFF;
        width                       : 25px;
        height                      : 25px;
    }
</style>
<form method="post">
<input type="hidden" id="programOrderInfo" name="programOrderInfo">
<input type="hidden" id="disciplineOrderInfo" name="disciplineOrderInfo">
<input type="hidden" id="sectionOrderInfo" name="sectionOrderInfo">
<input type="hidden" id="materialOrderInfo" name="materialOrderInfo">
<input type="hidden" id="materialDeleteInfo" name="materialDeleteInfo">
<table cellspacing="0" cellpadding="0">
<tr><td><select id="programsTypeSelect" onchange="switchProgramsType ();"><option value="direction">Направления</option><option value="course">Курсы</option></select></td><td>Дисциплины</td><td>Разделы</td></tr>
<tr>
<td><select id="programsSelect" class="educationProgramItems" size="15" onclick="updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect, sectionsSelect, materialsSelect]);">
<?php foreach ($directions as $i => $direction): ?>
<option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
<?php endforeach; ?>
</select></td>
<td><select id="disciplinesSelect" class="educationProgramItems" size="15" onchange="updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect, materialsSelect]);"></select></td>
<td><select id="sectionsSelect" class="educationProgramItems" size="15" onchange="updateSelect (sectionsSelect, materialsSelect, MATERIALS, [materialsSelect]);"></select></td>
</tr>
<tr>
    <td>
        <input type="button" class="addButton" value="&#x002B;" onclick="addProgram ();">
        <input type="button" class="editButton" value="&#x270E;" onclick="editProgram ();">
        <input type="button" class="removeButton" value="&#x2212;" onclick="removeProgram ();">&nbsp;
        <input type="button" class="upButton" value="&uarr;" onclick="up(programsSelect)">
        <input type="button" class="downButton" value="&darr;" onclick="down(programsSelect)">
        <input type="button" value="Сохранить" onclick="saveProgramOrder()">
    </td>
    <td>
        <input type="button" class="addButton" value="&#x002B;" onclick="addDiscipline ();">
        <input type="button" class="editButton" value="&#x270E;" onclick="editDiscipline ();">
        <input type="button" class="removeButton" value="&#x2212;" onclick="removeDiscipline ();">&nbsp;
        <input type="button" class="upButton" value="&uarr;" onclick="up(disciplinesSelect)">
        <input type="button" class="downButton" value="&darr;" onclick="down(disciplinesSelect)">
        <input type="button" value="Сохранить" onclick="saveDisciplineOrder()">
    </td>
    <td>
        <input type="button" class="addButton" value="&#x002B;" onclick="addSection ();">
        <input type="button" class="editButton" value="&#x270E;" onclick="editSection ();">
        <input type="button" class="removeButton" value="&#x2212;" onclick="removeSection ();">&nbsp;
        <input type="button" class="upButton" value="&uarr;" onclick="up(sectionsSelect)">
        <input type="button" class="downButton" value="&darr;" onclick="down(sectionsSelect)">
        <input type="button" value="Сохранить" onclick="saveSectionOrder()">
    </td>
</tr>
</table>
<table>
<tr>
    <td>
        Материалы
    </td>
</tr>
<tr>
    <td>
        <select multiple="on" id="materialsSelect" class="educationProgramItems" size="15" style="min-width: 800px;"></select>
    </td>
</tr>
<tr>
    <td>
        <input type="button" class="addButton" value="&#x002B;" onclick="addMaterial ();">
        <input type="button" class="editButton" value="&#x270E;" onclick="editMaterial ();">
        <input type="button" class="removeButton" value="&#x2212;" onclick="removeMaterial ();">&nbsp;
        <input type="button" class="upButton" value="&uarr;" onclick="up(materialsSelect)">
        <input type="button" class="downButton" value="&darr;" onclick="down(materialsSelect)">
        <input type="button" value="Сохранить" onclick="saveMaterialOrder()">
        <input type="button" value="Загрузить" onclick="downloadMaterial()">
    </td>
</tr>
</table>
</form>
<script type="text/javascript">
    var programsTypeSelect    = document.getElementById ('programsTypeSelect');
    var programsSelect        = document.getElementById ('programsSelect');
    var disciplinesSelect     = document.getElementById ('disciplinesSelect');
    var sectionsSelect        = document.getElementById ('sectionsSelect');
    var materialsSelect       = document.getElementById ('materialsSelect');
</script>