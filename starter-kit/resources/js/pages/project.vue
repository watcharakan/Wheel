<template>
  <VCard>
    <!-- Stepper -->
    <VCardText>
      <AppStepper
        v-model:current-step="currentStep"
        :items="iconsSteps"
        align="center"
      />
    </VCardText>

    <VDivider />

    <!-- Steps Content -->
    <VCardText>
      <VWindow v-model="currentStep" class="disable-tab-transition">

        <!-- ========== STEP 0: Google Maps Mockup ========== -->
        <VWindowItem>
          <div class="step-wrapper">
            <h5 class="mb-3">Step 1: เลือกร้านใน Google Maps (Mockup)</h5>

            <!-- ค้นหา -->
            <v-text-field
              v-model="placeQuery"
              label="ค้นหาร้านใน Google Maps"
              append-inner-icon="bx-search"
              variant="outlined"
              density="comfortable"
              @keyup.enter="searchPlaces"
              class="mb-4"
            />
            <v-btn color="primary" class="mb-4" @click="searchPlaces">
              ค้นหา (Mockup)
            </v-btn>

            <!-- รายการผลลัพธ์ -->
            <v-list v-if="placeResults.length" class="place-list mb-4 elevation-1">
              <v-list-item
                v-for="place in placeResults"
                :key="place.place_id"
                @click="selectPlace(place)"
              >
                <v-list-item-title>{{ place.name }}</v-list-item-title>
                <v-list-item-subtitle>{{ place.formatted_address }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>

            <!-- แสดงข้อมูลร้านที่เลือก -->
            <div v-if="selectedPlace" class="selected-place mb-4">
              <strong>{{ selectedPlace.name }}</strong>
              <div>{{ selectedPlace.formatted_address }}</div>
              <div class="mt-2">
                <span class="me-1">ลิงก์รีวิว:</span>
                <a :href="reviewLink" target="_blank" class="review-link">{{ reviewLink }}</a>
              </div>
            </div>

            <!-- ปุ่มถัดไป -->
            <v-btn color="primary" :disabled="!selectedPlace" @click="currentStep++">
              ถัดไป
              <v-icon icon="bx-right-arrow-alt" end class="flip-in-rtl" />
            </v-btn>
          </div>
        </VWindowItem>

        <!-- ========== STEP 1: ตั้งค่าโปรเจ็กต์ ========== -->
        <VWindowItem>
          <div class="step-wrapper">
            <h5 class="mb-3">Step 2: ตั้งค่ารายละเอียดโปรเจ็กต์</h5>

            <v-text-field
              v-model="project.name"
              label="ชื่อโปรเจ็กต์"
              class="mb-3"
            />
            <v-textarea
              v-model="project.description"
              label="รายละเอียดโปรเจ็กต์"
              rows="3"
              class="mb-6"
            />

            <div class="d-flex flex-wrap gap-4 mb-6">
              <div style="min-inline-size:220px;">
                <label class="mb-1 d-block">สีธีม (themeColor)</label>
                <v-color-picker
                  v-model="project.themeColor"
                  hide-inputs
                  dot-size="18"
                />
              </div>
              <div style="min-inline-size:220px;">
                <label class="mb-1 d-block">สีตัวหนังสือ (textColor)</label>
                <v-color-picker
                  v-model="project.textColor"
                  hide-inputs
                  dot-size="18"
                />
              </div>
            </div>

            <!-- อัปโหลดรูป Banner -->
            <v-file-input
              v-model="bannerFile"
              label="อัปโหลดรูป Banner"
              accept="image/*"
              prepend-icon="mdi-image"
              show-size
              class="mb-3"
              @update:model-value="previewImage('banner')"
            />
            <v-img
              v-if="bannerPreview"
              :src="bannerPreview"
              class="mb-6"
              max-height="140"
              cover
            />

            <!-- อัปโหลดรูป Logo -->
            <v-file-input
              v-model="profileFile"
              label="อัปโหลดรูปโลโก้ (Logo)"
              accept="image/*"
              prepend-icon="mdi-image"
              show-size
              class="mb-3"
              @update:model-value="previewImage('profile')"
            />
            <v-img
              v-if="profilePreview"
              :src="profilePreview"
              class="mb-6"
              max-height="120"
              width="120"
              cover
              style="border-radius:50%"
            />

            <!-- Preview (Step 1) -->
            <div v-if="bannerPreview" class="preview-banner mb-4" :style="bannerStyle">
              <div class="preview-content" :style="{ color: project.textColor }">
                <h4 class="mb-1">{{ project.name || 'ตัวอย่างชื่อโปรเจ็กต์' }}</h4>
                <p>{{ project.description || 'รายละเอียดสั้น ๆ' }}</p>
              </div>
            </div>

            <div class="d-flex justify-space-between">
              <v-btn color="secondary" variant="tonal" @click="currentStep--">
                <v-icon icon="bx-left-arrow-alt" start class="flip-in-rtl" />
                ย้อนกลับ
              </v-btn>

              <v-btn
                color="primary"
                :disabled="!project.name"
                @click="currentStep++"
              >
                ถัดไป
                <v-icon icon="bx-right-arrow-alt" end class="flip-in-rtl" />
              </v-btn>
            </div>
          </div>
        </VWindowItem>

        <!-- ========== STEP 2: ใช้ pseudo-element ไล่สี (transparent -> themeColor) และ bottom สี themeColor ========== -->
        <VWindowItem>
          <!-- จุดสำคัญ: ใส่ :style="{ '--theme-color': project.themeColor }" -->
          <div
            class="phone-mock-container"
            :style="{ '--theme-color': project.themeColor }"
          >

            <!-- (1) Banner ด้านบน 200px -->
            <div class="top-banner" :style="topBannerStyle">
              <!-- overlay -->
              <div class="banner-overlay"></div>
            </div>

            <!-- (2) ส่วนล่าง = สี themeColor -->
            <div class="bottom-section" :style="bottomSectionStyle">
              <div class="logo-wrapper">
                <img
                  v-if="profilePreview"
                  :src="profilePreview"
                  alt="Logo"
                  class="logo-img"
                />
              </div>

              <div class="store-title" :style="{ color: project.textColor }">
                {{ selectedPlace?.name || 'ยังไม่ได้เลือกร้าน' }}
              </div>
              <div class="store-subtitle" :style="{ color: project.textColor }">
                {{ project.name || 'ชื่อโปรเจ็กต์' }}
              </div>

              <div class="weird-text" :style="{ color: project.textColor }">
                ไล่สี pseudo-element → พื้นหลัง themeColor
              </div>

              <!-- ฟอร์ม -->
              <div class="form-fields">
                <v-text-field
                  v-model="registerForm.fullname"
                  label="Fullname :"
                  variant="outlined"
                  class="mb-3"
                />
                <v-text-field
                  v-model="registerForm.tel"
                  label="Tel :"
                  variant="outlined"
                  class="mb-3"
                />
              </div>

              <v-btn
                block
                large
                class="register-btn"
                :style="registerBtnStyle"
                @click="doRegister"
              >
                ลงทะเบียน
              </v-btn>

              <v-alert type="info" variant="tonal" class="mt-6">
                ตรวจสอบข้อมูลก่อนสร้างแคมเปญ
              </v-alert>

              <div class="d-flex align-center gap-4 mb-4">
                <v-btn
                  style="background-color: limegreen; color: #fff;"
                  :disabled="creating || created"
                  @click="createCampaign"
                >
                  สร้างแคมเปญ
                </v-btn>
                <v-chip
                  v-if="creating"
                  color="info"
                  size="small"
                  class="text-white"
                >
                  กำลังสร้าง...
                </v-chip>
                <v-chip
                  v-if="created"
                  color="success"
                  size="small"
                  class="text-white"
                >
                  สร้างแคมเปญเสร็จแล้ว!
                </v-chip>
              </div>

              <div v-if="created">
                <v-btn color="primary" @click="goToRewards">
                  ไปหน้าสร้างรางวัล
                  <v-icon icon="bx-right-arrow-alt" end class="flip-in-rtl" />
                </v-btn>
              </div>
            </div> <!-- bottom-section -->

          </div> <!-- phone-mock-container -->
        </VWindowItem>

      </VWindow>
    </VCardText>
  </VCard>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// Stepper items
const iconsSteps = [
  { title: 'เลือกร้าน', icon: 'bx-map-pin' },
  { title: 'ตั้งค่าโปรเจ็กต์', icon: 'bx-edit' },
  { title: 'สร้างรางวัล', icon: 'bx-gift' },
]
const currentStep = ref(0)
const router = useRouter()

// STEP 0 (mockup)
const GOOGLE_API_KEY = import.meta.env.VITE_GOOGLE_API_KEY
const placeQuery   = ref('')
const placeResults = ref([])
const selectedPlace= ref(null)
const reviewLink   = ref('')

async function searchPlaces() {
  if (!placeQuery.value.trim()) return
  try {
    const { data } = await axios.get(
      'https://maps.googleapis.com/maps/api/place/textsearch/json',
      {
        params: {
          query: placeQuery.value,
          key: GOOGLE_API_KEY,
          language: 'th',
        },
      }
    )
    placeResults.value = data.results || []
  } catch {
    alert('ค้นหาร้านไม่สำเร็จ (Mockup)')
  }
}
function selectPlace(place) {
  selectedPlace.value = place
  reviewLink.value = `https://search.google.com/local/writereview?placeid=${place.place_id}`
  placeResults.value = []
}

// STEP 1
const project = reactive({
  name: '',
  description: '',
  themeColor: '#1976D2',
  textColor: '#000000',
  bannerImageUrl: '',
  profileImageUrl: '',
})

const bannerFile     = ref(null)
const profileFile    = ref(null)
const bannerPreview  = ref('')
const profilePreview = ref('')

function previewImage(type) {
  const fileRef = (type === 'banner') ? bannerFile : profileFile
  if (!fileRef.value) return
  const reader = new FileReader()
  reader.onload = e => {
    if (type === 'banner') bannerPreview.value = e.target.result
    else profilePreview.value = e.target.result
  }
  reader.readAsDataURL(fileRef.value)
}

// STEP 2
const creating = ref(false)
const created  = ref(false)
const registerForm = reactive({
  fullname: '',
  tel: '',
})

// Banner Preview (Step 1)
const bannerStyle = computed(() => ({
  backgroundImage: `url(${bannerPreview.value})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
  borderRadius: '8px',
  padding: '2rem',
}))

// topBanner style
const topBannerStyle = computed(() => ({
  width: '100%',
  height: '200px',
  position: 'relative',
  background: bannerPreview.value
    ? `url(${bannerPreview.value}) center/cover no-repeat`
    : '#000000',
  overflow: 'hidden',
}))

// bottom = themeColor
const bottomSectionStyle = computed(() => ({
  flex: 1,
  padding: '1.5rem',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  gap: '0.5rem',
  backgroundColor: project.themeColor,
}))

// ปุ่ม ลงทะเบียน => background= textColor, color= themeColor
const registerBtnStyle = computed(() => ({
  backgroundColor: project.textColor + ' !important',
  color: project.themeColor + ' !important',
  fontWeight: '600',
}))

async function uploadImage(file) {
  const form = new FormData()
  form.append('file', file)
  const { data } = await axios.post('/api/uploads', form)
  return data.url
}
async function createCampaign() {
  creating.value = true
  try {
    if (bannerFile.value) {
      project.bannerImageUrl = await uploadImage(bannerFile.value)
    }
    if (profileFile.value) {
      project.profileImageUrl = await uploadImage(profileFile.value)
    }
    await axios.post('/api/campaigns', {
      place: selectedPlace.value,
      review_link: reviewLink.value,
      project,
      registerForm,
    })
    created.value = true
  } catch {
    alert('สร้างแคมเปญไม่สำเร็จ')
  } finally {
    creating.value = false
  }
}
function doRegister() {
  alert(`Fullname: ${registerForm.fullname}, Tel: ${registerForm.tel}`)
}
function goToRewards() {
  router.push({ name: 'rewards', query: { project: project.name } })
}
</script>

<style scoped>
.step-wrapper {
  padding: 1rem;
  border-radius: 8px;
  background-color: #f6f6f9;
}
.place-list {
  max-height: 300px;
  overflow-y: auto;
  background: #fff;
  border-radius: 8px;
}
.review-link {
  color: blue;
  text-decoration: underline;
}

/* STEP 1 Preview Banner */
.preview-banner {
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: dashed 2px #ddd;
}

/* STEP 2 Container
   ใส่ :style="{ '--theme-color': project.themeColor }"
   เพื่อให้ pseudo-element ดึง var(--theme-color)
*/
.phone-mock-container {
  width: 390px;
  height: 844px;
  margin: 0 auto;
  border: 1px solid #ccc;
  border-radius: 24px;
  overflow: hidden;
  display: flex;
  flex-direction: column;

  /* สำคัญ: กำหนดค่า default ไว้ก่อน */
  --theme-color: #1976D2; 
}

/* top-banner => pseudo-element */
.top-banner {
  position: relative;
}

/* ไล่สีจาก transparent -> var(--theme-color) */
.top-banner::after {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  pointer-events: none;
  z-index: 1;
  background-image: linear-gradient(
    rgba(0,0,0,0),
    var(--theme-color)
  );
}

.banner-overlay {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  z-index: 2;
}

/* bottom-section => style binding => backgroundColor: project.themeColor */
.bottom-section {
  flex: 1;
  /* override by bottomSectionStyle */
}

.logo-wrapper {
  margin: 1.5rem 0 1rem;
}
.logo-img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  /* no white border */
}

.store-title {
  font-size: 1.25rem;
  font-weight: 600;
}
.store-subtitle {
  margin-bottom: 0.3rem;
}
.weird-text {
  margin-bottom: 1rem;
  font-style: italic;
  text-align: center;
}

/* form fields */
.form-fields {
  width: 80%;
  margin: 0 auto;
}
.register-btn {
  margin-top: 1rem;
  width: 80%;
  font-weight: 600;
}
</style>
