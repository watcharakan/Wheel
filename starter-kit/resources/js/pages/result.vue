<template>
  <v-app>
    <!-- Top Bar มีปุ่มย้อนกลับ -->
    <div class="top-bar">
      <v-btn icon class="back-button" @click="goHome">
        <!-- ไอคอน bx (ต้องแน่ใจว่าได้ติดตั้งไอคอน bx หรือใช้ icon ที่รองรับ) -->
        <v-icon>bx-arrow-back</v-icon>
      </v-btn>
      <h2 class="title-text">ผลลัพธ์ Quiz</h2>
    </div>

    <!-- ถ้า isImageLoading ยัง true => แสดง Loader เต็มหน้าจอ -->
    <div v-if="isImageLoading" class="loader-overlay">
      <v-progress-circular
        size="64"
        color="deep-purple accent-4"
        indeterminate
      />
      <p class="mt-2">กำลังโหลดรูป...</p>
    </div>

    <!-- ถ้ามี error => แจ้ง error -->
    <p v-else-if="imageError" class="error-text">
      ไม่สามารถโหลดรูปผลลัพธ์ได้
    </p>

    <!-- ถ้าโหลดครบและไม่มี error => แสดงรูป Archetype + Style -->
    <div v-else class="image-container">
      <!-- (1) แสดงรูป Archetype เต็มจอ (ถ้ามีหลายตัว จะวน loop) -->
      <div
        v-for="(imgSrc, idx) in typeImages"
        :key="`type-${idx}`"
        class="image-view"
      >
        <img :src="imgSrc" class="full-screen-image" />
      </div>

      <!-- (2) แสดงรูป Style เต็มจอ (ถ้ามีหลายตัว จะวน loop) -->
      <div
        v-for="(imgSrc, idx) in styleImages"
        :key="`style-${idx}`"
        class="image-view"
      >
        <img :src="imgSrc" class="full-screen-image" />
      </div>
    </div>

    <!-- ส่วน preload ซ่อนจับ event load/error -->
    <div class="preload-container">
      <img
        v-for="(imgSrc, i) in allResultImages"
        :key="`preload-${i}`"
        :src="imgSrc"
        class="preload-img"
        @load="onAnyImageLoad"
        @error="onAnyImageError"
      />
    </div>
  </v-app>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import {
  VApp,
  VBtn,
  VIcon,
  VProgressCircular
} from 'vuetify/components'

// ใช้งาน router
import { useRouter } from 'vue-router'
const router = useRouter()

// import liff
import liff from '@line/liff'

// ใช้ definePage (ถ้าใช้ Nuxt / หรือระบบที่มี wrapper meta)
definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

// ----------- ตัวอย่างรูป (Archetype + Style) -----------
import caregiverJpg from '@images/Result/caregiver.jpg'
import creatorJpg from '@images/Result/creator.jpg'
import entertainerJpg from '@images/Result/entertainer.jpg'
import everymanJpg from '@images/Result/everyman.jpg'
import explorerJpg from '@images/Result/explorer.jpg'
import heroJpg from '@images/Result/hero.jpg'
import innocentJpg from '@images/Result/innocent.jpg'
import loverJpg from '@images/Result/lover.jpg'
import magicianJpg from '@images/Result/magician.jpg'
import rebelJpg from '@images/Result/rebel.jpg'
import rulerJpg from '@images/Result/ruler.jpg'
import sageJpg from '@images/Result/sage.jpg'

import alluringJpg from '@images/Style/alluring.jpg'
import ClassicJpg from '@images/Style/Classic.jpg'
import CreativeJpg from '@images/Style/Creative.jpg'
import DramaticJpg from '@images/Style/Dramatic.jpg'
import elegantJpg from '@images/Style/elegant.jpg'
import FeminineJpg from '@images/Style/Feminine.jpg'
import NaturalJpg from '@images/Style/Natural.jpg'

// ---------- Map ภาพ ----------
const archetypeImageMap = {
  The_lover: loverJpg,
  The_caregiver: caregiverJpg,
  The_entertainer: entertainerJpg,
  The_Everyman: everymanJpg,
  The_explorer: explorerJpg,
  The_hero: heroJpg,
  The_innocent: innocentJpg,
  The_magician: magicianJpg,
  The_rebel: rebelJpg,
  The_ruler: rulerJpg,
  The_sage: sageJpg,
  The_creator: creatorJpg,
}

const styleImageMap = {
  alluring: alluringJpg,
  Classic: ClassicJpg,
  Creative: CreativeJpg,
  Dramatic: DramaticJpg,
  elegant: elegantJpg,
  Feminine: FeminineJpg,
  Natural: NaturalJpg,
}

// ---------- ตัวแปรเก็บผลลัพธ์ประเภท (Archetype/Type) และ (Style) ----------
const types = ref([])   // เดิม: ตัวอย่าง ['The_Everyman', 'The_lover']
const styles = ref([])  // เดิม: ตัวอย่าง ['Dramatic']

