<template>
  <div class="home-container" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <!-- Header -->
    <div class="header">
      <img :src="logo" alt="BeBetter Logo" class="logo" />
    </div>

    <!-- Wrapper ที่ครอบทั้งการ์ด + Avatar -->
    <section class="profile-wrapper">
      <!-- (1) การ์ดโปรไฟล์ -->
      <div class="profile-card">
        <button class="circle-btn white-circle left-btn">
          <i class="bx bx-arrow-back"></i>
        </button>
        <span class="profile-title">Profile</span>

        <button class="circle-btn white-circle right-btn" @click="toggleMenu">
          <i class="bx bx-dots-horizontal-rounded"></i>
        </button>
        <div v-if="showMenu" class="dot-menu">
          <button @click="goToPay">Pay</button>
        </div>
      </div>

      <!-- (2) Avatar วางนอกรอบการ์ด -->
      <div class="profile-avatar white-circle-avatar">
        <!-- (A) ถ้ามีภาพที่ดึงจากฐานข้อมูล -->
        <template v-if="userData?.avatar">
          <img
            :src="userData.avatar"
            alt="Profile"
            style="border-radius: 50%; block-size: 80px; inline-size: 80px; object-fit: cover;"
          />
        </template>

        <!-- (B) ถ้ามีรูป LINE แต่ไม่มีในฐานข้อมูล -->
        <template v-else-if="linePic">
          <img
            :src="linePic"
            alt="Profile"
            style="border-radius: 50%; block-size: 80px; inline-size: 80px; object-fit: cover;"
          />
        </template>

        <!-- (C) ถ้าไม่มีรูปอะไรเลย ก็เป็นไอคอน user -->
        <template v-else>
          <i class="bx bxs-user" style="color: var(--color-navy); font-size: 32px;"></i>
        </template>
      </div>
    </section>

    <!-- Welcome Section + point/coin -->
    <section class="welcome-section">
      <h2 class="welcome-text">Welcome,</h2>
      <h2 class="welcome-name">
        {{ userData.name || lineName || '...' }}
      </h2>

      <!-- ปุ่ม point -->
      <button class="point-button" @click="goToPay">
        <i class='bx bx-gift'></i>
        Your point
      </button>

      <!-- แสดง coin -->
      <p v-if="userData.coin !== undefined" style=" color: #555; margin-block-start: 8px;">
        Current Coin: <strong>{{ userData.coin }}</strong>
      </p>
    </section>

    <!-- Menu -->
    <section class="menu-section">
      <!-- ปุ่ม Your Type -->
      <button class="menu-box" @click="goToResult">
        Your Type
        <br />
        <small>For your style</small>
      </button>

      <!-- ปุ่ม Tips => ไปหน้า /color -->
      <button class="menu-box" @click="goToColor">
        Tips
        <br />
        <small>For your style</small>
      </button>

      <!-- ปุ่ม Shop => ไปหน้า /shop -->
      <button class="menu-box" @click="goToShop">
        Shop
        <br />
        <small>For your style</small>
      </button>
    </section>

    <!-- Quiz -->
    <section class="quiz-section">
      <h2 class="quiz-header">Quizzes</h2>
      <div class="quiz-card">
        <h3 class="quiz-title">What's your Personality?</h3>
        <!-- ปุ่มเริ่มทำ Quiz -->
        <button class="start-quiz-btn" @click="onStartQuiz">Start Quiz</button>
      </div>
    </section>

    <!-- (1) Modal ถ้า coin ไม่พอ -->
    <div class="modal-overlay" v-if="showCoinModal">
      <div class="modal-content">
        <h3>Coin not enough!</h3>
        <p>You need {{ quizCost }} coins to take this quiz. Your coin: {{ userData.coin }}</p>
        <p>Would you like to top up your coin now?</p>
        <div class="modal-actions">
          <button @click="goToPackage" class="confirm-btn">Yes, Top up</button>
          <button @click="closeModal" class="cancel-btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import backgroundImage from '@images/BB/BG.jpg'
import logo from '@images/BB/Logo2-01.png'
import liff from '@line/liff'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

const showMenu = ref(false)
function toggleMenu() {
  showMenu.value = !showMenu.value
}

const router = useRouter()

function goToPay() {
  router.push({ name: 'pay' })
}

