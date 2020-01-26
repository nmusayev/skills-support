<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ translate('site.heading.community') }}</div>

                    <div class="card-body">
                        <add-question></add-question>

                        <h5 class="mt-4 mb-3">{{ translate('site.label.recently_asked') }}:</h5>
                        <div class="recent-questions">
                            <router-link tag="div"
                                         v-for="question in questions.data"
                                         :key="question.id"
                                         :to="'/question-detail/' + question.id"
                                         class="question border border-secondary bg-light p-3 mb-3">
                                <question :question="question" :limited="true"></question>
                            </router-link>
                        </div>

                        <pagination :data="linkData"
                                    :limit="3"
                                    align="center"
                                    class="mt-4"
                                    @pagination-change-page="getQuestions">
                            <span slot="prev-nav">&lt; {{ translate('site.label.previous') }}</span>
                            <span slot="next-nav">{{ translate('site.label.next') }} &gt;</span>
                        </pagination>
                    </div>
                </div>
            </div>
        </div>

        <FlashMessage></FlashMessage>
    </div>
</template>

<script>
    import AddQuestion from "./AddQuestion";
    import Question from "./Question";
    import helpers from "../helpers";

    export default {
        data() {
            return {
                questions: [],
                linkData: {},
            }
        },
        methods: {
            // Our method to GET results from a Laravel endpoint
            getQuestions(page = 1) {
                axios.get('/question/recent?page=' + page)
                    .then(res => {
                        // console.log(res.data);
                        this.questions = res.data;

                        this.linkData = {
                            next_page_url: this.questions.links.next,
                            prev_page_url: this.questions.links.prev,
                            ...this.questions.meta,
                        };
                    });
            }
        },
        mounted() {
            // Check for Authentication [Redirecting if not logged in]
            helpers.redirectIfNotLoggedId(this.$router);

            // Getting Recently Questions
            this.getQuestions();

            // Listeting to Custom Event: 'questionAdded'
            bus.$on('questionAdded', () => {
                // Reload Questions Again
                this.getQuestions();
            });
        },
        components: {
            AddQuestion,
            Question
        }
    }
</script>
