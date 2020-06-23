import { shallowMount } from '@vue/test-utils'
import Component from '../../../components/fh/Url.vue'

const wrapper = shallowMount(Component, {
  propsData: {
    guid: '123-uuid',
    field: 'urlfield',
    value: 'http://localhost'
  }
})

describe('URL fh component tests', () => {
  it('should properly set props', () => {
    expect(wrapper.props('guid')).toBe('123-uuid')
    expect(wrapper.props('field')).toBe('urlfield')
    expect(wrapper.props('value')).toBe('http://localhost')
    expect(wrapper.vm.val).toBe('http://localhost')
    // testing data set

    wrapper.setData({ 'val': 'https://google.com' })
    expect(wrapper.vm.val).toBe('https://google.com')

    wrapper.vm.$emit('input-value-updated', wrapper.props('field'), wrapper.props('guid'), wrapper.vm.val)
    expect(wrapper.emitted('input-value-updated')).toBeTruthy()

    let sentValues = wrapper.emitted('input-value-updated')[0]
    expect(sentValues).toEqual(expect.arrayContaining(["urlfield", "123-uuid", "https://google.com"]))
  })
})
