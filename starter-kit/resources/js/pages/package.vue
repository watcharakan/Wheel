<template>
  <div
    class="topup-page"
    :style="{
      backgroundImage: `url(${backgroundImage})`,
      backgroundPosition: 'center',
      backgroundSize: 'cover',
      backgroundRepeat: 'no-repeat',
    }"
  >
    <!-- Header (Logo + Title) -->
    <div class="header">
      <img :src="logo" alt="BeBetter Logo" class="logo" />
      <h1 class="title">Coin Top-Up</h1>
    </div>

    <!-- Content (center area) -->
    <div class="content">
      <!-- Transparent card -->
      <div class="card-container">
        <h2 class="package-header">Select a Package</h2>
        <!-- Package list -->
        <div class="package-list">
          <div
            class="package-item"
            v-for="(pkg, index) in packages"
            :key="pkg.id"
            :class="{ selected: selectedPackage === pkg }"
            @click="selectPackage(pkg)"
          >
            <h3>{{ pkg.package_name }}</h3>
            <p>Get {{ pkg.coins }} coins</p>
            <p class="price">{{ pkg.price }} THB</p>
          </div>
        </div>
        <!-- Buttons -->
        <div class="button-group">
          <!-- ปุ่ม Cancel/Back ไปหน้า /home -->
          <v-btn class="outline-button" @click="handleCancel">Cancel</v-btn>
          <!-- ปุ่ม Pay Now ไปหน้า /pay -->
          <v-btn class="primary-button" @click="handlePay">Pay Now</v-btn>
          <!-- ปุ่ม Use Coupon -->
          <v-btn class="outline-button" @click="openCouponDialog">Use Coupon</v-btn>
        </div>
      </div>
    </div>

    <!-- POPUP (Dialog) สำหรับกรอกคูปอง/แสดงผล -->
    <v-dialog v-model="showCouponDialog" max-width="400">
      <v-card>
        <!-- แสดงหัวข้อแตกต่างกันตามสถานะ -->
        <v-card-title v-if="!couponSuccess">Enter Coupon</v-card-title>
        <v-card-title v-else>Congratulations!</v-card-title>

        <v-card-text>
          <!-- ถ้ายังไม่กด confirm ให้กรอกโค้ด -->
          <template v-if="!couponSuccess">
            <v-text-field
              v-model="couponCode"
              label="Coupon Code"
              placeholder="Enter your coupon..."
            />
          </template>
          <!-- ถ้ากด confirm แล้ว แสดงผลลัพธ์ -->
          <template v-else>
            You got {{ couponCoin }} coins!
          </template>
        </v-card-text>

        <v-card-actions>
          <!-- ถ้ายังไม่ confirm แสดงปุ่ม confirm -->
          <v-btn
            v-if="!couponSuccess"
            class="primary-button"
            @click="handleCouponConfirm"
          >
            Confirm
          </v-btn>
          <!-- ถ้า confirm แล้ว แสดงปุ่ม OK -->
          <v-btn
            v-else
            class="primary-button"
            @click="goHome"
          >
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import backgroundImage from '@images/BB/BG.jpg';
import logo from '@images/BB/Logo2-01.png';
import liff from '@line/liff'; // ไลบรารี LINE LIFF
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

// State for packages (ดึงจาก API)
const packages = ref([])

// State for selected package
const selectedPackage = ref(null)

// เก็บ lineUdid ที่ได้จาก liff
const lineUdid = ref('')

// Router instance
const router = useRouter()

// ควบคุม Dialog และ Coupon
const showCouponDialog = ref(false)   // ควบคุมการแสดง/ซ่อน Dialog
const couponCode = ref('')            // เก็บค่าคูปองที่กรอก
const couponSuccess = ref(false)      // ระบุว่ากดยืนยันแล้วหรือยัง
const couponCoin = ref(0)            // จำนวนเหรียญที่ได้จากคูปอง

/**
 * ดึงข้อมูลแพ็กเกจจาก API
 */
async function fetchPackages() {
  try {
    // ตัวอย่าง สมมติมี API /api/packages
    const response = await axios.get('/api/packages')
    packages.value = response.data
  } catch (error) {
    console.error('Error fetching packages:', error)
  }
}

// เรียกใช้ initLineLiff() และ fetchPackages() เมื่อ component mount
onMounted(() => {
  fetchPackages()
  initLineLiff()
})

// ฟังก์ชันสำหรับเริ่มต้น liff
async function initLineLiff() {
  try {
    // ใส่ LIFF ID ของคุณ
    await liff.init({ liffId: '2006911756-4J15o7wx' })
    
    // ถ้ายังไม่ได้ login ให้บังคับ login
    if (!liff.isLoggedIn()) {
      liff.login()
    } else {
      // ถ้า login แล้ว ให้ getProfile เพื่อดึง userId
      const profile = await liff.getProfile()
      lineUdid.value = profile.userId
      console.log('LINE User ID:', lineUdid.value)
    }
  } catch (error) {
    console.error('LIFF init error:', error)
  }
}

