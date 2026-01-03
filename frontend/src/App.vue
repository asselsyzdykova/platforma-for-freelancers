<template>
  <HeaderComponent />
  <RouterView />
  <FooterComponent />
</template>

<script>
import HeaderComponent from '@/components/Layout/HeaderComponent.vue'
import FooterComponent from '@/components/Layout/FooterComponent.vue'

export default {
  name: 'App',
  components: {
    HeaderComponent,
    FooterComponent,
  },
}
</script>
