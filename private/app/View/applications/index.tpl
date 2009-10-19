<?php $this->title = 'Заявки' ?>
<h3>Подать заявку</h3>

Ваша учебная программа может заключаться в изучении всего направления/курса или набора отдельной дисциплины.<br />
Для оформления заявки на обучение укажите учебную программу, пользуясь списками ниже:<br /><br />

<?php
	$directions 	= $this->directions;
	$courses		= $this->courses;
	$disciplines	= $this->disciplines;
	$form = $this->form;
?>
<script type="text/javascript">
	var PROGRAMS			= [];
	PROGRAMS['direction']	= [<?php $delimiter = ''; foreach ($directions as $i => $direction): echo $delimiter; ?>{'id':<?php echo $direction['program_id']; ?>,'title':'<?php echo $direction["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];	
	PROGRAMS['course'] 		= [<?php $delimiter = ''; foreach ($courses as $i => $course): echo $delimiter; ?>{'id':<?php echo $course['program_id']; ?>,'title':'<?php echo $course["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];

	programTypeRus =  {'direction' : 'о направление',
					   'course'    : ' курс'
					  };

	var DISCIPLINES = [];
	<?php foreach ($disciplines as $i => $specialityDiscipline): ?>
	DISCIPLINES[<?php echo $specialityDiscipline[0]['program_id']; ?>] = [<?php $delimiter = ""; foreach ($specialityDiscipline as $j => $discipline): echo $delimiter ?>{"id":<?php echo $discipline['discipline_id']; ?>,"title":"<?php echo $discipline['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>
	
	function switchProgramsType ()
	{
		clearProgramsList 		();
		clearDisciplinesList 	();
		
		for (var i = 0; i < PROGRAMS[programsTypeSelect.value].length; i++) {
			programsSelect.options[i] = new Option (PROGRAMS[programsTypeSelect.value][i].title, PROGRAMS[programsTypeSelect.value][i].id);
		}
	}

	function clearProgramsList () {
		while (programsSelect.firstChild) {
			programsSelect.removeChild (programsSelect.firstChild);
		}		
	}
	
	function clearDisciplinesList () {
		while (disciplinesSelect.firstChild) {
			disciplinesSelect.removeChild (disciplinesSelect.firstChild);
		}		
	}
	
	function updateDisciplinesList ()
	{
		clearDisciplinesList ();
		
		var disciplines = DISCIPLINES[programsSelect.options[programsSelect.selectedIndex].value];
		for (var i = 0; i < disciplines.length; i++) {
			disciplinesSelect.options[i] = new Option (disciplines[i].title, disciplines[i].id);
		}
		changePhrase('programsSelect');
	}
	
	function changePhrase(choiceType)
	{
		delChildren($('appChosen'));
		if (choiceType == 'disciplinesSelect')
		{
			$('programType').value = 'discipline';
			$('programId').value = disciplinesSelect.options[disciplinesSelect.selectedIndex].value;
			appChosenStr = document.createTextNode("Для обучения выбрана дисциплина "+
												   disciplinesSelect.options[disciplinesSelect.selectedIndex].text+"\u00A0");
		} else
		{
			$('programType').value = 'program';
			$('programId').value = programsSelect.options[programsSelect.selectedIndex].value;
			appChosenStr = document.createTextNode("Для обучения выбран"+programTypeRus[programsTypeSelect.value]+" "+
												   programsSelect.options[programsSelect.selectedIndex].text+"\u00A0");
		}
		$('appChosen').appendChild(appChosenStr);
	}
	
	function appApply()
	{
		if (($('programsSelect').selectedIndex == -1) && ($('disciplinesSelect').selectedIndex == -1))
		{
			alert ('Необходимо выбрать направление/курс или дисциплину');
			return;
		}
		
		window.location = '/applications/apply/' + $('programType').value + '/' + $('programId').value;
	}
	
	function delChildren(obj)
	{
		var child_list = obj.childNodes;
		for (var ch_i=child_list.length-1; ch_i>=0; ch_i--)
		{
			obj.removeChild(child_list.item(0));
		};
	}


</script>
<br />
<table cellspacing="0" cellpadding="0">
<tr><td><select id="programsTypeSelect" onchange="switchProgramsType ();"><option value="direction">Направления</option><option value="course">Курсы</option></select></td><td>Дисциплины</td></tr>
<tr>
	<td><select onmouseup="changePhrase(this.id);" id="programsSelect" class="educationProgramItems" size="10" onchange="updateDisciplinesList ();">
	<?php foreach ($directions as $i => $direction): ?>
	<option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
	<?php endforeach; ?>
	</select></td>
	<td><select id="disciplinesSelect" class="educationProgramItems" size="10" onmouseup="changePhrase(this.id);"></select></td>
</tr>
</table>
<p>
	<div id="appChosen">Учебная программа не выбрана.<br /></div>
</p>
<p>
	<input name="programType" type="hidden" id="programType" /> 
	<input name="programId" type="hidden" id="programId" /> 
	<input type="submit" value="Подать заявку" onclick="appApply();" />
</p>

<script type="text/javascript">
	var programsTypeSelect	= document.getElementById ('programsTypeSelect');
	var programsSelect 		= document.getElementById ('programsSelect');
	var disciplinesSelect 	= document.getElementById ('disciplinesSelect');
</script>