function selectPackage(pkg) {
  selectedPackage.value = pkg
  console.log('Selected package:', pkg)
}

/* ======== Flow กด Pay Now ======== */
function handlePay() {
  if (!selectedPackage.value) {
    alert('Please select a package first.')
    return
  }
  // ไปหน้า /pay พร้อมส่ง package_id
  router.push({
    name: 'pay',
    query: {
      package_id: selectedPackage.value.id
    }
  })
}

/* ======== Flow ปุ่ม Cancel ======== */
function handleCancel() {
  // ย้อนกลับไปหน้า /home
  router.push('/home')
}

/* ======== Flow ปุ่ม Use Coupon ======== */
function openCouponDialog() {
  showCouponDialog.value = true
  couponSuccess.value = false
  couponCode.value = ''
}

// เมื่อกดยืนยันคูปอง
async function handleCouponConfirm() {
  if (!couponCode.value) {
    alert('Please enter a coupon code.')
    return
  }

  try {
    // เรียก API ไปเช็คคูปองที่ /api/coupons/apply-coupon
    const response = await axios.post('/api/coupons/apply-coupon', {
      line_udid: lineUdid.value,
      coupon_code: couponCode.value,
    })

    // หาก success -> ได้เหรียญเท่าไร ก็อัพเดตใน couponCoin
    if (response.data.success) {
      couponCoin.value = response.data.added_coin ?? 0
      couponSuccess.value = true
      console.log(`Coupon "${couponCode.value}" is valid. You got ${couponCoin.value} coins!`)
    } else {
      // ถ้า success = false, แสดงข้อความผิดพลาดจาก message
      alert(response.data.message)
    }
  } catch (error) {
    console.error('Coupon error:', error)
    alert('Failed to apply coupon')
  }
}

// เมื่อแสดงผลว่าได้เหรียญแล้ว กด OK -> กลับหน้า Home
function goHome() {
  showCouponDialog.value = false
  router.push('/home')
}
</script>

<style scoped>
/* ======================================
   Main Container (Set background via :style)
   ====================================== */
.topup-page {
  min-block-size: 100vh;
  padding-inline: 24px !important;
}

/* ======================================
   Header (Logo + Title)
   ====================================== */
.header {
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-start !important;
  gap: 8px !important;
  margin-block: 12px !important;
}

.logo {
  block-size: auto !important;
  inline-size: 150px !important;
}

.title {
  margin: 0 !important;
  color: #2c2f36 !important;
  font-size: 36px !important;
  font-weight: 700 !important;
}

/* ======================================
   Content (center align)
   ====================================== */
.content {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  min-block-size: 100vh !important;
}

/* ======================================
   Card Container (transparent white)
   ====================================== */
.card-container {
  border-radius: 12px !important;
  backdrop-filter: blur(4px) !important;
  background-color: rgba(255, 255, 255, 22%) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%) !important;
  inline-size: 100% !important;
  max-inline-size: 350px !important;
  padding-block: 32px 24px !important;
  padding-inline: 24px !important;
  text-align: center !important;
}

.package-header {
  color: #555 !important;
  margin-block-end: 24px !important;
}

/* ======================================
   Package list
   ====================================== */
.package-list {
  display: flex !important;
  flex-direction: column !important;
  gap: 16px !important;
  margin-block-end: 24px !important;
}

.package-item {
  padding: 12px !important;
  border: 2px solid transparent !important;
  border-radius: 8px !important;
  background-color: rgba(255, 255, 255, 40%) !important;
  cursor: pointer !important;
  transition: border-color 0.3s ease !important;
}

.package-item:hover {
  border-color: #d4af37 !important; /* gold border on hover */
}

.package-item.selected {
  border-color: #d4af37 !important; /* gold border if selected */
}

.package-item h3 {
  margin: 0 !important;
  color: #2c2f36 !important;
  font-size: 1.2rem !important;
}

.package-item p {
  color: #555 !important;
  margin-block: 4px !important;
  margin-inline: 0 !important;
}

.package-item .price {
  color: #2c2f36 !important;
  font-weight: 600 !important;
}

/* ======================================
   Buttons
   ====================================== */
.button-group {
  display: flex !important;
  flex-wrap: wrap !important;
  justify-content: center !important;
  gap: 16px !important;
}

/* Primary button (dark background) */
.primary-button.v-btn {
  background-color: #2c2f36 !important;
  color: #fff !important;
  font-weight: 600 !important;
  text-transform: none !important;
}

/* Outline button (gold border, transparent background) */
.outline-button.v-btn {
  border: 2px solid #d4af37 !important;
  background-color: #ffffff15 !important;
  color: #2c2f36 !important;
  font-weight: 500 !important;
  text-transform: none !important;
}
</style>
