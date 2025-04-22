<template>
  <v-app>
    <!-- พื้นหลัง Quiz -->
    <v-img
      :src="backgroundImage"
      alt="background"
      cover
      height="100vh"
      aspect-ratio="0"
      class="bg-full no-sizer"
    >
      <!-- Logo มุมบน -->
      <div class="header">
        <img :src="logo" alt="BeBetter Logo" class="logo" />
      </div>

      <!-- Container Quiz (แสดงเฉพาะขณะทำ quiz) -->
      <v-container
        v-if="!showResult"
        class="quiz-container d-flex flex-column justify-center align-center"
      >
        <v-card
          class="px-4 py-4 quiz-card"
          elevation="3"
          max-width="800"
          width="100%"
        >
          <v-card-text>
            <!-- ส่วนหัว + Progress -->
            <div class="title-section d-flex flex-column mb-4">
              <h1 class="quiz-title mb-2">Quizzes</h1>
              <p class="quiz-subtitle mb-2">
                Question {{ quizIndex + 1 }}/{{ quizQuestions.length }}
              </p>
              <v-progress-linear
                :model-value="(quizIndex + 1) / quizQuestions.length * 100"
                height="8"
                color="#fcb800"
                class="progress-bar"
              />
            </div>

            <!-- คำถาม -->
            <h3 class="question-text mt-2">{{ currentQuestion.question }}</h3>

            <!-- ตัวเลือก (mandatory) -->
            <v-radio-group
              v-model="selectedAnswers[quizIndex]"
              class="choice-container"
              mandatory
            >
              <v-radio
                v-for="(choice, cIndex) in currentQuestion.choices"
                :key="cIndex"
                :label="choice"
                :value="cIndex"
                class="mb-2"
              />
            </v-radio-group>

            <!-- ปุ่มกด -->
            <div class="d-flex justify-space-between mt-6">
              <v-btn
                :disabled="quizIndex === 0"
                class="btn-dark"
                @click="prevQuestion"
              >
                ย้อนกลับ
              </v-btn>

              <v-btn
                class="btn-dark"
                :disabled="selectedAnswers[quizIndex] === null"
                @click="nextOrShowResult"
              >
                <template v-if="quizIndex < quizQuestions.length - 1">
                  Next Question
                </template>
                <template v-else>
                  ดูผลลัพธ์
                </template>
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-container>
    </v-img>

    <!-- ผลลัพธ์: แสดงรูปเต็มจอหลายรูปเรียงลงมา -->
    <div v-if="showResult" class="result-overlay">
      <div class="images-stack">
        <!-- วนทุกภาพที่ต้องการโชว์ (archetype + styles) -->
        <div
          v-for="(imgUrl, idx) in displayImages"
          :key="idx"
          class="full-screen-image"
        >
          <img
            :src="imgUrl"
            alt="Full"
            class="img-cover"
          />
          <!-- ถ้าต้องการใส่ข้อความใต้รูป ก็ใส่ได้ เช่น:
               <p class="image-caption">รูปที่ {{ idx + 1 }}</p>
          -->
        </div>
      </div>

      <!-- ปุ่ม สำเร็จ -->
      <div class="finish-btn-container">
        <v-btn class="btn-dark" @click="goHome">
          สำเร็จ
        </v-btn>
      </div>
    </div>
  </v-app>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import {
  VApp,
  VBtn,
  VCard,
  VCardText,
  VContainer,
  VImg,
  VProgressLinear,
  VRadio,
  VRadioGroup,
} from 'vuetify/components'

// ถ้าใช้ LIFF
import liff from '@line/liff'

// Quiz Data (ปรับ path ตามโปรเจกต์)
import { quizQuestions } from '@/views/apps/quizData.js'

// รูปพื้นหลัง + logo
import backgroundImage from '@images/BB/BG.jpg'
import logo from '@images/BB/Logo2-01.png'

