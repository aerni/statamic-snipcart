import MoneyFieldtype from './components/MoneyFieldtype.vue'

Statamic.booting(() => {
    Statamic.$components.register('money-fieldtype', MoneyFieldtype);
});