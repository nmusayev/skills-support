<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ translate('site.heading.users') }}</div>

                    <div class="card-body">

                        <form @submit.prevent id="user-search-form" class="form-inline justify-content-center mb-4">
                            <input id="user-search" v-model="name" class="form-control w-50" type="text"
                                   @keydown.enter.prevent="getUsers(1)"
                                   name="search-key" :placeholder="translate('site.label.search_here')">

                            <a
                                href="/search-results/"
                                id="search-btn"
                                @click.prevent="getUsers(1)"
                                class="btn btn-success">
                                {{ translate('site.btn.search') }}
                            </a>
                        </form>

                        <div class="users">
                            <router-link tag="a"
                                         v-for="user in users.data"
                                         target="_blank"
                                         :key="user.id"
                                         :to="'/profile/' + user.id"
                                         class="user d-block border border-success bg-light p-3 mb-3">
                                <div class="row">
                                    <div class="col-sm-3 col-6 text-center">
                                        <img :src="user.profile_image"
                                             class="img-thumbnail"
                                             style="object-fit: cover; width: 100px; height: 100px; border-radius: 100px"
                                             alt="profile image">
                                    </div>
                                    <div class="col-sm-9 col-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <span class="h5 d-inline-block mt-3 text-black">{{ user.name }}</span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-black">Points: {{ user.overall_point}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </router-link>
                            <div v-if="users.data.length === 0">
                                No User Found
                            </div>
                        </div>

                        <pagination :data="linkData"
                                    :limit="3"
                                    align="center"
                                    class="mt-4"
                                    @pagination-change-page="getUsers">
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
    import helpers from "../helpers";

    export default {
        data() {
            return {
                users: [],
                name: '',
                linkData: {},
            }
        },
        methods: {
            // Our method to GET users from a Laravel endpoint
            getUsers(page = 1) {
                let n = page;
                axios.get(`/user/all?page=${n}&name=${this.name}`)
                    .then(res => {
                        // console.log(res.data);
                        this.users = res.data;

                        this.linkData = {
                            next_page_url: this.users.links.next,
                            prev_page_url: this.users.links.prev,
                            ...this.users.meta,
                        };
                    });
            }
        },
        mounted() {
            // Check for Authentication [Redirecting if not logged in]
            // helpers.redirectIfNotLoggedId(this.$router);

            // Getting Recently Questions
            this.getUsers();

            // Listeting to Custom Event: 'questionAdded'
            // bus.$on('questionAdded', () => {
            //     // Reload Questions Again
            //     this.getQuestions();
            // });
        },
        components: {

        }
    }
</script>
