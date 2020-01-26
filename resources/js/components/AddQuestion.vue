<template>
    <form action="" id="addQuestionForm" @submit.prevent="addQuestion" method="post">
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
                    <!--                    <option v-for="skill in skills"-->
                    <!--                            :value="skill.id">{{ skill.name }}</option>-->
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="language">
                    {{ translate('site.label.language') }}
                </label>
                <div>
                    <select class="form-control languages-select" id="language">
                        <option value="">{{ translate('site.label.choose_language') }}</option>
                        <option v-for="language in languages_all"
                                :value="language.id">{{ language.text }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <button class="btn btn-success col-md-12 pull-right" type="submit">
                    {{ translate('site.btn.ask') }}
<!--                    Ask Question-->
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        name: "AddQuestion",
        data() {
            return {
                skills: [],
                languages_all: [],
                form: {
                    title: '',
                    content: '',
                    skills: [],
                },
                // selectedValue: [],
            }
        },
        methods: {
            // getSkills() {
            //     axios.get('/skill/all')
            //         .then(res => {
            //             let skills = res.data.success;
            //             // console.log(skills);
            //
            //             this.skills = skills;
            //         })
            //         .catch(err => {
            //             console.log(err);
            //         });
            // },
            getLanguages() {
                axios.get('/language')
                    .then(res => {
                        let languages = res.data.success;
                        // console.log(languages);

                        this.languages_all = languages;
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            addQuestion() {
                console.log($('#skills').select2('data'));
                axios.post('/question', {
                    'title': this.form.title,
                    'content': this.form.content,
                    'language_id': $(".languages-select").val(),
                    'skills': $('#skills').val(),
                }).then(res => {
                    bus.$emit('questionAdded');

                    // Resetting form fileds
                    this.form.title = '';
                    this.form.content = '';
                    $(".languages-select").val('').trigger('change');
                    $(".skills-select").val('').trigger('change');

                    this.flashMessage.success({
                        title: 'Success Message',
                        message: 'Question successfully sent!',
                    });
                }).catch(err => {
                    let error = err.response.data.message;

                    this.flashMessage.show({
                        status: 'error',
                        title: 'Error Message',
                        message: 'You have to fill all fileds!',
                    });
                });
            }
        },
        mounted() {
            // this.getSkills();
            this.getLanguages();

            // Setting Select2 Plugin for Skills
            $('#skills').select2({
                ajax: {
                    url: 'api/skill/all',
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
        }
    }
</script>

<style scoped>

</style>
