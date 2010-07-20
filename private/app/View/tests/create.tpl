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