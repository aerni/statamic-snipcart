<template>
    <div>
        <text-input
            type="number"
            :prepend="symbol"
            min="0"
            step="0.01"
            :value="value"
            @input="update" />
    </div>
</template>

<script>
    export default {
        name: 'currency-fieldtype',
        mixins: [Fieldtype],
        data() {
            return {
                symbol: this.meta.symbol
            }
        }
    };
</script>