// รูป Archetype
import caregiverJpg from '@images/Result/caregiver.jpg'
import creatorJpg from '@images/Result/creator.jpg'
import entertainerJpg from '@images/Result/entertainer.jpg'
import everymanJpg from '@images/Result/everyman.jpg'
import explorerJpg from '@images/Result/explorer.jpg'
import fallbackJpg from '@images/Result/fallbackArchetype.jpg'
import heroJpg from '@images/Result/hero.jpg'
import innocentJpg from '@images/Result/innocent.jpg'
import loverJpg from '@images/Result/lover.jpg'
import magicianJpg from '@images/Result/magician.jpg'
import rebelJpg from '@images/Result/rebel.jpg'
import rulerJpg from '@images/Result/ruler.jpg'
import sageJpg from '@images/Result/sage.jpg'

// รูป Style
import alluringJpg from '@images/Style/alluring.jpg'
import ClassicJpg from '@images/Style/Classic.jpg'
import CreativeJpg from '@images/Style/Creative.jpg'
import DramaticJpg from '@images/Style/Dramatic.jpg'
import elegantJpg from '@images/Style/elegant.jpg'
import FeminineJpg from '@images/Style/Feminine.jpg'
import NaturalJpg from '@images/Style/Natural.jpg'

// Map Archetype
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

// Map Style
const styleImageMap = {
  alluring: alluringJpg,
  Classic: ClassicJpg,
  Creative: CreativeJpg,
  Dramatic: DramaticJpg,
  elegant: elegantJpg,
  Feminine: FeminineJpg,
  Natural: NaturalJpg,
}

// หน้าบางใช้ layout blank
definePage({
  meta: { layout: 'blank', public: true },
})

// --- State ---
const lineUdid = ref('')

onMounted(async () => {
  try {
    // ถ้าใช้ LIFF
    await liff.init({ liffId: '2006911756-4J15o7wx' })
    if (!liff.isLoggedIn()) {
      liff.login()
    } else {
      const profile = await liff.getProfile()
      lineUdid.value = profile.userId
    }
  } catch (err) {
    console.error('LIFF init error', err)
  }
})

// เก็บลำดับข้อ
const quizIndex = ref(0)
// เก็บคำตอบ
const selectedAnswers = ref(Array(quizQuestions.length).fill(null))
// state แสดงหน้า Result
const showResult = ref(false)

// นับสไตล์
const styleCounts = ref({
  Natural: 0,
  Classic: 0,
  elegant: 0,
  Feminine: 0,
  alluring: 0,
  Creative: 0,
  Dramatic: 0,
})

// Archetype
const finalType = ref('')
const finalTypeId = ref('')

// เก็บ style สูงสุด
const selectedStyles = ref([])

// --- Computed ---
const currentQuestion = computed(() => quizQuestions[quizIndex.value])

/** หาก Archetype ไม่มีค่า ก็ใช้ fallback */
const archetypeImage = computed(() => {
  if (!finalTypeId.value) return fallbackJpg
  return archetypeImageMap[finalTypeId.value] || fallbackJpg
})

/**
 * รวมรูปทั้งหมด (Archetype + Styles)
 * เพื่อโชว์เต็มจอเรียงกัน
 */
const displayImages = computed(() => {
  const arr = []
  // รูป archetype ตัวแรก
  arr.push(archetypeImage.value)
  // ตามด้วยรูป style ที่เลือก
  selectedStyles.value.forEach(styleName => {
    if (styleImageMap[styleName]) {
      arr.push(styleImageMap[styleName])
    }
  })
  return arr
})

// --- Methods ---
function prevQuestion() {
  if (quizIndex.value > 0) {
    quizIndex.value--
  }
}

async function nextOrShowResult() {
  updateStyleForQuestion(quizIndex.value)
  if (quizIndex.value < quizQuestions.length - 1) {
    quizIndex.value++
  } else {
    // ถึงข้อสุดท้าย => สรุปผล
    await sendQuizResult()
    showResult.value = true
  }
}

