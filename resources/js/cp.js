import CurrencyFieldtype from './components/CurrencyFieldtype.vue'
import DimensionFieldtype from './components/DimensionFieldtype.vue'
import StockFieldtype from './components/StockFieldtype.vue'

Statamic.booting(() => {
    Statamic.$components.register('currency-fieldtype', CurrencyFieldtype);
    Statamic.$components.register('dimension-fieldtype', DimensionFieldtype);
    Statamic.$components.register('stock-fieldtype', StockFieldtype);
});
