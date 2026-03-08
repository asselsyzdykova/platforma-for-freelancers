<template>
  <div class="cert-modal" @click.self="$emit('close')">

    <button
      class="cert-modal-close"
      @click="$emit('close')"
    >
      ×
    </button>

    <img :src="image" />

  </div>
</template>

<script setup>

defineProps({
  image:String
})

</script>

<style scoped>
.cert-modal{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.7);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:9999;
}

.cert-modal img{
  max-width:90vw;
  max-height:90vh;
  border-radius:16px;
}

.cert-modal-close{
  position:absolute;
  top:20px;
  right:20px;
  width:40px;
  height:40px;
  border-radius:50%;
  border:none;
  font-size:24px;
  cursor:pointer;
}
</style>
