import CurrencyFieldtype from './components/CurrencyFieldtype.vue'
import LengthFieldtype from './components/LengthFieldtype.vue'
import WeightFieldtype from './components/WeightFieldtype.vue'

Statamic.booting(() => {
    Statamic.$components.register('currency-fieldtype', CurrencyFieldtype);
    Statamic.$components.register('length-fieldtype', LengthFieldtype);
    Statamic.$components.register('weight-fieldtype', WeightFieldtype);
});