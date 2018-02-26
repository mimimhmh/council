<template>
    <button class="btn btn-secondary btn-sm like-button" @click="toggle">
        <span v-text="favoritesCount"></span>
        <i class="fa " v-bind:class="classes"></i>
    </button>
</template>

<script>
    export default {
        props: ['reply'],

        data() {
            return {
                favoritesCount: this.reply.favoritesCount,
                isFavorited: this.reply.isFavorited
            }
        },

        computed: {
            classes() {
                return [
                    this.isFavorited ? 'fa-heart' : 'fa-heart-o'
                ];
            },

            endpoint() {
                return '/replies/' + this.reply.id + '/favorites';
            },
        },

        methods: {
            toggle() {
                this.isFavorited ? this.destroy() : this.create();
            },

            create() {
                axios.post(this.endpoint);
                this.isFavorited = true;
                this.favoritesCount++;
            },

            destroy() {
                axios.delete(this.endpoint);
                this.isFavorited = false;
                this.favoritesCount--;
            }
        }
    }
</script>

<style>
    .like-button {
        cursor: pointer;
    }

    .like-button i {
        color: #ff4628;
    }
</style>

