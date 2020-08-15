<template>
    <div>
        <text-input
            type="number"
            :append="short"
            :isReadOnly="isReadOnly"
            min="0"
            step="1"
            :value="value"
            @input="update" />
    </div>
</template>

<script>
    export default {
        name: 'dimension-fieldtype',
        mixins: [Fieldtype],
        data() {
            return {
                short: this.meta.short
            }
        }
    };
</script>
