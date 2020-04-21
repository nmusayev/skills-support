<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ translate('site.heading.home') }}</div>

                    <div class="card-body">
<!--                        <div class="info-banner mb-3">-->
<!--                            Skills-Support-->
<!--                        </div>-->
                        <div v-html="translate('site.content.home')">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>
