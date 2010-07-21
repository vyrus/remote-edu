<!--
<form action="/tests/create" method="post">
    <input type="text" name="question" value="Question" />
    <br >

    <input type="radio" name="correct_answer" value="1" />
    <input type="text" name="answers[]" value="Answer 1" />
    <br />

    <input type="radio" name="correct_answer" value="2" />
    <input type="text" name="answers[]" value="Answer 2" />
    <br />

    <input type="radio" name="correct_answer" value="3" />
    <input type="text" name="answers[]" value="Answer 3" />
    <br />

    <input type="radio" name="correct_answer" value="4" />
    <input type="text" name="answers[]" value="Answer 4" />
    <br />

    <input type="submit" value="Добавить вопрос" />
</form>
-->

<style type="text/css">
    div#controls {
        /*margin-bottom: 1.7em;*/
    }

    .question-form {
        /*border: 1px solid red;*/
        margin: 1.7em 0px;
    }

    .question-text {
        /*border: 1px solid blue;*/
        display: block;
    }

    .question-radio {
        /*border: 1px solid black;*/
    }

    .question-answer {
        /*border: 1px solid green;*/
        /*display: block;*/
    }
</style>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/test-create.js') ?>"></script>
<script type="text/javascript">
    var quests;
    var test = new Test();

    $(document).ready(function() {
        quests = $('div#questions');
    })

    function addQuestion() {
        test.addPickOne(quests);
    }
</script>

<div id="controls">
    <a href="#" onClick="javascript: addQuestion(); return false;">Добавить вопрос</a>
    <a href="#" onClick="javascript: alert('Implement me!')">Сохранить</a>
</div>

<div id="questions"></div>