<template>
  <HeaderComponent />
  <ToastContainer />
  <RouterView />
  <FooterComponent />
</template>

<script>
import HeaderComponent from '@/components/Layout/HeaderComponent.vue'
import FooterComponent from '@/components/Layout/FooterComponent.vue'
import ToastContainer from '@/components/UI/ToastContainer.vue'

export default {
  name: 'App',
  components: {
    HeaderComponent,
    FooterComponent,
    ToastContainer,
  },
}
</script>
