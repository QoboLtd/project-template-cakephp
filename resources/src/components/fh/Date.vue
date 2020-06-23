<template>
  <div
    :class="{'required': isRequired}"
    class="form-group"
  >
    <label
      v-if="label !== ''"
      :for="guid"
      class="control-label"
    >
      {{ label }}
    </label>
    <div
      :class="[sizeClass]"
      class="input-group"
    >
      <div class="input-group-addon">
        <i class="fa fa-calendar" />
      </div>
      <input
        type="text"
        autocomplete="off"
        class="form-control"
      >
    </div>
  </div>
</template>
<script>
import * as $ from 'jquery'
import datepicker from 'bootstrap-datepicker'
import { MAGIC_VALUE_WRAPPER } from '../../utils/constants.js'

export default {
  props: {
    field: {
      type: String,
      required: true
    },
    guid: {
      type: String,
      required: true
    },
    value: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: '',
      required: false
    },
    size: {
      type: String,
      default: '',
      required: false
    },
    isRequired: {
      type: Boolean,
      default: false,
      required: false
    }
  },
  data () {
    return {
      val: this.value,
      magicValueClass: 'datepicker-magic-value',
      magicValues: ['Today', 'Yesterday', 'Tomorrow']
    }
  },
  computed: {
    sizeClass () {
      if (this.size === 'input-sm') {
        return 'input-group-sm'
      }

      return this.size
    }
  },
  watch: {
    val () {
      this.$emit('input-value-updated', this.field, this.guid, this.val)
    }
  },
  mounted () {
    const self = this
    const $input = $(this.$el).find('input')
    const options = {
      autoclose: true,
      forceParse: false,
      format: 'yyyy-mm-dd',
      weekStart: 1
    }

    if (this.val) {
      $input.val(this.val)
    }

    $input.datepicker(options)

    this.magicValues.forEach(function (item) {
      // convert magic value to label, for example "%%today%%" becomes "Today"
      if (self.val === MAGIC_VALUE_WRAPPER + item.toLowerCase() + MAGIC_VALUE_WRAPPER) {
          $input.val(item)
      }
    })

    $input.on('changeDate', function (e) {
      self.val = $input.val()
    })

    $input.on('show', function (e) {
      var input = this

      $('.datepicker tfoot').empty()

      self.magicValues.forEach(function (item) {
        $('.datepicker tfoot').append('<tr><th colspan="7" data-magic-value="1">' + item + '</th></tr>')
      })

      $('th[data-magic-value="1"]').on('click', function () {
        const value = $(this).text()
        $input.val(value)
        self.val = MAGIC_VALUE_WRAPPER + value.toLowerCase() + MAGIC_VALUE_WRAPPER

        $input.datepicker('hide')
      })
    })
  }
}
</script>
<style lang="css">
  @import '~bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css';
</style>
