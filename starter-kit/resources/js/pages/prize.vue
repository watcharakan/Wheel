<!-- App.vue -->
<template>
  <v-app>
    <VMain class="pa-4">
      <VCard>
        <!-- ส่วนหัวของหน้า -->
        <VCardTitle class="d-flex align-center justify-space-between">
          <div>
            <h2>จัดการของรางวัล (Reward Management)</h2>
            <p class="text-muted">
              สร้างและแก้ไขของรางวัล พร้อมกำหนดชื่อ, รายละเอียด, สี, จำนวน และโอกาสได้รับ
            </p>
          </div>
          <VBtn color="success" class="ml-auto" @click="openAddRewardDialog">
            + เพิ่มรางวัล
          </VBtn>
        </VCardTitle>

        <VCardText>
          <hr class="my-4" />

          <!-- ส่วนปรับสีเพิ่มเติม -->
          <div class="d-flex mb-4" style="gap: 16px; align-items: center;">
            <div>
              <label>Pointer Color:</label>
              <VTextField
                v-model="pointerColor"
                type="color"
                hide-details
                style="max-width: 70px;"
              />
            </div>
            <div>
              <label>Center Pin:</label>
              <VTextField
                v-model="centerPinColor"
                type="color"
                hide-details
                style="max-width: 70px;"
              />
            </div>
            <div>
              <label>Pin Border:</label>
              <VTextField
                v-model="centerPinStrokeColor"
                type="color"
                hide-details
                style="max-width: 70px;"
              />
            </div>

            <!-- ใหม่: สีขอบวงล้อ -->
            <div>
              <label>Outer Circle Color:</label>
              <VTextField
                v-model="outerCircleColor"
                type="color"
                hide-details
                style="max-width: 70px;"
              />
            </div>

            <!-- ใหม่: สี/เงาของ wedge -->
            <div>
              <label>Wedge Border:</label>
              <VTextField
                v-model="wedgeBorderColor"
                type="color"
                hide-details
                style="max-width: 70px;"
              />
            </div>
            <div>
              <label>Wedge Shadow:</label>
              <VTextField
                v-model="wedgeShadowColor"
                type="color"
                hide-details
                style="max-width: 70px;"
              />
            </div>

            <VBtn color="primary" @click="drawWheel">Apply</VBtn>
          </div>

          <div class="d-flex">
            <!-- DataTable (ซ้าย) -->
            <div style="flex: 1; margin-right: 20px;">
              <VDataTable
                :headers="rewardHeaders"
                :items="rewards"
                :items-per-page="5"
                height="300"
                fixed-header
              >
                <!-- หัวตาราง -->
                <template #header="{ headers }">
                  <thead>
                    <tr>
                      <th v-for="header in headers" :key="header.value">
                        {{ header.text }}
                      </th>
                    </tr>
                  </thead>
                </template>

                <!-- คอลัมน์ Actions -->
                <template #item.actions="{ item }">
                  <VBtn size="small" color="warning" class="me-2" @click="openEditReward(item)">
                    แก้ไข
                  </VBtn>
                  <VBtn size="small" color="error" @click="deleteReward(item)">
                    ลบ
                  </VBtn>
                </template>
              </VDataTable>
            </div>

            <!-- วงล้อ (ขวา) -->
            <div style="flex: 1; text-align: center;">
              <h3>วงล้อของรางวัล</h3>
              <canvas
                ref="wheelCanvas"
                :width="canvasSize"
                :height="canvasSize"
                style="border: 1px solid #ccc; border-radius: 50%;"
              ></canvas>

              <!-- ปุ่ม Spin -->
              <div class="mt-3">
                <VBtn color="primary" @click="spinWheel">Spin!</VBtn>
              </div>

              <!-- ผลลัพธ์ -->
              <div v-if="spinResult" class="mt-3">
                <strong>ผลการสุ่ม:</strong> {{ spinResult.name }}
              </div>
            </div>
          </div>
        </VCardText>

        <!-- Dialog เพิ่ม/แก้ไข -->
        <VDialog v-model="showRewardDialog" max-width="600px">
          <VCard>
            <VCardTitle>
              <span v-if="isEditing">แก้ไขของรางวัล</span>
              <span v-else>เพิ่มของรางวัล</span>
            </VCardTitle>
            <VCardText>
              <!-- ฟอร์มกรอกชื่อ/รายละเอียด/สี/จำนวน/chance -->
              <div class="mb-3">
                <VTextField v-model="rewardForm.name" label="ชื่อรางวัล" />
              </div>
              <div class="mb-3">
                <VTextarea v-model="rewardForm.detail" label="รายละเอียดรางวัล" />
              </div>
              <div class="d-flex mb-3" style="gap: 10px;">
                <VTextField
                  v-model="rewardForm.color"
                  label="เลือกสี"
                  type="color"
                  style="flex: 1;"
                />
                <VTextField
                  v-model="rewardForm.quantity"
                  label="จำนวน"
                  type="number"
                  style="flex: 1;"
                />
                <VTextField
                  v-model="rewardForm.chance"
                  label="โอกาส (ตัวเลข)"
                  type="number"
                  style="flex: 1;"
                />
              </div>
            </VCardText>
            <VCardActions>
              <VSpacer />
              <VBtn color="grey" @click="showRewardDialog = false">ยกเลิก</VBtn>
              <VBtn color="primary" @click="saveReward">บันทึก</VBtn>
            </VCardActions>
          </VCard>
        </VDialog>
      </VCard>
    </VMain>
  </v-app>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import axios from 'axios'

