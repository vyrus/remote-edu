<?php
	$directions 	= $this->directions;
	$courses		= $this->courses;
	$disciplines	= $this->disciplines;
	$sections		= $this->sections;

	foreach ($sections as $i => $disciplineSections) {
		foreach ($disciplineSections as $j => $section) {
			$sections[$i][$j]['title'] = 'Раздел ' . $section['number'] . ': ' . $section['title'];
		}
	}
?>
<script type="text/javascript">
	var PROGRAMS			= [];
	PROGRAMS['direction']	= [<?php $delimiter = ''; foreach ($directions as $i => $direction): echo $delimiter; ?>{'id':<?php echo $direction['program_id']; ?>,'title':'<?php echo $direction["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];
	PROGRAMS['course'] 		= [<?php $delimiter = ''; foreach ($courses as $i => $course): echo $delimiter; ?>{'id':<?php echo $course['program_id']; ?>,'title':'<?php echo $course["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];

	var DISCIPLINES = [];
	<?php foreach ($disciplines as $i => $specialityDiscipline): ?>
	DISCIPLINES[<?php echo $specialityDiscipline[0]['program_id']; ?>] = [<?php $delimiter = ""; foreach ($specialityDiscipline as $j => $discipline): echo $delimiter ?>{"id":<?php echo $discipline['discipline_id']; ?>,"title":"<?php echo $discipline['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>

	var SECTIONS 	= [];
	<?php foreach ($sections as $i => $disciplineSection): ?>
	SECTIONS[<?php echo $disciplineSection[0]['discipline_id']; ?>] = [<?php $delimiter = ""; foreach ($disciplineSection as $j => $section): echo $delimiter ?>{"id":<?php echo $section['section_id']; ?>,"title":"<?php echo $section['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>

	function switchProgramsType () {
		clearSelect (programsSelect);
		clearSelect (disciplinesSelect);
		clearSelect (sectionsSelect);

		for (var i = 0; i < PROGRAMS[programsTypeSelect.value].length; i++) {
			programsSelect.options[i] = new Option (PROGRAMS[programsTypeSelect.value][i].title, PROGRAMS[programsTypeSelect.value][i].id);
		}
	}

	function addProgram () {
		window.location = '/add_program/' + programsTypeSelect.value;
	}

	function removeProgram () {
		if (programsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать ' + ((programsTypeSelect.value == 'direction') ? ('направление') : ('курсы')));
			return;
		}

		window.location = '/remove_program/' + programsTypeSelect.value + '/' + programsSelect.options[programsSelect.selectedIndex].value;
	}

	function editProgram () {
		if (programsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать ' + ((programsTypeSelect.value == 'direction') ? ('направление') : ('курсы')));
			return;
		}

		window.location = '/edit_program/' + programsTypeSelect.value + '/' + programsSelect.options[programsSelect.selectedIndex].value;
	}

	function addDiscipline () {
		if (programsTypeSelect.value == 'course') {
			alert ('Курсы не содержат дисциплин');
			return
		}

		if (programsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать направление');
			return;
		}

		window.location = '/add_discipline/' + programsSelect.options[programsSelect.selectedIndex].value;
	}

	function removeDiscipline () {
		if (disciplinesSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать дисциплину');
			return;
		}

		window.location = '/remove_discipline/' + disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
	}

	function editDiscipline () {
		if (disciplinesSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать дисциплину');
			return;
		}

		window.location = '/edit_discipline/' + disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
	}

	function addSection () {
		if (disciplinesSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать дисциплину');
			return;
		}

		window.location = '/add_section/' + disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
	}

	function removeSection () {
		if (sectionsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать раздел');
			return;
		}

		window.location = '/remove_section/' + sectionsSelect.options[sectionsSelect.selectedIndex].value;
	}

	function editSection () {
		if (sectionsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать раздел');
			return;
		}

		window.location = '/edit_section/' + sectionsSelect.options[sectionsSelect.selectedIndex].value;
	}

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
    function upDiscipline() {
        if (disciplinesSelect.selectedIndex > 0) {
            var tempText = disciplinesSelect.options[disciplinesSelect.selectedIndex - 1].text;
            var tempValue = disciplinesSelect.options[disciplinesSelect.selectedIndex - 1].value;
            disciplinesSelect.options[disciplinesSelect.selectedIndex - 1].text = disciplinesSelect.options[disciplinesSelect.selectedIndex].text;
            disciplinesSelect.options[disciplinesSelect.selectedIndex - 1].value = disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
            disciplinesSelect.options[disciplinesSelect.selectedIndex].text = tempText;
            disciplinesSelect.options[disciplinesSelect.selectedIndex].value = tempValue;
            disciplinesSelect.selectedIndex = disciplinesSelect.selectedIndex - 1;
        }
    }
    function downDiscipline() {
        if (disciplinesSelect.selectedIndex < disciplinesSelect.length) {
            var tempText = disciplinesSelect.options[disciplinesSelect.selectedIndex + 1].text;
            var tempValue = disciplinesSelect.options[disciplinesSelect.selectedIndex + 1].value;
            disciplinesSelect.options[disciplinesSelect.selectedIndex + 1].text = disciplinesSelect.options[disciplinesSelect.selectedIndex].text;
            disciplinesSelect.options[disciplinesSelect.selectedIndex + 1].value = disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
            disciplinesSelect.options[disciplinesSelect.selectedIndex].text = tempText;
            disciplinesSelect.options[disciplinesSelect.selectedIndex].value = tempValue;
            disciplinesSelect.selectedIndex = disciplinesSelect.selectedIndex + 1;
        }
    }
    function saveDisciplineOrder() {
        var disciplineOrder = '';
        for (var i = 0; i < disciplinesSelect.length; i++) {
            if (i > 0) {
                disciplineOrder += ',';
            }
            disciplineOrder += disciplinesSelect.options[i].value;
            //disciplinesSelect.options[i].text = i;
        }
        //document.forms[0].action = '/save_discipline_order/' + programsSelect.options[programsSelect.selectedIndex].value;
        document.forms[0].disciplineOrderInfo.value = disciplineOrder;
        document.forms[0].action = '/save_discipline_order/';
        document.forms[0].submit();
    }
</script>
<style type="text/css">
    select.educationProgramItems {
        width						: 200px;
        margin-right				: 15px;
    }
    input.addButton,
    input.editButton,
    input.removeButton,
    input.upButton,
    input.downButton {
        border 						: 1px solid #AAAAAA;
        background-color 			: #FFFFFF;
        width						: 25px;
        height						: 25px;
    }
</style>
<form method="post">
<input type="hidden" id="disciplineOrderInfo" name="disciplineOrderInfo">
<table cellspacing="0" cellpadding="0">
<tr><td><select id="programsTypeSelect" onchange="switchProgramsType ();"><option value="direction">Направления</option><option value="course">Курсы</option></select></td><td>Дисциплины</td><td>Разделы</td></tr>
<tr>
<td><select id="programsSelect" class="educationProgramItems" size="15" onclick="updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect, sectionsSelect]);">
<?php foreach ($directions as $i => $direction): ?>
<option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
<?php endforeach; ?>
</select></td>
<td><select id="disciplinesSelect" class="educationProgramItems" size="15" onchange="updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect]);"></select></td>
<td><select id="sectionsSelect" class="educationProgramItems" size="15"></select></td>
</tr>
<tr>
<td><input type="button" class="addButton" value="&#x002B;" onclick="addProgram ();"><input type="button" class="editButton" value="&#x270E;" onclick="editProgram ();"><input type="button" class="removeButton" value="&#x2212;" onclick="removeProgram ();"></td>
<td><input type="button" class="addButton" value="&#x002B;" onclick="addDiscipline ();"><input type="button" class="editButton" value="&#x270E;" onclick="editDiscipline ();"><input type="button" class="removeButton" value="&#x2212;" onclick="removeDiscipline ();">&nbsp;<input type="button" class="upButton" value="&uarr;" onclick="upDiscipline()"><input type="button" class="downButton" value="&darr;" onclick="downDiscipline()"><input type="button" value="Сохранить" onclick="saveDisciplineOrder()"></td>
<td><input type="button" class="addButton" value="&#x002B;" onclick="addSection ();"><input type="button" class="editButton" value="&#x270E;" onclick="editSection ();"><input type="button" class="removeButton" value="&#x2212;" onclick="removeSection ();"></td>
</tr>
</table>
</form>
<script type="text/javascript">
	var programsTypeSelect	= document.getElementById ('programsTypeSelect');
	var programsSelect 		= document.getElementById ('programsSelect');
	var disciplinesSelect 	= document.getElementById ('disciplinesSelect');
	var sectionsSelect 		= document.getElementById ('sectionsSelect');
</script>