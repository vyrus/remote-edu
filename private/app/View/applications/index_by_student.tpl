<?php $this->title = 'Заявки' ?>
<h3>Подать заявку</h3>

Ваша учебная программа может заключаться в изучении всего направления/курса или набора отдельных дисциплин.<br />
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
        $('#programsSelect').empty();
		$('#disciplinesSelect').empty();
		
        var type = $('#programsTypeSelect').val();
        
        $.each(PROGRAMS[type], function(key, program) {
            $('#programsSelect').append('<option value="' + program.id + '">' + 
                                         program.title + '</option>');
        });
	}

	function updateDisciplinesList ()
	{
		$('#disciplinesSelect').empty();
        
        var program_id = $('#programsSelect').val();
        if (undefined === DISCIPLINES[program_id]) return;
        
        $.each(DISCIPLINES[program_id], function(key, disc) {
            $('#disciplinesSelect').append('<option value="' + disc.id + '">' + 
                                            disc.title + '</option>');
        });
        
		changePhrase('programsSelect');
	}
	
	function changePhrase(choiceType)
	{
        if (choiceType == 'disciplinesSelect')
		{
			$('#programType').val('discipline');
			$('#programId').val($('#disciplinesSelect').val());
			
            appChosenStr = 'Для обучения выбрана дисциплина "' + 
			               $('#disciplinesSelect option:selected').text() + '"';
		}
        else
		{
			$('#programType').val('program');
			$('#programId').val($('#programsSelect').val());
			
            var program_type = $('#programsTypeSelect').val();
            
            appChosenStr = 'Для обучения выбран' + 
                            programTypeRus[program_type] + ' "' + 
							$('#programsSelect option:selected').text() + '"';
		}
        
		$('#appChosen').text(appChosenStr);
	}
	
	function appApply()
	{
		if (null === $('#programsSelect').val() && 
            null === $('#disciplinesSelect').val())
		{
			alert ('Необходимо выбрать направление/курс или дисциплину');
			return;
		}
		
		window.location = '/applications/apply/' + $('#programType').val() + 
                          '/' + $('#programId').val();
	}      
</script>
<br />

<table cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <select id="programsTypeSelect" onchange="switchProgramsType ();">
                <option value="direction">Направления</option>
                <option value="course">Курсы</option>
            </select>
        </td>
        
        <td>Дисциплины</td>
    </tr>

    <tr>
	    <td>
            <select onmouseup="changePhrase(this.id);" id="programsSelect" class="educationProgramItems" size="10" onchange="updateDisciplinesList ();">
	        <?php foreach ($directions as $i => $direction): ?>
	            <option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
	        <?php endforeach; ?>
	        </select>
        </td>
	    
        <td>
            <select id="disciplinesSelect" class="educationProgramItems" size="10" onmouseup="changePhrase(this.id);">
            </select>
        </td>
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