// ======= Mock Data =======
const mockRewards = [
  { id: 1, name: 'Reward A', detail: 'ของรางวัล A', color: '#f44336', quantity: 5, chance: 2 },
  { id: 2, name: 'Reward B', detail: 'ของรางวัล B', color: '#2196f3', quantity: 3, chance: 1 },
  { id: 3, name: 'Reward C', detail: 'ของรางวัล C', color: '#4caf50', quantity: 2, chance: 1 },
]
// =========================

// axios baseURL ถ้ามี backend
axios.defaults.withCredentials = true

// DataTable Headers
const rewardHeaders = [
  { text: 'ชื่อรางวัล', value: 'name' },
  { text: 'รายละเอียด', value: 'detail' },
  { text: 'สี', value: 'color' },
  { text: 'จำนวน', value: 'quantity' },
  { text: 'โอกาส', value: 'chance' },
  { text: 'การจัดการ', value: 'actions', sortable: false },
]

// State หลัก
const rewards = ref([])
const showRewardDialog = ref(false)
const isEditing = ref(false)
const spinResult = ref(null)

// ฟอร์มรางวัล
const rewardForm = ref({
  id: null,
  name: '',
  detail: '',
  color: '#FF0000',
  quantity: 1,
  chance: 1,
})

// ขนาด Canvas
const canvasSize = 300
const wheelCanvas = ref(null)
let isSpinning = false

// **ตัวแปรกำหนดสี** 
const pointerColor = ref('#000000')       
const centerPinColor = ref('#ff0000')     
const centerPinStrokeColor = ref('#8b0000') 
const outerCircleColor = ref('#00BFFF')
const wedgeBorderColor = ref('#ffffff')    
const wedgeShadowColor = ref('#000000')    

// โหลดข้อมูล Mock
onMounted(() => {
  loadRewards()
})

// ดึงข้อมูล (mock)
async function loadRewards() {
  await new Promise(r => setTimeout(r, 500))
  rewards.value = mockRewards
  await nextTick()
  drawWheel()
}

function openAddRewardDialog() {
  isEditing.value = false
  rewardForm.value = {
    id: null,
    name: '',
    detail: '',
    color: '#FF0000',
    quantity: 1,
    chance: 1,
  }
  showRewardDialog.value = true
}

function openEditReward(item) {
  isEditing.value = true
  rewardForm.value = { ...item }
  showRewardDialog.value = true
}

function saveReward() {
  if (isEditing.value) {
    updateReward()
  } else {
    createReward()
  }
}

function createReward() {
  // สร้าง id ใหม่ mock
  const newId = rewards.value.length
    ? Math.max(...rewards.value.map(r => r.id)) + 1
    : 1

  const newReward = { ...rewardForm.value, id: newId }
  rewards.value.push(newReward)   // Reactive update
  showRewardDialog.value = false
  alert('เพิ่มของรางวัลเรียบร้อย')
}

function updateReward() {
  if (!rewardForm.value.id) return
  const idx = rewards.value.findIndex(r => r.id === rewardForm.value.id)
  if (idx !== -1) {
    rewards.value[idx] = { ...rewardForm.value }
  }
  showRewardDialog.value = false
  alert('แก้ไขของรางวัลเรียบร้อย')
}

function deleteReward(item) {
  if (!confirm(`ยืนยันลบรางวัล: ${item.name}?`)) return
  rewards.value = rewards.value.filter(r => r.id !== item.id)
  alert('ลบรางวัลเรียบร้อย')
}

// รวม chance
const totalChance = computed(() => {
  return rewards.value.reduce((acc, r) => acc + Number(r.chance), 0)
})

