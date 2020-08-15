<template>
    <div>
        <text-input
            type="number"
            :prepend="symbol"
            :placeholder="originalPrice"
            :isReadOnly="isReadOnly"
            min="0"
            step="1"
            :value="value"
            @input="update" />
    </div>
</template>

<script>
    export default {
        name: 'money-fieldtype',
        mixins: [Fieldtype],
        computed: {
            originalPrice() {
                return this.$store.state.publish.base.values.price;
            },
            symbol() {
                return this.meta[this.$store.state.publish.base.site]['symbol'];
            }
        }
    };
</script>
