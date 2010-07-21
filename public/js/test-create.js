var Test = function() {};

Test.prototype = {
    questions: [],

    addPickOne: function(container) {
        var question = new Question_PickOne();
        question.add(container);

        this.questions.push(question);
    }
}

var Question_PickOne = function() {}

Question_PickOne.prototype = {
    classes: {
        form:   'question-form',
        text:   'question-text',
        radio:  'question-radio',
        answer: 'question-answer',

        inactiveInput: 'inactive'
    },

    add: function(container) {
        var form = $('<form></form>')
                     .addClass(this.classes.form);

        var text = $('<input type="text" value="Вопрос" />')
                     .addClass(this.classes.text);

        var inputs = [text];

        $(inputs).each(function() {
            $(this)
                .data('default', $(this).val())
                .addClass(this.classes.inactiveInput)

                .focus(function() {
                    $(this).removeClass(this.classes.inactiveInput);
                    if ($(this).val() == $(this).data('default') || '') {
                        $(this).val('');
                    }
                })

                .blur(function() {
                    var default_val = $(this).data('default');
                    if ($(this).val() == '') {
                        $(this).addClass(this.classes.inactiveInput);
                        $(this).val($(this).data('default'));
                    }
                });
        });

        form.append(text);

        for (var i = 0; i < 4; i++) {
            form
            .append(
                $('<input type="radio" name="correct_answer" />')
                  .addClass(this.classes.radio)
            )
            .append(
                $('<input type="text" value="Answer ' + (i + 1) + '" />')
                  .addClass(this.classes.answer)
            );
        }

        alert(form.html());
        container.append(form);
    }
}