function drawWheel() {
  if (!wheelCanvas.value) return
  const ctx = wheelCanvas.value.getContext('2d')
  if (!ctx) return

  ctx.clearRect(0, 0, canvasSize, canvasSize)

  const centerX = canvasSize / 2
  const centerY = canvasSize / 2
  const radius = canvasSize / 2

  // พื้นหลัง (outer circle)
  ctx.save()
  ctx.fillStyle = outerCircleColor.value
  ctx.beginPath()
  ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI)
  ctx.fill()
  ctx.restore()

  // segments (ถ้ามี reward)
  if (!rewards.value.length || totalChance.value === 0) {
    ctx.save()
    ctx.fillStyle = '#eee'
    ctx.beginPath()
    ctx.arc(centerX, centerY, radius - 10, 0, 2 * Math.PI)
    ctx.fill()
    ctx.restore()
  } else {
    let startAngle = 0
    for (const r of rewards.value) {
      const sliceAngle = (Number(r.chance) / totalChance.value) * 2 * Math.PI
      const endAngle = startAngle + sliceAngle

      ctx.save()
      // ใส่เงา/ขอบ wedge
      ctx.shadowColor = wedgeShadowColor.value
      ctx.shadowBlur = 5
      ctx.shadowOffsetX = 2
      ctx.shadowOffsetY = 2

      ctx.beginPath()
      ctx.moveTo(centerX, centerY)
      ctx.arc(centerX, centerY, radius - 10, startAngle, endAngle)
      ctx.fillStyle = r.color
      ctx.fill()

      // เส้นขอบ wedge
      ctx.strokeStyle = wedgeBorderColor.value
      ctx.lineWidth = 2
      ctx.stroke()
      ctx.restore()

      // วาดชื่อ
      ctx.save()
      ctx.fillStyle = '#fff'
      ctx.font = 'bold 14px sans-serif'
      ctx.translate(centerX, centerY)
      ctx.rotate((startAngle + endAngle) / 2)
      ctx.textAlign = 'right'
      ctx.fillText(r.name, (radius - 10) - 10, 5)
      ctx.restore()

      startAngle = endAngle
    }
  }

  // ขอบใน
  ctx.save()
  ctx.strokeStyle = '#fff'
  ctx.lineWidth = 6
  ctx.beginPath()
  ctx.arc(centerX, centerY, radius - 10, 0, 2 * Math.PI)
  ctx.stroke()
  ctx.restore()

  drawPointer(ctx)
  drawCenterPin(ctx)
}

// วาด pointer (สามเหลี่ยม)
function drawPointer(ctx) {
  const centerX = canvasSize / 2
  const centerY = canvasSize / 2
  const radius = canvasSize / 2

  ctx.save()
  ctx.fillStyle = pointerColor.value
  ctx.beginPath()
  ctx.moveTo(centerX, centerY - radius + 10)
  ctx.lineTo(centerX - 15, centerY - radius + 35)
  ctx.lineTo(centerX + 15, centerY - radius + 35)
  ctx.closePath()
  ctx.fill()
  ctx.restore()
}

// วาด center pin (วงกลมตรงกลาง)
function drawCenterPin(ctx) {
  const centerX = canvasSize / 2
  const centerY = canvasSize / 2
  ctx.save()
  ctx.fillStyle = centerPinColor.value
  ctx.beginPath()
  ctx.arc(centerX, centerY, 10, 0, 2*Math.PI)
  ctx.fill()

  // border
  ctx.strokeStyle = centerPinStrokeColor.value
  ctx.lineWidth = 2
  ctx.stroke()
  ctx.restore()
}

// หมุนแบบสุ่ม: หยุดตรงกลาง wedge
function spinWheel() {
  if (isSpinning) return
  if (!rewards.value.length) {
    alert('ยังไม่มีของรางวัลให้หมุน')
    return
  }
  isSpinning = true
  spinResult.value = null

  const { finalAngle, reward } = pickRandomFinalAngle()
  const spinDuration = 3000
  const startTime = performance.now()

  requestAnimationFrame(function animate(now) {
    const elapsed = now - startTime
    let t = elapsed / spinDuration
    if (t > 1) t = 1

    const eased = t * (2 - t) // easeOutQuad
    const angle = finalAngle * eased

    rotateWheel(angle)
    if (t < 1) {
      requestAnimationFrame(animate)
    } else {
      spinResult.value = reward
      isSpinning = false
    }
  })
}

