<template>
    <div>
        <text-input
            type="number"
            :placeholder="originalStock"
            min="0"
            step="1"
            :value="value"
            @input="update" />
    </div>
</template>

<script>
    export default {
        name: 'stock-fieldtype',
        mixins: [Fieldtype],
        computed: {
            originalStock() {
                return this.$store.state.publish.base.values.stock;
            },
        }
    };
</script>
