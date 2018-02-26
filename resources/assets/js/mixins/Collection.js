export default {

    data(){
        return {
            items: []
        };
    },

    methods: {
        addData(item) {
            this.items.push(item);

            this.$emit('added');
        },

        remove(index) {
            this.items.splice(index, 1);

            this.$emit('removed');

        }
    }
}