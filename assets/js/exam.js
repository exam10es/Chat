const ExamEngine = {
    currentQuestion: 0,
    answers: {},
    timer: null,
    init: () => {
        const dataElement = document.getElementById('exam-data');
        if (!dataElement) {
            return;
        }
        ExamEngine.questions = JSON.parse(dataElement.textContent);
        ExamEngine.loadQuestion(0);
        document.getElementById('next-btn')?.addEventListener('click', ExamEngine.nextQuestion);
        document.getElementById('prev-btn')?.addEventListener('click', ExamEngine.previousQuestion);
        document.getElementById('submit-btn')?.addEventListener('click', ExamEngine.submitExam);
    },
    loadQuestion: (index) => {
        const question = ExamEngine.questions[index];
        if (!question) {
            return;
        }
        ExamEngine.currentQuestion = index;
        const container = document.getElementById('question-container');
        container.innerHTML = '';
        const title = document.createElement('h3');
        title.textContent = question.question_text;
        container.appendChild(title);
        ['A', 'B', 'C', 'D'].forEach((option) => {
            const key = `option_${option.toLowerCase()}`;
            if (question[key]) {
                const label = document.createElement('label');
                label.className = 'option';
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'answer';
                input.value = option;
                input.checked = ExamEngine.answers[question.id] === option;
                input.addEventListener('change', () => ExamEngine.saveAnswer(question.id, option));
                label.appendChild(input);
                label.append(` ${question[key]}`);
                container.appendChild(label);
            }
        });
        document.getElementById('progress').textContent = `${index + 1} / ${ExamEngine.questions.length}`;
    },
    saveAnswer: (questionId, answer) => {
        ExamEngine.answers[questionId] = answer;
    },
    nextQuestion: () => {
        ExamEngine.loadQuestion(ExamEngine.currentQuestion + 1);
    },
    previousQuestion: () => {
        ExamEngine.loadQuestion(ExamEngine.currentQuestion - 1);
    },
    submitExam: () => {
        const input = document.getElementById('answers-input');
        input.value = JSON.stringify(ExamEngine.answers);
        document.getElementById('exam-form').submit();
    },
};

document.addEventListener('DOMContentLoaded', ExamEngine.init);
