<template>
  <VCard>
    <!-- ส่วนหัวของหน้า (Title / Subtitle) -->
    <VCardTitle class="d-flex align-center justify-space-between">
      <div>
        <h2>จัดการแพ็กเกจ (Package Management)</h2>
        <p class="text-muted">สร้างและแก้ไขแพ็กเกจ ระบุชื่อแพ็กเกจ Coin ที่ได้รับ ราคา และหมายเหตุ</p>
      </div>
      <VBtn color="success" class="ml-auto" @click="openAddPackageDialog">
        + เพิ่มแพ็กเกจ
      </VBtn>
    </VCardTitle>

    <VCardText>
      <hr class="my-4" />

      <!-- ตารางแสดงแพ็กเกจ -->
      <VDataTable
        :headers="packageHeaders"
        :items="packages"
        :items-per-page="5"
        height="300"
        fixed-header
      >
        <!-- ตัวอย่างการทำ Custom Table Header (ถ้าอยากปรับสไตล์เพิ่มเติม) -->
        <template #header="{ props }">
          <thead>
            <tr>
              <!-- วนลูปสร้างหัวตารางจาก packageHeaders -->
              <th
                v-for="header in props.headers"
                :key="header.value"
                class="text-center"
              >
                {{ header.text }}
              </th>
            </tr>
          </thead>
        </template>

        <!-- คอลัมน์ Actions -->
        <template #item.actions="{ item }">
          <VBtn
            size="small"
            color="warning"
            class="me-2"
            @click="openEditPackage(item)"
          >
            แก้ไข
          </VBtn>
          <VBtn
            size="small"
            color="error"
            @click="deletePackage(item)"
          >
            ลบ
          </VBtn>
        </template>
      </VDataTable>
    </VCardText>
  </VCard>

  <!-- Dialog Add/Edit Package -->
  <VDialog v-model="showPackageDialog" max-width="600px">
    <VCard>
      <VCardTitle>
        <span v-if="isEditing">แก้ไขแพ็กเกจ</span>
        <span v-else>เพิ่มแพ็กเกจ</span>
      </VCardTitle>
      <VCardText>
        <div class="mb-3">
          <VTextField
            v-model="packageForm.package_name"
            label="ชื่อแพ็กเกจ"
          />
        </div>
        <div class="d-flex mb-3" style="gap: 10px;">
          <VTextField
            v-model="packageForm.coins"
            label="จำนวน Coins"
            type="number"
            style="flex: 1;"
          />
          <VTextField
            v-model="packageForm.price"
            label="ราคา"
            type="number"
            step="0.01"
            style="flex: 1;"
          />
        </div>
        <div class="mb-3">
          <VTextarea
            v-model="packageForm.notes"
            label="หมายเหตุ"
          />
        </div>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn color="grey" @click="showPackageDialog = false">ยกเลิก</VBtn>
        <VBtn color="primary" @click="savePackage">บันทึก</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

// กรณีใช้ Laravel Sanctum + SPA ให้ปลดคอมเมนต์ถ้าจำเป็น
// axios.defaults.withCredentials = true

// ข้อมูลคอลัมน์ในตาราง
const packageHeaders = [
  { text: 'ชื่อแพ็กเกจ', value: 'package_name' },
  { text: 'Coins ที่ได้รับ', value: 'coins' },
  { text: 'ราคา (บาท)', value: 'price' },
  { text: 'หมายเหตุ', value: 'notes' },
  { text: 'การจัดการ', value: 'actions', sortable: false },
]

// รายการแพ็กเกจ
const packages = ref([])

// Dialog และสถานะแก้ไข
const showPackageDialog = ref(false)
const isEditing = ref(false)

// ฟอร์มข้อมูลแพ็กเกจ
const packageForm = ref({
  id: null,
  package_name: '',
  coins: 0,
  price: 0.00,
  notes: '',
})

// เมื่อหน้าโหลด ให้ดึงข้อมูลจาก API
onMounted(() => {
  loadPackages()
})

// ฟังก์ชันโหลดข้อมูลแพ็กเกจทั้งหมดจาก API
async function loadPackages() {
  try {
    const response = await axios.get('/api/packages') 
    packages.value = response.data
  } catch (error) {
    console.error('Load packages error:', error)
    alert('ไม่สามารถโหลดข้อมูลแพ็กเกจได้')
  }
}

// ฟังก์ชันเปิด Dialog เพิ่มแพ็กเกจ
function openAddPackageDialog() {
  isEditing.value = false
  packageForm.value = {
    id: null,
    package_name: '',
    coins: 0,
    price: 0.00,
    notes: '',
  }
  showPackageDialog.value = true
}

// ฟังก์ชันเปิด Dialog แก้ไขแพ็กเกจ
function openEditPackage(item) {
  isEditing.value = true
  packageForm.value = { ...item }
  showPackageDialog.value = true
}

// ฟังก์ชันบันทึกแพ็กเกจ (เพิ่มหรือแก้ไข)
function savePackage() {
  if (isEditing.value) {
    updatePackage()
  } else {
    createPackage()
  }
}

// เพิ่มแพ็กเกจใหม่ (POST -> /api/packages)
async function createPackage() {
  try {
    await axios.post('/api/packages', packageForm.value)
    alert('เพิ่มแพ็กเกจเรียบร้อย')
    showPackageDialog.value = false
    loadPackages()
  } catch (error) {
    console.error('Create package error:', error)
    alert('ไม่สามารถเพิ่มแพ็กเกจได้')
  }
}

// แก้ไขแพ็กเกจ (PUT -> /api/packages/:id)
async function updatePackage() {
  try {
    await axios.put(`/api/packages/${packageForm.value.id}`, packageForm.value)
    alert('แก้ไขแพ็กเกจเรียบร้อย')
    showPackageDialog.value = false
    loadPackages()
  } catch (error) {
    console.error('Update package error:', error)
    alert('ไม่สามารถแก้ไขแพ็กเกจได้')
  }
}

// ลบแพ็กเกจ (DELETE -> /api/packages/:id)
async function deletePackage(item) {
  if (!confirm(`ยืนยันลบแพ็กเกจ: ${item.package_name}?`)) return
  try {
    await axios.delete(`/api/packages/${item.id}`)
    alert('ลบแพ็กเกจเรียบร้อย')
    loadPackages()
  } catch (error) {
    console.error('Delete package error:', error)
    alert('ไม่สามารถลบแพ็กเกจได้')
  }
}
</script>

<style scoped>
.text-muted {
  color: #6c757d !important;
}
</style>
