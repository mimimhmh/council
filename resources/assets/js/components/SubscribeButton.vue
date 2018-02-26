<template>
    <button :class="classes" v-text="isSubscribed" @click="subscribe">Subscribe</button>
</template>

<script>
    export default {
        props: ['active'],

        data: function () {
            return {
                activated: this.active
            };
        },

        computed: {
            classes() {
                return ['btn', this.activated ? 'btn-primary' : 'btn-default', 'btn-md'];
            },

            isSubscribed() {
                return this.activated ? 'Unsubscribe' : 'Subscribe'
            }
        },

        methods: {
            subscribe() {
                let requestType = this.activated ? 'delete' : 'post';

                axios[(requestType)](location.pathname + '/subscriptions');

                this.activated = ! this.activated;

            }
        }
    }
</script>