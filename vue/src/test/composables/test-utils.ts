import { createApp, h } from 'vue';

interface Child<R> {
  wrapper: () => R
}

export function withSetup<R>(
  composable: () => R,
  options: {provider?: () => void}
): {result: R, unmount: () => void} {
  const Child = {
    setup(): Child<R> {
      const result = composable()
      const wrapper = () => result
      return { wrapper }
    },
    render() {},
  }

  const app = createApp({
    setup() {
      options.provider?.()
    },
    render() {
      return h(Child, {
        ref: 'child',
      })
    },
  })
  const vm = app.mount(document.createElement('div'));

  return {
    result: (vm.$refs.child as Child<R>).wrapper(),
    unmount: () => app.unmount(),
  }
}
