<template>
  <div class="landing-container">
    <h1>Landing Page</h1>
    <!-- Other content -->

    <ScrollToTop />

    <a
      class="contact-btn"
      href="https://line.me"
      target="_blank"
      rel="noopener"
    >
      <svg
        viewBox="0 0 36 32"
        xmlns="http://www.w3.org/2000/svg"
        class="line-logo"
      >
        <path
          fill="#06c755"
          d="M18 0C7.7 0 0 6.4 0 15.2c0 4.7 2.4 8.8 6.4 11.6v5.2L12 29c1.9.5 3.9.8 6 .8 10.3 0 18-6.4 18-15.2S28.3 0 18 0Z"
        />
        <path
          fill="#fff"
          d="m9.5 10.7-.5 10.6h3.2l.5-10.6H9.5zm4.9 0v10.6h3.2V16h3.2v-2.7h-3.2v-2.6h3.2V8h-6.4zm9.1 0 4 10.6h3.3l4-10.6h-3.4l-2.3 6.8-2.3-6.8h-3.3z"
        />
      </svg>
    </a>
  </div>
</template>

<script setup>
import ScrollToTop from '@core/components/ScrollToTop.vue'
</script>

<style scoped>
.landing-container {
  min-height: 200vh;
  padding: 2rem;
}

.contact-btn {
  position: fixed;
  z-index: 998;
  right: 25px;
  bottom: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background-color: #06c755;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.line-logo {
  width: 32px;
  height: 32px;
}
</style>
