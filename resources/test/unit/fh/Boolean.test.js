import Component from '../../../src/components/fh/Boolean.vue'
import { shallowMount } from '@vue/test-utils'

describe('Boolean fh component tests', () => {
  it('should properly set props with Value as [Number]', () => {
    const wrapper = shallowMount(Component, {
      propsData: {
        guid: 'boolean-uuid',
        field: 'boolean_field',
        value: 1
      }
    })

    expect(wrapper.props('guid')).toBe('boolean-uuid')
    expect(wrapper.props('field')).toBe('boolean_field')
    expect(wrapper.props('value')).toBe(1)
    expect(wrapper.vm.val).toBe(1)

    wrapper.setData({ 'val': 0 })
    expect(wrapper.vm.val).toBe(0)

    wrapper.vm.$emit('input-value-updated', wrapper.props('field'), wrapper.props('guid'), wrapper.vm.val)
    expect(wrapper.emitted('input-value-updated')).toBeTruthy()

    let sentValues = wrapper.emitted('input-value-updated')[0]
    expect(sentValues).toEqual(expect.arrayContaining(["boolean_field", "boolean-uuid", 0]))
  })
})
