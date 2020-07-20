<template>
    <div>
        <text-input
            type="number"
            :append="abbr"
            min="0"
            step="1"
            :value="value"
            @input="update" />
    </div>
</template>

<script>
    export default {
        name: 'weight-fieldtype',
        mixins: [Fieldtype],
        data() {
            return {
                abbr: this.meta.abbr
            }
        }
    };
</script>