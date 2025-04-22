<template>
  <div class="welcome-page" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <!-- ส่วนบน: โลโก้ + ข้อความ WELCOME บนคนละบรรทัด -->
    <div class="top-section">
      <!-- โลโก้อยู่มุมซ้าย -->
      <div class="logo-wrapper">
        <img :src="logo" alt="BeBetter Logo" class="logo" />
      </div>
      <!-- ข้อความ WELCOME แยกบรรทัดลงมา -->
      <h1 class="main-title">WELCOME <br> TO BE BETTER</h1>
    </div>

    <!-- ส่วนกลาง: ข้อความ + ปุ่ม -->
    <div class="text-wrapper">
      <!-- ข้อความหลัก -->
      <p class="subtitle">Design your style<br />from your authentic self</p>

      <!-- ปุ่ม กำหนดคลาส .get เพื่อบังคับสี -->
      <v-btn size="large" class="get" @click="goToPdpaPage">
        Get started
      </v-btn>
    </div>
  </div>
</template>

<script setup>
import liff from '@line/liff'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

// 2) import assets
import backgroundImage from '@images/BB/BG.jpg'
import logo from '@images/BB/Logo1-01.png'

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

const router = useRouter()

const userId = ref('')

// ฟังก์ชันเปลี่ยนหน้า
function goToPdpaPage() {
  router.push('/pdpa')
}

// เรียกใช้งานภายใน onMounted เพื่อ init LIFF และดึง userId
onMounted(async () => {
  try {
    await liff.init({ liffId: '2006911756-4J15o7wx' })

    if (!liff.isLoggedIn()) {
      liff.login()
    } else {
      const profile = await liff.getProfile()
      userId.value = profile.userId

      console.log('LINE UserID:', userId.value)

      // ======== เรียก API ฝั่ง Laravel เพื่อเช็คข้อมูลใน DB ========
      try {
        const { data } = await axios.get(
          `/api/users/profile?line_udid=${userId.value}`
        )
        if (data?.success) {
          const user = data.user
          // สมมติเงื่อนไขคือ: name != null, email != null, และ otp_confirmed == true
          if (user.name && user.email ) {
            // ถ้าครบเงื่อนไข ให้ข้ามไปที่หน้า /home ทันที
            router.push('/home')
          } else {
            // ยังไม่ครบเงื่อนไข เช่น ยังไม่เคยใส่ชื่อ/อีเมล
            // หรือยังไม่ได้ยืนยัน OTP -> ให้ทำตาม flow ปกติ (กดปุ่มไป pdpa)
            console.log('User not fully registered or no OTP. Waiting PDPA flow...')
          }
        }
      } catch (err) {
        // ถ้า 404 หรือมี error อื่นแสดงว่ายังไม่มีข้อมูล user นี้ใน DB
        // ก็ให้ flow เดิมคือ กดปุ่มไปทำ PDPA ต่อได้
        console.warn('No user data found or error occurred:', err)
      }
      // ============================================================
    }
  } catch (error) {
    console.error('เกิดข้อผิดพลาดในการเชื่อมต่อ LINE LIFF:', error)
  }
})
</script>

<style scoped>
/* ================================
   พื้นหลัง + การจัดวางหลัก
   ================================ */
.welcome-page {
  display: flex;
  box-sizing: border-box;
  flex-direction: column;
  align-items: stretch;
  justify-content: flex-start;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  inline-size: 100%;
  min-block-size: 100vh;
  padding-block: 40px;
  padding-inline: 16px;
}

/* ================================
   ส่วนบน: โลโก้ + Title
   ================================ */
.top-section {
  display: flex;
  flex-direction: column;
  margin-block-end: 60px;
}

.logo-wrapper {
  display: flex;
  justify-content: flex-start;
  inline-size: 100%;
  margin-block-end: 8px;
}

.logo {
  block-size: 70px;
  inline-size: 70px;
}

.main-title {
  margin: 0;
  color: #1f1f1f;
  font-size: 48px;
  font-weight: 700;
  text-transform: uppercase;
}

/* ================================
   ส่วนกลาง: subtitle + button
   ================================ */
.text-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-block-start: 150px;
  text-align: center;
}

.subtitle {
  color: #555;
  font-size: 20px;
  line-height: 1.4;
  margin-block: 0 24px;
  margin-inline: 0;
}

/* ================================
   ปุ่ม (ใช้ .get)
   ================================ */
.get.v-btn {
  border-radius: 8px !important;
  background-color: #2c2f36 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%) !important;
  color: #fff !important;
  font-size: 22px !important;
  font-weight: 600 !important;
  padding-block: 10px !important;
  padding-inline: 32px !important;
}
</style>
