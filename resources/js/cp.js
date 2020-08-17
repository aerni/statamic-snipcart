import DimensionFieldtype from './components/DimensionFieldtype.vue'
import MoneyFieldtype from './components/MoneyFieldtype.vue'
import StockFieldtype from './components/StockFieldtype.vue'

Statamic.booting(() => {
    Statamic.$components.register('dimension-fieldtype', DimensionFieldtype);
    Statamic.$components.register('money-fieldtype', MoneyFieldtype);
    Statamic.$components.register('stock-fieldtype', StockFieldtype);
});
