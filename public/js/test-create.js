var Test = function() {
    this.questions = [];
};

Test.prototype = {
    questions: [],

    addPickOne: function(container) {
        var question = new Question_PickOne();
        question.add(container);

        this.questions.push(question);
    },

    save: function(url) {
        var questions = [];

        for (q in this.questions) {
            questions.push(this.questions[q].toJson());
        }

        questions = $.toJSON(questions);
        alert(questions);

        $.ajax({
            type: 'POST',
            url: url,
            data: {questions: questions},
            dataType: 'text',
            success: function(text){
                alert(text);
            }
        });
    }
}

var Question_PickOne = function() {
    this.question = '';
    this.answers = [];
}

Question_PickOne.prototype = {
    _type: 'pick-one',

    classes: {
        form:   'question-form',
        text:   'question-text',
        radio:  'question-radio',
        answer: 'question-answer',

        inactiveInput: 'inactive'
    },

    question: '',

    answers: [],

    add: function(container) {
        var inputs = [], radio, answer, answer_record;

        var form = $('<form></form>')
                     .addClass(this.classes.form);

        var text = $('<input type="text" value="Вопрос" />')
                     .addClass(this.classes.text);

        this.question = text;

        form.append(text);

        for (var i = 0; i < 4; i++) {
            radio = $('<input type="radio" name="correct_answer" />')
                      .addClass(this.classes.radio);

            answer = $('<input type="text" value="Ответ ' + (i + 1) + '" />')
                       .addClass(this.classes.answer);

            inputs.push(radio);
            inputs.push(answer);

            answer_record = {radio: radio, answer: answer};
            this.answers.push(answer_record);

            form
                .append(radio)
                .append(answer)
        }

        inputs.push(text);
        $(inputs).each(this._createInputHinter());

        //alert(form.html());
        container.append(form);
    },

    toJson: function() {
        var json, radio, answer, correct_answer = null;

        json = {
            type: this._type,
            text: this.question.val(),
            answers: [],
            correct_answer: null
        };

        for (idx in this.answers)
        {
            radio  = this.answers[idx].radio;
            answer = this.answers[idx].answer;

            if (radio.attr('checked')) {
                correct_answer = idx;
            }

            json.answers.push(answer.val());
        }

        json.correct_answer = correct_answer;

        return json;
    },

    _createInputHinter: function() {
        var class_inactive = this.classes.inactiveInput;

        var hinter = function() {
            $(this)
                .data('default', $(this).val())
                .addClass(class_inactive)

                .focus(function() {
                    $(this).removeClass(class_inactive);
                    if ($(this).val() == $(this).data('default') || '') {
                        $(this).val('');
                    }
                })

                .blur(function() {
                    var default_val = $(this).data('default');
                    if ($(this).val() == '') {
                        $(this).addClass(class_inactive);
                        $(this).val($(this).data('default'));
                    }
                });
        };

        return hinter;
    }
}