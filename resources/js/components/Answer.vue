<template>
    <div class="answer border border-primary bg-light p-3 mb-3">
        <div v-if="!editing" class="row">
            <div class="col-sm-1 col-3 text-center">
                <i
                    @click="voteUp()"
                    :class="{ selected: this.votedUp}"
                    class="vote-icon fa fa-chevron-up" aria-hidden="true"></i>
                <span>{{ answer.votes }}</span>
                <i
                    @click="voteDown()"
                    :class="{ selected: this.votedDown}"
                    class="vote-icon fa fa-chevron-down" aria-hidden="true"></i>
                <i
                    @click="makeBestAnswer()"
                    :class="{selected: answer.is_best }"
                    v-if="answer.is_best || (this.questionOwner && !this.answerOwner)"
                    class="vote-icon fa fa-check" aria-hidden="true"></i>
            </div>
            <div class="col-sm-11 col-9">
                <p>
                    {{ answer.content }}
                </p>
                <span>{{ answer.created_at }}
                                    <b>
                                        <a :href="/profile/ + answer.user.id" target="_blank">by {{ answer.user.name }}</a>
                                    </b>
                </span>

                <div class="mt-3" v-if="answerOwner">
                    <button @click="editing = true" class="btn btn-warning btn-sm">Edit</button>
                    <button @click="deleteAnswer" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </div>
        </div>

        <div v-else class="row">
            <div class="col-12">
                <div class="form-group">
                    <textarea class="form-control"
                              name="content"
                              v-model="editContent"
                              id="content" rows="4"></textarea>
                    <button
                        @click="updateAnswer()"
                        class="btn btn-success mt-2 pull-right">
                        Update
                    </button>
                    <button
                        @click="editing = false"
                        class="btn btn-info mt-2 mr-2 text-white pull-right">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import helpers from "../helpers";

    export default {
        name: "Answer",
        props: ['answer', 'questionOwner'],
        data() {
            return {
                'editing': false,
                'editContent': '',
            }
        },
        methods: {
            updateAnswer() {
                axios.patch(`/answer/${this.answer.id}`, {
                    'content': this.editContent,
                }).then(res => {
                    console.log(res);

                    this.answer.content = this.editContent;
                    this.editing = false;

                    this.flashMessage.success({
                        title: 'Success Message',
                        message: 'Answer successfully updated!',
                    });
                }).catch(err => {
                    let error = err.response.data.message;

                    this.flashMessage.show({
                        status: 'error',
                        title: 'Error Message',
                        message: error,
                    });
                });
            },
            deleteAnswer() {
                let result = confirm("Are you sure to delete answer?");
                if (result) {
                    axios.delete(`/answer/${this.answer.id}`)
                        .then(res => {
                            this.flashMessage.success({
                                title: 'Success Message',
                                message: 'Answer successfully deleted!',
                            });

                            bus.$emit('answerDeleted');
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
            makeBestAnswer() {
                axios.post(`/answer/${this.answer.id}/best`)
                    .then(res => {
                        this.flashMessage.success({
                            title: 'Success Message',
                            message: 'Answer successfully chosen as best answer!',
                        });

                        bus.$emit('bestAnswerSelected');
                    }).catch(err => {
                    let error = err.response.data.message;

                    this.flashMessage.show({
                        status: 'error',
                        title: 'Error Message',
                        message: error,
                    });
                });
            },
            voteUp() {
                axios.post(`/answer/${this.answer.id}/up`).then(res => {
                    // Checking if voted already or not
                    if(this.votedUp) {
                        // When up voted, just removing it
                        const index = this.answer.vote_up_users.indexOf(auth.user.id);
                        if (index > -1) {
                            this.answer.vote_up_users.splice(index, 1);
                        }
                        this.answer.votes -= 1;
                    } else {
                        // When Up vote, if before down voted, removing it and adding id to vote_up_users
                        const index = this.answer.vote_down_users.indexOf(auth.user.id);
                        if (index > -1) {
                            this.answer.vote_down_users.splice(index, 1);
                            this.answer.votes += 1;
                        }
                        this.answer.vote_up_users.push(auth.user.id);
                        this.answer.votes += 1;
                    }
                }).catch(err => {
                    console.log(err);
                });
            },
            voteDown() {
                axios.post(`/answer/${this.answer.id}/down`).then(res => {
                    // Checking if voted already or not
                    if(this.votedDown) {
                        // When down voted, just removing it
                        const index = this.answer.vote_down_users.indexOf(auth.user.id);
                        if (index > -1) {
                            this.answer.vote_down_users.splice(index, 1);
                        }
                        this.answer.votes += 1;
                    } else {
                        // When Down vote, if before down voted, removing it and adding id to vote_down_users
                        const index = this.answer.vote_up_users.indexOf(auth.user.id);
                        if (index > -1) {
                            this.answer.vote_up_users.splice(index, 1);
                            this.answer.votes -= 1;
                        }
                        this.answer.vote_down_users.push(auth.user.id);
                        this.answer.votes -= 1;
                    }
                }).catch(err => {
                    console.log(err);
                });
            }
        },
        computed: {
            answerOwner() {
                return helpers.checIfUserAnswerOwner(this.answer);
            },
            votedUp() {
                return helpers.checkUserVoted(this.answer.vote_up_users);
            },
            votedDown() {
                return helpers.checkUserVoted(this.answer.vote_down_users);
            },
        },
        mounted() {
            this.editContent = this.answer.content;
        }
    }
</script>

<style scoped>

</style>