function updateStyleForQuestion(qIdx) {
  const answer = selectedAnswers.value[qIdx]
  if (answer === null) return

  const question = quizQuestions[qIdx]

  // +คะแนนสไตล์
  const arrStyle = question.style?.[answer]
  if (arrStyle && arrStyle.length) {
    arrStyle.forEach(styleName => {
      if (styleCounts.value[styleName] !== undefined) {
        styleCounts.value[styleName] += 1
      }
    })
  }

  // เก็บ Archetype
  if (question.type && question.typeid) {
    finalType.value = question.type[answer]
    finalTypeId.value = question.typeid[answer]
  }
}

async function sendQuizResult() {
  try {
    const quizLink = quizQuestions.map((q, index) => ({
      question: q.question,
      answerIndex: selectedAnswers.value[index],
      answerText: q.choices[selectedAnswers.value[index]],
    }))

    // หาคะแนน style สูงสุด
    const maxCount = Math.max(...Object.values(styleCounts.value))
    const finalStylesArr = Object.entries(styleCounts.value)
      .filter(([_, count]) => count === maxCount && count > 0)
      .map(([styleName]) => styleName)

    selectedStyles.value = finalStylesArr

    // เก็บ Archetype เป็น array
    const finalTypes = []
    if (finalType.value) {
      finalTypes.push(finalType.value)
    }

    // เรียก API เพื่อบันทึก
    await fetch('/api/quiz-users/submit-quiz', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        line_udid: lineUdid.value || 'UNKNOWN_LINE_UDID',
        quiz_link: quizLink,
        styles: finalStylesArr,
        types: finalTypes,
      }),
    })
  } catch (err) {
    console.error('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', err)
  }
}

function goHome() {
  window.location.href = '/home'
}
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

/* พื้นหลัง Quiz */
.bg-full {
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.no-sizer .v-responsive__sizer {
  display: none !important;
}

.logo {
  block-size: auto !important;
  inline-size: 180px !important;
}

/* ---------------------------
   Quiz Page
---------------------------- */
.quiz-container {
  min-block-size: 100%;
}

.quiz-card {
  overflow: hidden;
  background-color: #fffc; /* ขาวโปร่ง */
  inline-size: 100%;
  max-inline-size: 800px;
  min-block-size: 600px;
}

.title-section {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.quiz-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
}

.quiz-subtitle {
  margin: 0;
  color: #444;
  font-size: 1rem;
}

.progress-bar {
  max-inline-size: 400px;
}

.question-text {
  font-size: 1rem;
  font-weight: 600;
}

.choice-container {
  margin-block-start: 1rem;
  max-block-size: 400px;
  overflow-y: auto;
}

.choice-container .v-selection-control__label {
  font-size: 0.9rem;
}

.btn-dark {
  background-color: #192b2d !important;
  color: #fff !important;
}

.btn-dark.v-btn--disabled {
  background-color: #192b2d !important;
  opacity: 0.5;
}

/* ---------------------------
   Result Overlay
---------------------------- */
.result-overlay {
  position: absolute;
  z-index: 9999;

  /* ถ้าต้องการพื้นหลังโปร่งใสเล็กน้อยก็ใส่ได้
     background-color: rgba(0,0,0,0.4);
  */
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between; /* ปุ่มอยู่ล่าง */
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;

  /* อนุญาตให้ scroll ถ้ารูปหลายรูป */
  overflow-y: auto;
  padding-block-end: 2rem; /* เว้นที่ให้ปุ่มด้านล่าง */
}

.images-stack {
  display: flex;
  flex-direction: column;
  align-items: stretch; /* ให้ความกว้างเต็มพ่อ */
  inline-size: 100%;
}

.full-screen-image {
  position: relative;
  block-size: 100vh; /* แต่ละรูปสูงเท่า viewport */
  inline-size: 100%;
}

.img-cover {
  display: block;
  block-size: 100%;
  inline-size: 100%;
  object-fit: cover; /* ภาพเต็มหน้าจอ (ตัดส่วนเกิน) */
}

/* ส่วนปุ่มอยู่ล่างสุด */
.finish-btn-container {
  margin-block-start: 1rem;
}
</style>
