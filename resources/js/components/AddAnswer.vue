<template>
    <div class="border border-primary bg-light p-3 mb-3 mt-3">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="content">{{ translate('site.label.your_answer' )}}</label>
                    <textarea class="form-control"
                              name="content"
                              v-model="form.content"
                              :placeholder="translate('site.label.answer_placeholder')"
                              id="content" rows="4"></textarea>
                    <button
                        @click="addAnswer()"
                        class="btn btn-success mt-2 pull-right">
                        {{ translate('site.btn.add_answer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AddAnswer",
        props: ['question'],
        data() {
            return {
                form: {
                    content: '',
                }
            }
        },
        methods: {
            addAnswer() {
                axios.post('/answer', {
                    'content': this.form.content,
                    'question_id': this.question.id,
                }).then(res => {
                    bus.$emit('answerAdded');

                    // Resetting form filed
                    this.form.content = '';

                    this.flashMessage.success({
                        title: 'Success Message',
                        message: 'Answer successfully added!',
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
        }
    }
</script>

<style scoped>

</style>
