<?php
	$specialities 	= $this->specialities;
	$disciplines	= $this->disciplines;
	$sections		= $this->sections;
?>
<script type="text/javascript">
	var DISCIPLINES = [];
	<?php foreach ($disciplines as $i => $specialityDiscipline): ?>
	DISCIPLINES[<?php echo $specialityDiscipline[0]['program_id']; ?>] = [
	<?php $delimiter = ""; foreach ($specialityDiscipline as $j => $discipline): echo $delimiter ?>
	{"id":<?php echo $discipline['discipline_id']; ?>,"title":"<?php echo $discipline['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>
	];
	<?php endforeach; ?>
	
	var SECTIONS = [];
	<?php foreach ($sections as $i => $disciplineSection): ?>
	SECTIONS[<?php echo $disciplineSection[0]['discipline_id']; ?>] = [
	<?php $delimiter = ""; foreach ($disciplineSection as $j => $section): echo $delimiter ?>
	{"id":<?php echo $section['section_id']; ?>,"title":"<?php echo $section['title']; ?>","number":<?php echo $section['number']; ?>}<?php ; $delimiter =","; endforeach; ?>
	];
	<?php endforeach; ?>

	function removeSpeciality () {
		if (specialitiesSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать направление');
			return;
		}
		
		window.location = '/remove_speciality/' + specialitiesSelect.options[specialitiesSelect.selectedIndex].value;
	}

	function addDiscipline () {
		if (specialitiesSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать направление');
			return;
		}
		
		window.location = '/add_discipline/' + specialitiesSelect.options[specialitiesSelect.selectedIndex].value;
	}
	
	function removeDiscipline () {
		if (disciplinesSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать дисциплину');
			return;
		}
		
		window.location = '/remove_discipline/' + disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
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
	
	function clearDisciplinesList () {
		while (disciplinesSelect.firstChild) {
			disciplinesSelect.removeChild (disciplinesSelect.firstChild);
		}		
	}
	
	function clearSectionsList () {
		while (sectionsSelect.firstChild) {
			sectionsSelect.removeChild (sectionsSelect.firstChild);
		}		
	}
	
	function updateDisciplinesList () {
		clearDisciplinesList ();
		clearSectionsList ();
		
		var disciplines = DISCIPLINES[specialitiesSelect.options[specialitiesSelect.selectedIndex].value];
		for (var i = 0; i < disciplines.length; i++) {
			disciplinesSelect.options[i] = new Option (disciplines[i].title, disciplines[i].id);
		}
	}
	
	function updateSectionsList () {
		clearSectionsList ();
				
		var sections = SECTIONS[disciplinesSelect.options[disciplinesSelect.selectedIndex].value];
		for (var i = 0; i < sections.length; i++) {
			sectionsSelect.options[i] = new Option ("Раздел " + sections[i].number + ": " + sections[i].title, sections[i].id);
		}
	}
</script>
<table cellspacing="0" cellpadding="0">
<tr><td>Направления</td><td>Дисциплины</td><td>Разделы</td></tr>
<tr>
<td><select id="specialitiesSelect" class="educationProgramItems" size="10" onchange="updateDisciplinesList ();">
<?php foreach ($specialities as $i => $speciality): ?>
<option value="<?php echo $speciality['program_id']; ?>"><?php echo $speciality['title']; ?></option>
<?php endforeach; ?>
</select></td>
<td><select id="disciplinesSelect" class="educationProgramItems" size="10" onchange="updateSectionsList ();"></select></td>
<td><select id="sectionsSelect" class="educationProgramItems" size="10"></select></td>
</tr>
<tr>
<td><a href="/add_speciality"><button class="addButton">&#x002B;</button></a><button class="editButton">&#x270E;</button><button class="removeButton" onclick="removeSpeciality ();">&#x2212;</button></td>
<td><button class="addButton" onclick="addDiscipline ();">&#x002B;</button><button class="editButton">&#x270E;</button><button class="removeButton" onclick="removeDiscipline ();">&#x2212;</button></td>
<td><button class="addButton" onclick="addSection ();">&#x002B;</button><button class="editButton">&#x270E;</button><button class="removeButton" onclick="removeSection ();">&#x2212;</button></td>
</tr>
</table>
<script type="text/javascript">
	var specialitiesSelect 	= document.getElementById ('specialitiesSelect');
	var disciplinesSelect 	= document.getElementById ('disciplinesSelect');
	var sectionsSelect 		= document.getElementById ('sectionsSelect');
</script>