<template>
  <vue-final-modal
    v-slot="{ params, close }"
    v-bind="$attrs"
    classes="modal-container"
    content-class="modal-content w-[500px]"
  >
    <span
      class="modal__title my-2"
    >
      <slot name="title" />
    </span>
    <div class="modal__content my-2">
      <slot />
    </div>
    <button
      class="modal__close"
      @click="close"
    >
      X
    </button>
  </vue-final-modal>
</template>
<script>
export default {
  name: 'ModalContainer',
  inheritAttrs: false,
  emits: ['confirm', 'cancel'],
};
</script>
<style scoped>
::v-deep(.modal-container) {
  display: flex;
  justify-content: center;
  align-items: center;
}
::v-deep(.modal-content) {
  position: relative;
  display: flex;
  flex-direction: column;
  max-height: 90%;
  margin: 0 1rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.25rem;
  background: #fff;
}
.modal__title {
  margin: 0 2rem 0 0;
  font-size: 1.5rem;
  font-weight: 700;
}
.modal__content {
  overflow-y: auto;
  overflow-x: auto;
}
.modal__close {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
}
</style>
