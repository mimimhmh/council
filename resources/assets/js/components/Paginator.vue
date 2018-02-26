<template>
    <ul class="pagination pagination-sm" v-if="shouldPaginate">
        <li class="page-item" v-show="prevUrl">
            <a class="page-link" href="#"
               aria-label="Previous"
               rel="prev" @click.prevent="page--">
                Previous
            </a>
        </li>

        <li class="page-item" v-for="n in dataSet.last_page">
            <a class="page-link" href="#" @click.prevent="page=n">{{n}}</a>
        </li>

        <li class="page-item" v-show="nextUrl" @click.prevent="page++">
            <a class="page-link" href="#" aria-label="Next" rel="next">Next</a>
        </li>
    </ul>
</template>

<script>
    export default {
        props: ['dataSet'],

        watch: {
            dataSet() {
                this.page = this.dataSet.current_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },

            page() {
                this.broadcast().updateUrl();
            }
        },

        data() {
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false
            };
        },

        computed: {
            shouldPaginate() {
                return !!this.prevUrl || !!this.nextUrl;
            }
        },

        methods: {
            broadcast() {
                return this.$emit('changed', this.page);
            },

            updateUrl() {
                history.pushState(null, null, '?page=' + this.page);
            }
        }
    }

</script>