<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ translate('site.heading.question_detail' )}}</div>

                    <div class="card-body">

                        <question v-if="!editingQuestion" :limited="false" :question="question"></question>

                        <edit-question v-else :question="question"></edit-question>

                        <div v-if="questionOwner">
                            <button
                                @click="editQuestion()"
                                v-if="!editingQuestion"
                                class="btn btn-success col-12 mt-3">
                                {{ translate('site.btn.edit_question' )}}
                            </button>
                            <button
                                @click="cancelEditQuestion()"
                                v-if="editingQuestion"
                                class="btn btn-info text-white col-12 mt-1">
                                {{ translate('site.btn.cancel' )}}
                            </button>
                            <button
                                v-if="!editingQuestion"
                                @click="deleteQuestion()"
                                class="btn btn-danger col-12 mt-1">
                                {{ translate('site.btn.delete_question' )}}
                            </button>
                        </div>
                        <hr v-else class="mt-4 d-block border-success w-100">


                        <h5 class="mt-4 mb-3" v-if="answers.length">Answers:</h5>
                        <div class="answers">
                            <answer v-for="answer in answers"
                                    :key="answer.id"
                                    :answer="answer"
                                    :questionOwner="questionOwner"
                            ></answer>

                            <button
                                v-if="load_more_link"
                                @click="loadMoreAnswer()"
                                class="btn btn-info text-white col-12 mt-1">
                                {{ translate('site.btn.load_more' )}}
                            </button>

                        </div>

                        <add-answer v-if="loggedIn > 0" :question="question"></add-answer>

                    </div>
                </div>
            </div>
        </div>

        <FlashMessage></FlashMessage>
    </div>
</template>

<script>
    import Question from "./Question";
    import Answer from "./Answer";
    import EditQuestion from "./EditQuestion";
    import AddAnswer from "./AddAnswer";
    import helpers from "../helpers";


    export default {
        data() {
            return {
                question: null,
                answers: [],
                editingQuestion: false,
                load_more_link: null,
            }
        },
        methods: {
            getQuestionDetails() {
                let question_id = this.$route.params.id;
                axios.get('/question/' + question_id)
                    .then(res => {
                        this.question = res.data.success;
                        // console.log(res.data.success);
                    }).catch(err => {
                    console.log(err);
                });
            },
            getQuestionAnswers() {
                let question_id = this.$route.params.id;
                axios.get(`/question/${question_id}/answer/`)
                    .then(res => {
                        // console.log('answers:');
                        // console.log(res.data);
                        this.answers = res.data.data;
                        this.load_more_link = res.data.links.next;
                    }).catch(err => {
                    console.log(err);
                });
            },
            loadMoreAnswer() {
                let question_id = this.$route.params.id;
                axios.get(`/question/${question_id}/answer` + this.load_more_link)
                    .then(res => {
                        Array.prototype.push.apply(this.answers,res.data.data);

                        this.load_more_link = res.data.links.next;
                    }).catch(err => {
                        console.log(err);
                    });
            },
            deleteQuestion() {
                let result = confirm("Are you sure to delete question?");
                if (result) {
                    let question_id = this.$route.params.id;
                    axios.delete(`/question/${question_id}`)
                        .then(res => {
                            this.flashMessage.success({
                                title: 'Success Message',
                                message: 'Question successfully deleted!',
                            });

                            this.$router.push('/community');
                        }).catch(err => {
                        let error = err.response.data.message;

                        this.flashMessage.show({
                            status: 'error',
                            title: 'Error Message',
                            message: error,
                        });
                    });
                }
            },
            editQuestion() {
                this.editingQuestion = true;
            },
            cancelEditQuestion() {
                this.editingQuestion = false;
            },
        },
        computed: {
            questionOwner() {
                return helpers.checkUserIsQuestionOwner(this.question);
            },
            loggedIn() {
                return helpers.loggedIn();
            }
        },
        mounted() {
            // Check for Authentication [Redirecting if not logged in]
            // helpers.redirectIfNotLoggedId(this.$router);

            this.getQuestionDetails();
            this.getQuestionAnswers();

            // Handling 'questionUpdated' Event
            bus.$on('questionUpdated', () => {
                // Reload Question Again
                this.getQuestionDetails();
                this.cancelEditQuestion();
            });

            // Handling 'questionUpdated' Event
            bus.$on('answerAdded', () => {
                // Reload Answers Again
                this.getQuestionAnswers();
            });

            // Listeting to Custom Event: 'answerDeleted'
            bus.$on('answerDeleted', () => {
                // Reload Questions Again
                this.getQuestionAnswers();
            });

            // Listeting to Custom Event: 'bestAnswerSelected'
            bus.$on('bestAnswerSelected', () => {
                // Reload Questions Again
                this.getQuestionAnswers();
            });
        },
        components: {
            Question,
            Answer,
            EditQuestion,
            AddAnswer,
        }
    }
</script>