// ไปหน้า /result
function goToResult() {
  router.push('/result')
}

// ไปหน้า /color (Tips)
function goToColor() {
  router.push('/color')
}

// ไปหน้า /shop
function goToShop() {
  router.push('/shop')
}

// -------------------------------------------------
// State หลัก
// -------------------------------------------------
const userData = ref({
  name: '',
  avatar: '',
  coin: 0,
})
const lineUdid = ref('')
const linePic = ref('')
const lineName = ref('')

// Mock ราคา Quiz
const quizCost = 25

// สำหรับเปิด/ปิด modal coin ไม่พอ
const showCoinModal = ref(false)

onMounted(async () => {
  try {
    // Init LIFF
    await liff.init({ liffId: '2006911756-4J15o7wx' })
    if (!liff.isLoggedIn()) {
      liff.login()
      return
    }

    const profile = await liff.getProfile()
    lineUdid.value = profile.userId
    linePic.value = profile.pictureUrl
    lineName.value = profile.displayName

    // ดึงข้อมูล user จาก DB
    const { data } = await axios.get('/api/users/profile', {
      params: { line_udid: lineUdid.value },
    })

    if (data?.success && data?.user) {
      userData.value = {
        name: data.user.name || lineName.value,
        avatar: data.user.line_pic || linePic.value,
        coin: data.user.coin ?? 0,
      }
    } else {
      // ถ้าไม่พบ user ใน DB => ใช้ข้อมูล LINE
      userData.value = {
        name: lineName.value,
        avatar: linePic.value,
        coin: 0,
      }
    }
  } catch (error) {
    console.error(error)
  }
})

// -------------------------------------------------
// ฟังก์ชันเมื่อคลิก Start Quiz
// -------------------------------------------------
async function onStartQuiz() {
  if (userData.value.coin < quizCost) {
    // ถ้าเหรียญไม่พอ => เปิด modal
    showCoinModal.value = true
  } else {
    // coin พอ => หัก coin ในระบบก่อน
    try {
      const resp = await axios.post('/api/quiz-users/deduct-coin', {
        line_udid: lineUdid.value,
        quiz_cost: quizCost,
      })

      if (resp.data.success) {
        // อัปเดต coin ที่เหลือ
        userData.value.coin = resp.data.coin_remaining
        // ไปหน้า quiz
        router.push({ name: 'quiz' })
      } else {
        alert(resp.data.message || 'Cannot start quiz')
      }
    } catch (err) {
      console.error(err)
      alert('Failed to deduct coin. Please try again.')
    }
  }
}

function closeModal() {
  showCoinModal.value = false
}

function goToPackage() {
  router.push({ name: 'package' })
  showCoinModal.value = false
}
</script>

<style>
:root {
  --color-navy: #23303d;
  --color-gold: #dbab61;
  --color-white: #fff;
  --color-gray: #dedede;
  --color-text-black: #232323;
}
</style>

<style scoped>
.home-container {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;
  inline-size: 100%;
  min-block-size: 100vh;
}

/* Header */
.header {
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  inset-block-start: 16px;
  inset-inline-start: 16px;
}

.logo {
  block-size: auto;
  inline-size: 150px;
}

/* PROFILE WRAPPER */
.profile-wrapper {
  position: relative;
  inline-size: 100%;
  margin-block: 50px 36px;
  max-inline-size: 400px;
}

/* CARD */
.profile-card {
  position: relative;
  overflow: hidden; /* ซ่อนส่วนที่ล้น (วงกลมทอง) */
  border-radius: 12px;
  background-color: var(--color-navy);
  block-size: 180px;
  color: var(--color-white);
  inline-size: 100%;
}

.profile-card::before,
.profile-card::after {
  position: absolute;
  z-index: 1;
  border-radius: 50%;
  background-color: var(--color-gold);
  content: "";
}

.profile-card::before {
  block-size: 140px;
  inline-size: 140px;
  inset-block-start: -10px;
  inset-inline-start: -20px;
}

.profile-card::after {
  block-size: 100px;
  inline-size: 100px;
  inset-block-end: -10px;
  inset-inline-end: -20px;
}

/* ปุ่มบนการ์ด */
.circle-btn {
  position: absolute;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 50%;
  background-color: var(--color-white);
  block-size: 36px;
  cursor: pointer;
  inline-size: 36px;
  inset-block-start: 12px;
}

