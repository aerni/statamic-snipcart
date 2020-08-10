import CurrencyFieldtype from './components/CurrencyFieldtype.vue'
import DimensionFieldtype from './components/DimensionFieldtype.vue'

Statamic.booting(() => {
    Statamic.$components.register('currency-fieldtype', CurrencyFieldtype);
    Statamic.$components.register('dimension-fieldtype', DimensionFieldtype);
});