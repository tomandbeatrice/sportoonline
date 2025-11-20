import Checkout from './Checkout.vue';

export default {
  title: 'Order/Checkout',
  component: Checkout,
};

const Template = (args) => ({
  components: { Checkout },
  setup() {
    return { args };
  },
  template: '<Checkout v-bind="args" />',
});

export const Default = Template.bind({});
Default.args = {};