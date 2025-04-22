<template>
  <!-- พื้นหลัง -->
  <div class="sign-in-page" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <!-- ส่วนหัว: โลโก้ + Title -->
    <div class="header-section">
      <div class="logo-wrapper">
        <img :src="logo" alt="BeBetter Logo" class="logo" />
      </div>
      <h1 class="title">Sign In</h1>
    </div>

    <!-- กล่องขาวโปร่งแสงส่วนที่ 1: Name / Phone / Email -->
    <div class="top-form-section">
      <!-- ชื่อ -->
      <div class="field-label">Name *</div>
      <v-text-field
        class="custom-field"
        v-model="name"
        variant="outlined"
        placeholder="Type Your Name"
        required
      >
        <template #prepend-inner>
          <span class="bx bx-user" style="color: #999; font-size: 1.2rem;"></span>
        </template>
      </v-text-field>

      <!-- เบอร์โทร -->
      <div class="field-label">Phone Number *</div>
      <v-text-field
        class="custom-field"
        v-model="phoneNumber"
        variant="outlined"
        placeholder="Type Here"
        required
      >
        <template #prepend-inner>
          <span class="bx bx-phone" style="color: #999; font-size: 1.2rem;"></span>
        </template>
      </v-text-field>

      <!-- อีเมล -->
      <div class="field-label">E-Mail *</div>
      <v-text-field
        class="custom-field"
        v-model="email"
        variant="outlined"
        placeholder="Type Your Email"
        required
      >
        <template #prepend-inner>
          <span class="bx bx-envelope" style="color: #999; font-size: 1.2rem;"></span>
        </template>
      </v-text-field>
    </div>

    <!-- กล่องขาวโปร่งแสงส่วนที่ 2: Checkbox + ปุ่ม Log In -->
    <div class="bottom-form-section2">
      <div class="checkbox-container">
        <v-checkbox
          v-model="agreeTnc"
          :label="'Agree to the Terms and Conditions'"
          color="#DAA520"
        />
        <v-checkbox
          v-model="agreePrivacy"
          :label="'Agree to Privacy Policy'"
          color="#DAA520"
        />
      </div>

      <v-btn
        class="login-btn"
        size="large"
        @click="submitForm"
        :disabled="!canSubmit"
      >
        <span class="bx bx-log-in" style="font-size: 1.25rem; margin-inline-end: 8px;"></span>
        Log In
      </v-btn>
    </div>
  </div>
</template>

<script setup>
import backgroundImage from '@images/BB/BG.jpg'
import logo from '@images/BB/Logo2-01.png'
import liff from '@line/liff'
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

// ตัวแปรเก็บข้อมูลจากฟอร์ม
const name = ref('')
const phoneNumber = ref('')
const email = ref('')
const agreeTnc = ref(false)
const agreePrivacy = ref(false)

// ตัวแปรเก็บข้อมูลจาก LIFF
const lineUdid = ref('')
const lineName = ref('')
const linePic = ref('')

// เรียกใช้งาน LIFF เมื่อ component mount
onMounted(async () => {
  try {
    // ใส่ LIFF ID ของคุณ
    await liff.init({ liffId: '2006911756-4J15o7wx' })

    // ถ้าไม่ได้ login ให้ login ก่อน
    if (!liff.isLoggedIn()) {
      liff.login()
    } else {
      // ดึงข้อมูลโปรไฟล์จาก LIFF
      const profile = await liff.getProfile()
      lineUdid.value = profile.userId
      lineName.value = profile.displayName
      linePic.value = profile.pictureUrl
    }
  } catch (error) {
    console.error('LIFF init error:', error)
  }
})

// เงื่อนไขให้ปุ่ม Log In กดได้เฉพาะเมื่อกรอกครบ + ติ๊ก checkbox
const canSubmit = computed(() => {
  return (
    name.value.trim() !== '' &&
    phoneNumber.value.trim() !== '' &&
    email.value.trim() !== '' &&
    agreeTnc.value &&
    agreePrivacy.value
  )
})

const router = useRouter()

// ฟังก์ชัน submit (เมื่อกดปุ่ม Log In)
async function submitForm() {
  if (!canSubmit.value) return

  try {
    // ส่งข้อมูลไปที่ Laravel (API) หรือ URL ที่กำหนด
    const response = await axios.post('/api/quiz-users/store-or-update', {
      line_udid: lineUdid.value,
      line_name: lineName.value,
      line_pic: linePic.value,
      name: name.value,
      phone: phoneNumber.value,
      email: email.value
    })

    console.log('Response:', response.data)
    // หากบันทึกสำเร็จ ให้ไปหน้า OTP หรือทำอย่างอื่นได้ตามต้องการ
    router.push('/otp')
  } catch (error) {
    console.error(error)
    alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล')
  }
}

// หากโปรเจกต์รองรับ definePage
definePage({
  meta: {
    layout: 'blank',
    public: true,
    head: {
      title: 'Sign In',
      meta: [
        {
          name: 'viewport',
          content: 'width=device-width, initial-scale=1.0, maximum-scale=1.0',
        },
      ],
    },
  },
})
</script>

<style scoped>
/* ------------------------------------------
   ป้องกันการซูมเข้าบนอุปกรณ์ iOS
   โดยให้ input font-size >= 16px
------------------------------------------ */
.custom-field ::v-deep input {
  font-size: 16px !important;
}

/* ------------------------------------------
   ปรับโครงสร้างและสไตล์ส่วนต่าง ๆ
------------------------------------------ */
.sign-in-page {
  display: flex;
  box-sizing: border-box;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  inline-size: 100%;
  min-block-size: 100vh;
  padding-block: 40px;
  padding-inline: 16px;
}

.header-section {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  inline-size: 100%;
  margin-block-end: 60px;
  max-inline-size: 400px;
}

.logo-wrapper {
  margin-block-end: 32px;
}

.logo {
  block-size: auto !important;
  inline-size: 150px !important;
}

.title {
  margin: 0;
  color: #1f1f1f;
  font-size: 36px;
  font-weight: 700;
  text-transform: uppercase;
}

.top-form-section {
  display: flex;
  flex-direction: column;
  padding: 24px;
  border-radius: 8px;
  background-color: rgba(255, 255, 255, 28.8%);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 10%);
  inline-size: 100%;
  margin-block-end: 20px;
  max-inline-size: 400px;
}

.bottom-form-section2 {
  display: flex;
  flex-direction: column;
  padding: 24px;
  border-radius: 8px;
  inline-size: 100%;
  margin-block-end: 16px;
  max-inline-size: 400px;
}

.field-label {
  color: #444;
  font-size: 16px;
  font-weight: 500;
  margin-block: 0 4px;
}

.custom-field {
  font-size: 18px;
  margin-block-end: 16px;
}

.custom-field ::v-deep .v-text-field__control {
  min-block-size: 54px; /* ความสูงของช่อง */
}

.custom-field ::v-deep .v-field__outline {
  border: 1px solid #daa520 !important;
}

.custom-field ::v-deep .v-text-field--focused .v-field__outline {
  border-color: #ffd700 !important;
}

.checkbox-container {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-block-end: 16px;
}

.login-btn {
  border-radius: 8px !important;
  background-color: #2c2f36 !important;
  color: #fff !important;
  font-size: 18px !important;
  font-weight: 600 !important;
  padding-block: 10px !important;
}
</style>
