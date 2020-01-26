<template>
    <form action="" id="addQuestionForm" @submit.prevent="updateQuestion" method="post">
        <div class="form-group">
            <label for="title">{{ translate('site.label.q_title') }}</label>
            <input class="form-control" name="title" v-model="form.title"
                   placeholder="Your question goes here.."
                   id="title">
        </div>

        <div class="form-group">
            <label for="content">{{ translate('site.label.q_body') }}</label>
            <textarea class="form-control"
                      name="content" v-model="form.content"
                      placeholder="Your question goes here.."
                      id="content" rows="3"></textarea>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="skills">{{ translate('site.label.skills') }}</label>
                <select
                    multiple="multiple"
                    class="form-control skills-select"
                    name="skills[]"
                    id="skills">
                                        <option v-for="skill in form.skills"
                                                :value="skill.id">{{ skill.text }}</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="language">
                    {{ translate('site.label.language') }}
                </label>
                <div>
                    <select class="form-control languages-select" id="language">
                        <option v-for="language in languages_all"
                                :selected="language.id === form.language.id"
                                :value="language.id">{{ language.text }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <button
            type="submit"
            class="btn btn-success col-12 mt-3">
            {{ translate('site.btn.update_question') }}
        </button>
    </form>
</template>

<script>
    export default {
        name: "EditQuestion",
        props: ['question'],
        data() {
            return {
                languages_all: [],
                form: {
                    title: this.question.title,
                    content: this.question.content,
                    skills: this.question.skills,
                    language: this.question.language,
                },
                // selectedValue: [],
            }
        },
        methods: {
            getLanguages() {
                axios.get('/language/all')
                    .then(res => {
                        let languages = res.data.success;
                        // console.log(languages);

                        this.languages_all = languages;
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            updateQuestion() {
                let id = this.$route.params.id;

                axios.put(`/question/${id}`, {
                    'title': this.form.title,
                    'content': this.form.content,
                    'language_id': $(".languages-select").val(),
                    'skills': $('#skills').val(),
                }).then(res => {
                    // console.log(res);

                    bus.$emit('questionUpdated');

                    this.flashMessage.success({
                        title: 'Success Message',
                        message: 'Question successfully updated!',
                    });
                }).catch(err => {
                    let error = err.response.data.message;
                    console.log(error);

                    this.flashMessage.show({
                        status: 'error',
                        title: 'Error Message',
                        message: error,
                    });
                });
            }
        },
        mounted() {
            // Getting Languages
            this.getLanguages();

            // Setting Select2 Plugin for Skills
            $('#skills').select2({
                ajax: {
                    url: '/api/skill/all',
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: function (params) {
                        let query = {
                            search: params.term,
                            type: 'public'
                        };

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: data.success,
                        };
                    },
                }
            });

            // Mounting Select2 Plugin
            $('.languages-select').select2();

            // Setting Skills
            $('.skills-select').val(this.form.skills.map(a => a.id)).trigger('change');
        },
    }
</script>

<style scoped>

</style>