.circle-btn i {
  color: var(--color-navy);
  font-size: 18px;
}

.left-btn {
  inset-inline-start: 12px;
}

.right-btn {
  inset-inline-end: 12px;
}

.profile-title {
  position: absolute;
  z-index: 2;
  font-size: 1.1rem;
  font-weight: 600;
  inset-block-start: 16px;
  inset-inline-start: 50%;
  transform: translateX(-50%);
}

.dot-menu {
  position: absolute;
  z-index: 5;
  padding: 8px;
  border-radius: 8px;
  background-color: var(--color-white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 20%);
  inset-block-start: 50px;
  inset-inline-end: 12px;
}

.dot-menu button {
  border: none;
  background: none;
  color: var(--color-navy);
  cursor: pointer;
  font-size: 0.9rem;
  inline-size: 100%;
  padding-block: 4px;
  padding-inline: 8px;
  text-align: start;
}

/* AVATAR */
.profile-avatar {
  position: absolute;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: var(--color-white);
  block-size: 120px;
  inline-size: 120px;
  inset-block-end: -24px;
  inset-inline-start: 50%;
  transform: translateX(-50%);
}

.white-circle-avatar i {
  color: var(--color-navy);
  font-size: 32px;
}

/* WELCOME SECTION */
.welcome-section {
  margin-block-end: 24px;
  text-align: center;
}

.welcome-text {
  margin: 0;
  color: var(--color-navy);
  font-size: 1rem;
}

.welcome-name {
  color: var(--color-gold);
  font-size: 1.6rem;
  font-weight: bold;
  margin-block: 4px 8px;
}

.point-button {
  display: inline-flex;
  align-items: center;
  border: none;
  border-radius: 20px;
  background-color: var(--color-gray);
  color: var(--color-text-black);
  cursor: pointer;
  gap: 4px;
  padding-block: 6px;
  padding-inline: 12px;
}

/* MENU SECTION */
.menu-section {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 16px;
  margin-block-end: 28px;
}

.menu-box {
  padding: 12px;
  border: 2px solid var(--color-gold);
  border-radius: 8px;
  background-color: var(--color-navy);
  color: var(--color-white);
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  inline-size: 100px;
  min-block-size: 60px;
  text-align: center;
}

.menu-box small {
  font-size: 0.75rem;
}

/* QUIZ SECTION */
.quiz-section {
  inline-size: 100%;
  margin-block-end: 60px;
  max-inline-size: 500px;
  text-align: center;
}

.quiz-header {
  color: var(--color-navy);
  font-size: 1.3rem;
  margin-block-end: 25px;
}

.quiz-card {
  position: relative;
  overflow: hidden;
  padding: 36px;
  border-radius: 12px;
  background-color: var(--color-navy);
  color: var(--color-white);
}

.quiz-card::before,
.quiz-card::after {
  position: absolute;
  z-index: 1;
  border-radius: 50%;
  background-color: var(--color-gold);
  content: "";
}

.quiz-card::before {
  block-size: 120px;
  inline-size: 120px;
  inset-block-start: -30px;
  inset-inline-start: -40px;
}

.quiz-card::after {
  block-size: 100px;
  inline-size: 100px;
  inset-block-end: -30px;
  inset-inline-end: -30px;
}

.quiz-title {
  position: relative;
  z-index: 2;
  color: var(--color-white);
  font-size: 1.1rem;
  font-weight: 700;
  margin-block: 0 12px;
}

.start-quiz-btn {
  position: relative;
  z-index: 2;
  border: none;
  border-radius: 4px;
  background-color: var(--color-gold);
  color: var(--color-white);
  cursor: pointer;
  font-size: 0.9rem;
  padding-block: 8px;
  padding-inline: 14px;
}

/* modal */
.modal-overlay {
  position: fixed;
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 50%);
  inset: 0;
}

.modal-content {
  padding: 24px;
  border-radius: 12px;
  background-color: #fff;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 30%);
  min-inline-size: 280px;
  text-align: center;
}

.modal-actions {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-block-start: 16px;
}

.confirm-btn {
  border: none;
  border-radius: 4px;
  background-color: #06c;
  color: #fff;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 14px;
}

.cancel-btn {
  border: none;
  border-radius: 4px;
  background-color: #999;
  color: #fff;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 14px;
}
</style>
