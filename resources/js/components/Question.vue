<template>
    <div v-if="question">
        <h5>
            <a href="#">
                {{ question.title }}
            </a>
        </h5>
        <p>
            <span v-if="limited && question.content">
                <pre>{{ question.content.substring(0, 180) }}</pre>
            </span>
            <span v-else>
                <pre>{{ question.content }}</pre>
            </span>
        </p>
        <div class="row">
            <div class="col-md-5">
                <span>
                                    {{ question.created_at }}
                                    <b>
                                        <a :href="'/profile/' + question.user.id" target="_blank">by {{ question.user.name }}</a>
                                        |
                                        <span class="badge badge-dark text-light" v-if="question.language">
                                            {{ question.language.text }}
                                        </span>
                                    </b>
                                </span>
            </div>
            <div class="col-md-7">
                <ul class="skills pull-right list-inline">
                    <li v-for="skill in question.skills"
                        :key="skill.id"
                        class="list-inline-item badge badge-success h3 single-skill">
                        {{ skill.text }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Question",
        props: ['question', 'limited'],
        data() {
            return {}
        }
    }
</script>

<style scoped>

</style>
