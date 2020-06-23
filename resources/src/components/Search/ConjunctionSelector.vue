<template>
  <div>
    <select
      v-model="conjunction"
      class="form-control input-sm"
    >
      <option
        v-for="(item, index) in conjunctions"
        :key="index"
        :value="item.value"
      >
        {{ item.text }}
      </option>
    </select>
  </div>
</template>
<script>
import { CONJUNCTIONS } from '../../utils/search'

export default {
  name: 'ConjunctionSelector',
  data () {
    return {
      conjunctions: CONJUNCTIONS
    }
  },
  computed: {
    conjunction: {
      get() {
        return this.$store.state.search.conjunction
      },
      set(value) {
        this.$store.commit('search/conjunction', value)
      }
    }
  },
}
</script>
