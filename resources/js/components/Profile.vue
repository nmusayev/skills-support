<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ translate('site.heading.profile') }}
                    </div>

                    <div class="card-body">
                        <div class="">
                            <div class="row">
                                <div class="col-sm-4">
                                    <img
                                        :src="this.imageUrl"
                                        class="rounded img-fluid img-thumbnail" alt="profile image">
                                </div>
                                <div class="col-sm-8">
                                    <ul class="list-unstyled">
                                        <li>
                                            <h2>{{ this.name }}</h2>
                                        </li>
                                        <li>{{ translate('site.label.point') }}: {{ this.overall_point }}</li>
                                        <li>
                                            <div v-if="!linkedin_editing">
                                                <a :href="linkedin_profile"
                                                   target="_blank"
                                                   class="btn btn-info linkedin-profile">
                                                    {{ translate('site.label.profile') }}
                                                </a>
                                                <a v-if="profileOwner" href="" @click.prevent="showLinkedinInput()">
                                                    <i style="font-size: 30px; position: relative; top: 8px"
                                                       class="fa fa-pencil-square-o"
                                                       aria-hidden="true"></i>
                                                </a>
                                            </div>
                                            <div v-else>
                                                <input v-model="linkedin_profile_new"
                                                       class="d-block w-100 mt-2"
                                                       @keyup.enter="updateLinkedinLink()"
                                                       type="text"/>
                                                <div class="mt-2">
                                                    <button
                                                        @click="updateLinkedinLink()"
                                                        class="btn btn-success">
                                                        {{ translate('site.label.update_profile') }}
                                                    </button>
                                                    <button
                                                        @click="cancelLinkedinEditing()"
                                                        class="btn btn-info text-white">
                                                        {{ translate('site.btn.cancel') }}
                                                    </button>
                                                </div>
                                            </div>

                                        </li>
                                        <li v-if="profileOwner">
                                            <label for="file-upload"
                                                   class="btn mt-2 bg-warning  custom-file-upload">
                                                {{ translate('site.label.profile_image') }}
                                            </label>
                                            <input type="file"
                                                   id="file-upload"
                                                   accept="image/*"
                                                   @change="fileInputOnChange"
                                                   name="file" class="d-none mt-2">
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-12" v-if="profileOwner">
                                    <br><br>
                                    <h5 class="d-inline-block mr-2">{{ translate('site.label.add_skill') }}: </h5>
                                    <form action="" class="">
                                        <select class="form-control skills-select">
                                            <option value="">{{ translate('site.label.choose_skill') }}</option>
                                        </select>
                                        <button class="mt-2 btn btn-success"
                                                @click.prevent="addSkill"
                                                type="submit">
                                            {{ translate('site.btn.add') }}
                                        </button>
                                    </form>
                                </div>
                                <div class="col-sm-12">
                                    <br><br>
                                    <h5>{{ translate('site.label.skills') }}:</h5>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <ul class="list-group skill-set">
                                                <li class="list-group-item"
                                                    :class="{active: active_skill_id === 0 }">
                                                    <a href="#"
                                                       @click.prevent="getAllSkillsDetails">
                                                        {{ translate('site.label.general_all') }}</a>
                                                </li>
                                                <li v-for="skill in skills"
                                                    :key="skill.id"
                                                    :class="{active: skill.id === active_skill_id}"
                                                    class="list-group-item">
                                                    <a href="#"
                                                       @click.prevent="getSkillDetails(skill.id)">
                                                        {{ skill.text }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="row text-center">
                                                <div class="col-sm-5 offset-1 mt-4">
                                                    <h5>{{ translate('site.label.answered') }}</h5>
                                                    <a href="#" data-toggle="modal"
                                                       @click="modalShowing = 'answers'"
                                                       data-target=".question-answer-modal" class="numbers">
                                                        {{ Object.keys(this.answers).length }}
                                                    </a>
                                                </div>
                                                <div class="col-sm-5 mt-4">
                                                    <h5>{{ translate('site.label.asked') }}</h5>
                                                    <a href="#" data-toggle="modal"
                                                       @click="modalShowing = 'questions'"
                                                       data-target=".question-answer-modal" class="numbers">
                                                        {{ Object.keys(this.questions).length }}
                                                    </a>
                                                </div>
                                                <div class="col-12" v-if="profileOwner">
                                                    <button
                                                        v-if="active_skill_id !== 0"
                                                        @click="detachSkillFromUser()"
                                                        class="btn btn-outline-danger pull-right mt-5 mr-5">
                                                        {{ translate('site.label.delete_skill') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-5 mb-5 d-block border-success w-100">

                                <div class="col-sm-12 mb-5" v-if="profileOwner">
                                    <h5 class="d-inline-block mr-2">{{ translate('site.label.add_language') }}: </h5>
                                    <form action="" class="">
                                        <select class="form-control languages-select">
                                            <option value="">{{ translate('site.label.choose_language') }}</option>
                                            <option v-for="language in languages_all"
                                                    :value="language.id">{{ language.text }}
                                            </option>
                                        </select>
                                        <button class="btn btn-success mt-2"
                                                @click.prevent="addLanguage"
                                                type="submit">
                                            {{ translate('site.btn.add') }}
                                        </button>
                                    </form>
                                </div>
                                <div class="col-sm-12">
                                    <h5>Languages:</h5>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <ul v-if="profileOwner" class="list-group skill-set language-set">
                                                <li v-for="language in languages"
                                                    :key="language.id"
                                                    @click="deleteLanguage(language.id)"
                                                    class="list-group-item">
                                                    <a>{{ language.text }}</a>
                                                </li>
                                            </ul>
                                            <ul v-else class="list-group skill-set language-set">
                                                <li v-for="language in languages"
                                                    :key="language.id"
                                                    class="list-group-item">
                                                    <a onmouseover="this.style.background='#5cb85c'"
                                                       onmouseout="this.style.background='#fff'">{{ language.text }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <FlashMessage></FlashMessage>

        <!-- Answer Question modal -->
        <div class="modal fade question-answer-modal"
             tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            <span v-if="modalShowing === 'questions'">
                                {{ translate('site.label.questions') }}
                            </span>
                            <span v-if="modalShowing === 'answers'">
                                {{ translate('site.label.answers') }}
                            </span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <router-link tag="a"
                                     v-if="modalShowing === 'questions'"
                                     v-for="question in questions"
                                     :key="question.id"
                                     target="_blank"
                                     :to="'/question-detail/' + question.id"
                                     class="question d-block border border-secondary modal-question bg-light p-3 mb-3">
                            <question :question="question" :limited="true"></question>
                        </router-link>


                        <router-link tag="a"
                                     v-if="modalShowing === 'answers'"
                                     v-for="answer in answers"
                                     :key="answer.id"
                                     target="_blank"
                                     :to="'/question-detail/' + answer.question.id"
                                     style="color: #000"
                                     class="question d-block modal-question bg-light">
                            <answer :answer="answer"
                                    :questionOwner="profileOwner" ></answer>
                        </router-link>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ translate('site.btn.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import helpers from "../helpers";
    import Question from "./Question";
    import Answer from "./Answer";

    export default {
        data() {
            return {
                name: '',
                location: '',
                linkedin_profile: '#',
                linkedin_profile_new: '',
                linkedin_editing: false,
                overall_point: 0,
                skills: [],
                skill: null,
                active_skill_id: 0,
                languages: [],
                languages_all: [],
                answers: 0,
                questions: 0,
                imageUrl: '',
                image: null,
                modalShowing: '',
            }
        },
        methods: {
            addSkill() {
                // Check skill written, then add btn clicked
                if($(".skills-select").select2('data')[0]['id'] !== '') {
                    axios.post('/skill', {
                        'name': $(".skills-select").select2('data')[0]['text'],
                    }).then(res => {
                        this.skill = '';

                        let skill = res.data.success;
                        this.skills.push(skill);

                        this.flashMessage.success({
                            title: 'Success Message',
                            message: 'Skill successfully added!',
                        });
                    }).catch(err => {
                        let error = err.response.data.message;

                        this.flashMessage.show({
                            status: 'error',
                            title: 'Error Message',
                            message: error,
                        })
                    }).finally(function () {
                        $(".skills-select").val('').trigger('change');
                    });
                }
            },
            detachSkillFromUser() {
                let result = confirm("Are you sure to delete selected skill?");
                if (result) {
                    axios.delete('/skill/' + this.active_skill_id).then(res => {
                        let id = this.active_skill_id;
                        this.skills = $.grep(this.skills, function (e) {
                            return e.id !== id;
                        });

                        this.flashMessage.success({
                            title: 'Success Message',
                            message: 'Skill successfully detached!',
                        });
                    }).catch(err => {
                        let error = err.response.data.message;

                        this.flashMessage.show({
                            status: 'error',
                            title: 'Error Message',
                            message: error,
                        })
                    });
                }
            },
            addLanguage() {
                axios.post('user/language', {
                    'id': $(".languages-select").val(),
                }).then(res => {
                    let language = res.data.success;
                    this.languages.push(language);

                    this.flashMessage.success({
                        title: 'Success Message',
                        message: 'Language successfully added!',
                    });
                }).catch(err => {
                    let error = err.response.data.message;

                    this.flashMessage.show({
                        status: 'error',
                        title: 'Error Message',
                        message: error,
                    })
                }).finally(function () {
                    $(".languages-select").val('').trigger('change');
                });
            },
            getSkillDetails(skill_id) {
                let user_id = this.$route.params.id;
                axios.get(`/skill/${skill_id}/user/${user_id}`).then(res => {
                    let details = res.data.success;

                    // console.log('answers:');
                    // console.log(details.answers);

                    this.answers = details.answers;
                    this.questions = details.questions;

                    this.active_skill_id = skill_id;
                }).catch(err => {
                    console.log(err);
                });
            },
            getAllSkillsDetails() {
                let user_id = this.$route.params.id;
                axios.get(`/skill/general/user/${user_id}`).then(res => {
                    let details = res.data.success;

                    this.answers = details.answers;
                    this.questions = details.questions;

                    this.active_skill_id = 0;
                }).catch(err => {
                    console.log(err);
                });
            },
            fileInputOnChange(e) {
                const file = e.target.files[0];
                this.imageUrl = URL.createObjectURL(file);

                // Uploading File
                const formData = new FormData();
                formData.append('image', file, file.name);
                axios.post('/updateProfileImage', formData)
                    .then(res => {
                        // console.log(res);

                        this.flashMessage.success({
                            title: 'Success Message',
                            message: 'Your Profile Photo successfully changed!',
                        });
                    }).catch(err => {
                    console.log(err);
                });
            },
            deleteLanguage(id) {
                let result = confirm("Are you sure to delete language?");
                if (result) {
                    // console.log(id);
                    axios.delete('user/language', {
                        data: {
                            'id': id,
                        }
                    }).then(res => {
                        // console.log(res);
                        this.languages = $.grep(this.languages, function (e) {
                            return e.id != id;
                        });

                        this.flashMessage.success({
                            title: 'Success Message',
                            message: 'Language successfully detached!',
                        });
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
            showLinkedinInput() {
                this.linkedin_editing = true;
            },
            cancelLinkedinEditing() {
                this.linkedin_editing = false;
            },
            updateLinkedinLink() {
                axios.put('/user', {
                    'linkedin_profile': this.linkedin_profile_new
                }).then(res => {
                    let user = res.data.success;
                    // console.log(user);

                    this.linkedin_profile = user.linkedin_profile;
                    this.linkedin_profile_new = this.linkedin_profile;
                    this.linkedin_editing = false;

                    this.flashMessage.success({
                        title: 'Success Message',
                        message: 'Linkedin Profile successfully updated!',
                    });
                }).catch(err => {
                    let error = err.response.data.message;
                    this.linkedin_editing = false;

                    this.flashMessage.show({
                        status: 'error',
                        title: 'Error Message',
                        message: error,
                    });
                });
            },
            getUserDetails(user_id) {
                axios.get('/user/' + user_id)
                    .then(res => {
                        let user = res.data.success;
                        // console.log(user);

                        this.name = user.name;
                        this.imageUrl = user.profile_image;
                        this.linkedin_profile = user.linkedin_profile;
                        this.linkedin_profile_new = this.linkedin_profile;
                        this.overall_point = user.overall_point;
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            getUserSkills(user_id) {
                axios.get('/skill/user/' + user_id)
                    .then(res => {
                        let skills = res.data.success;
                        // console.log(skills);

                        this.skills = skills;
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            getUserLanguages(user_id) {
                axios.get('/language/user/' + user_id)
                    .then(res => {
                        let languages = res.data.success;
                        // console.log(languages);

                        this.languages = languages;
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            getAllLanguages() {
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
        },
        watch: {
            '$route.params.id': function(id) {
                this.getUserDetails(id);

                // Getting User's Skills
                this.getUserSkills(id);

                // Getting User's Languages
                this.getUserLanguages(id);
            }
        },
        computed: {
            profileOwner() {
                return auth.user.id == this.$route.params.id;
            },
        },
        beforeMount() {
            if (typeof this.$route.params.id == 'undefined') {
                this.$router.push({name: 'profile', params: {id: auth.user.id}});
            }

            // Check for Authentication [Redirecting if not logged in]
            // helpers.redirectIfNotLoggedId(this.$router);

            // Getting User Details
            let user_id = this.$route.params.id;
            this.getUserDetails(user_id);

            // Getting User's Skills
            this.getUserSkills(user_id);

            // Getting User's Languages
            this.getUserLanguages(user_id);

            // Getting All Languages
            this.getAllLanguages();

            // Getting and setting General Skills Statistics
            this.getAllSkillsDetails();

        },
        mounted() {
            // Mounting Select2 Plugin
            $('.languages-select').select2();

            // Autocomplete for Skills
            $('.skills-select').select2({
                tags: true,
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
        },
        components: {
            Question,
            Answer,
        }
    }
</script>