// ---------- สถานะการโหลดรูป ----------
const allResultImages = computed(() => {
  const typeImgs = types.value.map(t => archetypeImageMap[t]).filter(Boolean)
  const styleImgs = styles.value.map(s => styleImageMap[s]).filter(Boolean)
  return [...typeImgs, ...styleImgs]
})
const totalImagesCount = computed(() => allResultImages.value.length)
const loadedCount = ref(0)
const isImageLoading = ref(true)
const imageError = ref(false)

function onAnyImageLoad() {
  loadedCount.value++
  checkIfAllLoaded()
}
function onAnyImageError() {
  imageError.value = true
  loadedCount.value++
  checkIfAllLoaded()
}
function checkIfAllLoaded() {
  if (loadedCount.value >= totalImagesCount.value) {
    // โหลดครบ (จะมี error หรือไม่ก็จะจบ process)
    isImageLoading.value = false
  }
}

// แยก Arrays รูปสำหรับ Archetype / Style
const typeImages = computed(() =>
  types.value.map(t => archetypeImageMap[t]).filter(Boolean)
)
const styleImages = computed(() =>
  styles.value.map(s => styleImageMap[s]).filter(Boolean)
)

// ---------- ปุ่มย้อนกลับ ----------
function goHome() {
  // ตัวอย่างเช่น สั่ง router.push('/home') หรือ window.location.replace('/')
  router.push('/home')
}

// ---------- Lifecycle: onMounted เพื่อ init LIFF + เรียก API ดึงข้อมูล ----------
onMounted(async () => {
  try {
    // 1) Initialize LIFF
    await liff.init({ liffId: '2006911756-4J15o7wx' })

    // 2) ถ้าไม่ได้ login ให้บังคับ login
    if (!liff.isLoggedIn()) {
      liff.login()
      return
    }

    // 3) ดึง Profile จาก LIFF
    const profile = await liff.getProfile()
    const userLineId = profile.userId  // line_udid

    // 4) เรียก API ฝั่ง Laravel เพื่อนำข้อมูล styles / types จาก DB
    const res = await fetch(`/api/users/profile?line_udid=${userLineId}`)
    const data = await res.json()

    if (data.success) {
      // ตัวอย่าง structure = { success: true, user: { styles: [...], types: [...] } }

      // **(ปรับแก้ฝั่งฟรอนต์)** แปลงช่องว่างให้เป็นอันเดอร์สกอร์เพื่อให้ match คีย์ใน archetypeImageMap
      if (Array.isArray(data.user.types)) {
        types.value = data.user.types.map(t => t.replace(/\s+/g, '_'))
      }

      if (Array.isArray(data.user.styles)) {
        styles.value = data.user.styles
      }
    } else {
      console.warn('User not found or API error', data)
    }

  } catch (err) {
    console.error('Error in onMounted:', err)
  }
})
</script>

<style scoped>
@import "https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&display=swap";

html,
body,
.v-application {
  padding: 0;
  margin: 0;
  font-family: Prompt, sans-serif;
}

/* Top Bar */
.top-bar {
  position: fixed;
  z-index: 10001;
  display: flex;
  align-items: center;
  background-color: #0009;
  inline-size: 100%;
  inset-block-start: 0;
  padding-block: 0.5rem;
  padding-inline: 1rem;
}

.back-button {
  color: #fff !important;
}

.title-text {
  margin: 0;
  color: #fff;
  font-size: 1.2rem;
  font-weight: 600;
  margin-inline-start: 1rem;
}

/* Loader Overlay (Fullscreen) */
.loader-overlay {
  position: fixed;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: #0002; /* พื้นหลังโปร่งเล็กน้อย */
  gap: 1rem;
  inset: 0;
}

/* ถ้ามี error */
.error-text {
  position: fixed;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fff8;
  color: red;
  font-weight: 600;
  inset: 0;
}

/* ภาพเต็มจอ */
.image-container {
  margin-block-start: 56px; /* เว้นให้ไม่ทับ top bar */
}

.image-view {
  position: relative;
  overflow: hidden;
  block-size: 100vh; /* สูงเต็มหน้าจอ (scroll ต่อกัน) */
  inline-size: 100%;
}

/* ปรับ object-fit ให้ภาพดูเต็มหน้าจอ */
.full-screen-image {
  block-size: 100%;
  inline-size: 100%;
  object-fit: cover;
}

/* แคปชั่นที่ซ้อนทับบนภาพ */
.image-caption {
  position: absolute;
  border-radius: 4px;
  background-color: #0005;
  color: #fff;
  font-weight: 600;
  inset-block-end: 1rem;
  inset-inline-start: 1rem;
  padding-block: 0.5rem;
  padding-inline: 1rem;
  text-shadow: 1px 1px 2px #000;
}

/* preload ซ่อน */
.preload-container {
  display: none;
}

.preload-img {
  display: none;
}
</style>
