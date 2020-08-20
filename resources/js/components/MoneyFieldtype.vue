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
                return this.$store.state.publish.base.values.price;
            },
            symbol() {
                if (this.meta[this.site]) {
                    return this.meta[this.site]['symbol'];
                }

                return this.meta[this.siteHandle()]['symbol'];
            },
            site() {
                return this.$store.state.publish.base.site;
            }
        },
        methods: {
            siteHandle() {
                let pathArray = window.location.pathname.split('/');
                return pathArray[pathArray.length - 1];
            }
        }
    };
</script>
