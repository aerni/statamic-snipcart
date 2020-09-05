<template>
    <div>
        <text-input
            type="number"
            :prepend="symbol"
            :placeholder="originalPrice"
            :isReadOnly="isReadOnly"
            min="0"
            step="0.01"
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
                let basePrice = this.$store.state.publish.base.values.price;

                if (basePrice) {
                    return `Base Price: ${basePrice}`;
                }
            },
            symbol() {
                return this.meta[this.site]['symbol'];
            },
            site() {
                return this.$store.state.publish.base.site;
            }
        }
    };
</script>
