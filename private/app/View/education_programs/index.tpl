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
</script>
<style type="text/css">
	select.educationProgramItems {
		width						: 130px;
		margin-right				: 15px;
	}
	
	button.addButton,
	button.editButton,
	button.removeButton {
		border 						: 1px solid #AAAAAA;
		background-color 			: #FFFFFF;
		width						: 25px;
		height						: 25px;
	}
</style>
<table cellspacing="0" cellpadding="0">
<tr><td><select id="programsTypeSelect" onchange="switchProgramsType ();"><option value="direction">Направления</option><option value="course">Курсы</option></select></td><td>Дисциплины</td><td>Разделы</td></tr>
<tr>
<td><select id="programsSelect" class="educationProgramItems" size="10" onchange="updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect, sectionsSelect]);">
<?php foreach ($directions as $i => $direction): ?>
<option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
<?php endforeach; ?>
</select></td>
<td><select id="disciplinesSelect" class="educationProgramItems" size="10" onchange="updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect]);"></select></td>
<td><select id="sectionsSelect" class="educationProgramItems" size="10"></select></td>
</tr>
<tr>
<td><button class="addButton" onclick="addProgram ();">&#x002B;</button><button class="editButton" onclick="editProgram ();">&#x270E;</button><button class="removeButton" onclick="removeProgram ();">&#x2212;</button></td>
<td><button class="addButton" onclick="addDiscipline ();">&#x002B;</button><button class="editButton" onclick="editDiscipline ();">&#x270E;</button><button class="removeButton" onclick="removeDiscipline ();">&#x2212;</button></td>
<td><button class="addButton" onclick="addSection ();">&#x002B;</button><button class="editButton" onclick="editSection ();">&#x270E;</button><button class="removeButton" onclick="removeSection ();">&#x2212;</button></td>
</tr>
</table>
<script type="text/javascript">
	var programsTypeSelect	= document.getElementById ('programsTypeSelect');
	var programsSelect 		= document.getElementById ('programsSelect');
	var disciplinesSelect 	= document.getElementById ('disciplinesSelect');
	var sectionsSelect 		= document.getElementById ('sectionsSelect');
</script>