// เลือกรางวัลแบบสุ่มตาม chance และคำนวณมุม finalAngle
function pickRandomFinalAngle() {
  const total = totalChance.value
  if (total === 0) return { finalAngle: 0, reward: null }

  const rand = Math.random() * total
  let accum = 0
  let selectedReward = null
  let wedgeStart = 0
  let wedgeEnd = 0

  for (const r of rewards.value) {
    const c = Number(r.chance)
    if (accum <= rand && rand < accum + c) {
      selectedReward = r
      wedgeEnd = accum + c
      break
    }
    accum += c
  }
  wedgeStart = accum

  const wedgeFractionStart = wedgeStart / total
  const wedgeFractionEnd   = wedgeEnd   / total
  const startAngle = wedgeFractionStart * 2 * Math.PI
  const endAngle   = wedgeFractionEnd   * 2 * Math.PI
  const centerAngle = (startAngle + endAngle) / 2

  const centerAngleDeg = centerAngle * 180 / Math.PI
  // pointer อยู่บน = 270°
  let finalAngle = 270 - centerAngleDeg
  finalAngle = (finalAngle % 360 + 360) % 360
  // หมุน 3 รอบ
  finalAngle += 360 * 3

  return { finalAngle, reward: selectedReward }
}

function rotateWheel(angleDeg) {
  if (!wheelCanvas.value) return
  const ctx = wheelCanvas.value.getContext('2d')
  if (!ctx) return

  ctx.clearRect(0, 0, canvasSize, canvasSize)
  ctx.save()
  const centerX = canvasSize / 2
  const centerY = canvasSize / 2
  ctx.translate(centerX, centerY)
  ctx.rotate(angleDeg * Math.PI / 180)
  ctx.translate(-centerX, -centerY)

  drawWheelSegmentOnly(ctx)
  ctx.restore()

  drawPointer(ctx)
  drawCenterPin(ctx)
}

// วาดเฉพาะ segment + border (ไม่รวม pointer/pin)
function drawWheelSegmentOnly(ctx) {
  const centerX = canvasSize / 2
  const centerY = canvasSize / 2
  const radius = canvasSize / 2

  // วาด background outer circle
  ctx.save()
  ctx.fillStyle = outerCircleColor.value
  ctx.beginPath()
  ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI)
  ctx.fill()
  ctx.restore()

  if (!rewards.value.length || totalChance.value === 0) {
    // กรณีไม่มีรางวัล
    ctx.save()
    ctx.fillStyle = '#eee'
    ctx.beginPath()
    ctx.arc(centerX, centerY, radius - 10, 0, 2 * Math.PI)
    ctx.fill()
    ctx.restore()
  } else {
    let startAngle = 0
    for (const r of rewards.value) {
      const sliceAngle = (Number(r.chance) / totalChance.value) * 2 * Math.PI
      const endAngle = startAngle + sliceAngle

      ctx.save()
      // ใส่เงา/ขอบ wedge
      ctx.shadowColor = wedgeShadowColor.value
      ctx.shadowBlur = 5
      ctx.shadowOffsetX = 2
      ctx.shadowOffsetY = 2

      ctx.beginPath()
      ctx.moveTo(centerX, centerY)
      ctx.arc(centerX, centerY, radius - 10, startAngle, endAngle)
      ctx.fillStyle = r.color
      ctx.fill()

      // เส้นขอบ wedge
      ctx.strokeStyle = wedgeBorderColor.value
      ctx.lineWidth = 2
      ctx.stroke()
      ctx.restore()

      // ชื่อ
      ctx.save()
      ctx.fillStyle = '#fff'
      ctx.font = 'bold 14px sans-serif'
      ctx.translate(centerX, centerY)
      ctx.rotate((startAngle + endAngle) / 2)
      ctx.textAlign = 'right'
      ctx.fillText(r.name, (radius - 10) - 10, 5)
      ctx.restore()

      startAngle = endAngle
    }
  }

  // ขอบใน
  ctx.save()
  ctx.strokeStyle = '#fff'
  ctx.lineWidth = 6
  ctx.beginPath()
  ctx.arc(centerX, centerY, radius - 10, 0, 2 * Math.PI)
  ctx.stroke()
  ctx.restore()
}
</script>

<style scoped>
.text-muted {
  color: #6c757d !important;
}
canvas {
  display: block;
  margin: 0 auto;
  border-radius: 50%;
}
.d-flex {
  display: flex;
}
.me-2 {
  margin-right: 8px;
}
.ml-auto {
  margin-left: auto;
}
.pa-4 {
  padding: 16px;
}
.my-4 {
  margin-top: 16px;
  margin-bottom: 16px;
}
</style>
