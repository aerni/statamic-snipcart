<template>
    <div>
        <text-input
            type="number"
            :prepend="symbol"
            min="0"
            step="0.01"
            placeholder="0.00"
            :value="value"
            @input="update" />
    </div>
</template>

<script>
    export default {
        name: 'money-fieldtype',
        mixins: [Fieldtype],
        data() {
            return {
                symbol: this.meta.symbol ?? this.meta.code,
            }
        }
    };
</script>