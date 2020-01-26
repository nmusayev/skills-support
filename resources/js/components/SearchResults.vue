<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Search Result</div>
                    <div class="card-body">
                        <h5 class="mt-1 mb-3">Search Result of Key:
                            <span class="text-success">
                                <b>{{ this.$route.params.key }}</b>
                            </span>
                        </h5>
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
                            <span slot="prev-nav">&lt; Previous</span>
                            <span slot="next-nav">Next &gt;</span>
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
                let key = this.$route.params.key;
                axios.get(`/question/search/${key}?page=` + page)
                    .then(res => {
                        console.log(res.data);
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
            // helpers.redirectIfNotLoggedId(this.$router);

            // Getting Recently Questions
            this.getQuestions();
        },
        components: {
            AddQuestion,
            Question
        }
    }
</script>
