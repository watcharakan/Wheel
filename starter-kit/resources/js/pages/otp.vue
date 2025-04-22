<template>
  <div
    class="verification-page"
    :style="{
      backgroundImage: `url(${backgroundImage})`,
      backgroundPosition: 'center',
      backgroundSize: 'cover',
      backgroundRepeat: 'no-repeat',
    }"
  >
    <!-- ส่วน Header (Logo มุมบนซ้าย) พร้อมหัวข้อ Verification Code -->
    <div class="header">
      <img :src="logo" alt="BeBetter Logo" class="logo" />
      <h1 class="title">Verification <br>Code</h1>
    </div>

    <!-- ส่วนคอนเทนต์กึ่งกลางหน้า -->
    <div class="content">
      <!-- กล่อง (card) โปร่งแสงสีขาว -->
      <div class="card-container">
        <!-- ข้อความแนะนำ -->
        <p class="instruction">
          <!-- ไอคอนมือถือ bx-mobile ในกรอบสีทอง -->
          <i class="bx bx-mobile icon-mobile"></i>
          <strong>Check your phone</strong><br />
          Please enter the verification code
        </p>

        <!-- กล่องกรอกโค้ด 4 หลัก -->
        <div class="code-container">
          <v-text-field
            v-model="code1"
            class="code-input"
            maxlength="1"
            outlined
            hide-details
          />
          <v-text-field
            v-model="code2"
            class="code-input"
            maxlength="1"
            outlined
            hide-details
          />
          <v-text-field
            v-model="code3"
            class="code-input"
            maxlength="1"
            outlined
            hide-details
          />
          <v-text-field
            v-model="code4"
            class="code-input"
            maxlength="1"
            outlined
            hide-details
          />
        </div>

        <!-- ลิงก์ resend -->
        <p class="resend-text">
          Didn’t get a code?
          <a @click="handleResend" class="resend-link">Click to resend</a>
        </p>

        <!-- ปุ่ม Cancel และ Verify -->
        <div class="button-group">
          <v-btn class="cancel-button" @click="handleCancel">Cancel</v-btn>
          <v-btn class="verify-button" @click="handleVerify">Verify</v-btn>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import backgroundImage from '@images/BB/BG.jpg'
import logo from '@images/BB/Logo2-01.png'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

// State เก็บค่า OTP
const code1 = ref('')
const code2 = ref('')
const code3 = ref('')
const code4 = ref('')

const router = useRouter()

function handleResend() {
  console.log('Resend code')
}

function handleCancel() {
  router.push('/')
}

function handleVerify() {
  const codeValue = code1.value + code2.value + code3.value + code4.value
  console.log('Verifying code:', codeValue)
  router.push('/home')
}
</script>

<style scoped>
/* ======================================
   โครงหลัก (ตั้งค่าพื้นหลังผ่าน :style ด้วย backgroundImage)
   ====================================== */
.verification-page {
  position: relative;
  min-block-size: 100vh;
  padding-inline: 24px !important;
}

/* ======================================
   Header (Logo + Title มุมบนซ้าย)
   ====================================== */
.header {
  position: absolute !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-start !important;
  gap: 8px !important; /* ระยะห่างระหว่าง Logo กับ Title */
  inset-block-start: 24px !important;
  inset-inline-start: 24px !important;
}

.logo {
  block-size: auto !important;
  inline-size: 150px !important;
}

.title {
  margin: 0 !important;
  color: #2c2f36 !important;
  font-size: 42px !important;
  font-weight: 700 !important;
  margin-block-start: 60px !important;
}

/* ======================================
   Content (กึ่งกลางแนวตั้ง)
   ====================================== */
.content {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-block-start: 35px;
  min-block-size: 100vh !important;
}

/* Card Container (โปร่งแสงสีขาว) */
.card-container {
  border-radius: 12px !important;
  backdrop-filter: blur(4px) !important;
  background-color: rgba(255, 255, 255, 22%) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%) !important;
  inline-size: 100% !important;
  margin-block-start: 35px;
  max-inline-size: 350px !important;

  /* จัดระยะด้านในของการ์ด */
  padding-block: 32px 24px !important; /* บน-ล่าง */
  padding-inline: 24px !important;     /* ซ้าย-ขวา */
}

/* ข้อความ Check your phone ... */
.instruction {
  margin: 0 !important;
  color: #555 !important;
  line-height: 1.4 !important;
  margin-block-end: 24px !important;
  text-align: start !important;
}

/* ไอคอนมือถือในกรอบทอง */
.icon-mobile {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  border: 2px solid #d4af37 !important; /* ขอบทอง */
  border-radius: 8px !important;
  block-size: 48px !important;
  color: #2c2f36 !important; /* สีไอคอนมือถือ */
  font-size: 24px !important;

  /* ขนาดกรอบ */
  inline-size: 48px !important;
  margin-inline-end: 6px !important;
}

/* ======================================
   ช่องกรอกโค้ด OTP
   ====================================== */
.code-container {
  display: flex !important;
  gap: 12px !important;
  margin-block-end: 16px !important;
}

/* ช่อง OTP ใหญ่ + ขอบทอง */
.code-input.v-text-field {
  inline-size: 130px !important; /* ความกว้าง */
  text-align: center !important;
}

/* เพิ่มความสูงช่อง */
.code-input.v-text-field .v-field__control {
  min-block-size: 90px !important;
}

/* ขยายขนาดตัวเลข */
.code-input input {
  font-size: 55px !important;
  text-align: center !important;
}

/* ขอบ outlined ทอง (Vuetify 3) */
.code-input.v-text-field .v-field--outlined .v-field__outline::before,
.code-input.v-text-field .v-field--outlined .v-field__outline::after {
  border-width: 3px !important; /* เพิ่มความหนาอีกหน่อย */
  border-color: #d4af37 !important; /* gold */
}

/* ======================================
   ลิงก์ Resend
   ====================================== */
.resend-text {
  color: #666 !important;
  font-size: 0.9rem !important;
  margin-block-end: 24px !important;
}

.resend-link {
  color: #007bff !important;
  cursor: pointer !important;
}

/* ======================================
   ปุ่ม (จัดเรียงกลาง)
   ====================================== */
.button-group {
  display: flex !important;
  justify-content: center !important;
  gap: 16px !important;
}

/* ปุ่มยกเลิก: ขอบทอง พื้นโปร่ง */
.cancel-button.v-btn {
  border: 2px solid #d4af37 !important; /* ขอบทอง */
  background-color: #ffffff15 !important; /* พื้นขาวโปร่งนิด ๆ */
  color: #2c2f36 !important;
  font-weight: 500 !important;
  text-transform: none !important;
}

/* ปุ่ม Verify: พื้นเข้ม, ตัวอักษรขาว */
.verify-button.v-btn {
  background-color: #2c2f36 !important;
  color: #fff !important;
  font-weight: 600 !important;
  text-transform: none !important;
}
</style>
