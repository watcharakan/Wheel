<template>
  <div class="p-6">
    <!-- ======= แถวบน: ปุ่มเพิ่มลูกค้า + ช่องค้นหา + ตัวกรอง ======= -->
    <div
      class="d-flex flex-wrap align-center justify-space-between mb-6 gap-4"
      style="padding: 1rem; border-radius: 8px; background-color: #f6f6f9;"
    >
      <div class="d-flex flex-wrap align-center gap-4">
        <!-- ช่องค้นหา -->
        <v-text-field
          v-model="searchTerm"
          label="ค้นหาลูกค้า (Name, Phone)"
          variant="outlined"
          density="comfortable"
          style="min-inline-size: 300px;"
          @keyup.enter="onSearch"
          clearable
        />

        <!-- ตัวกรอง is_active -->
        <v-select
          :items="activeOptions"
          v-model="selectedActive"
          label="กรองสถานะ Active"
          variant="outlined"
          density="comfortable"
          :clearable="true"
        />
      </div>

      <!-- ปุ่มเพิ่มลูกค้า -->
      <v-btn color="primary" size="small" @click="openAddCustomerDialog">
        + เพิ่มลูกค้า
      </v-btn>
    </div>

    <!-- ======= DataTable: customers ======= -->
    <v-data-table
      :headers="headers"
      :items="filteredCustomers"
      :items-per-page="10"
      class="elevation-1"
    >
      <template #item="{ item }">
        <tr>
          <!-- ปุ่ม expand -->
          <td class="px-4 py-2">
            <v-btn variant="text" icon class="me-2" @click="toggleExpand(item)">
              <v-icon
                :icon="expandedRows[item.id] ? 'bx-chevron-up' : 'bx-chevron-down'"
              />
            </v-btn>

            <!-- แสดง Avatar + ชื่อ และชื่อเล่น -->
            <div class="d-flex align-center gap-2">
              <v-avatar size="32">
                <img
                  v-if="item.avatar"
                  :src="item.avatar"
                  alt="Avatar"
                  style="background-color: #f0f0f0; object-fit: contain;"
                />
                <img
                  v-else
                  src="https://via.placeholder.com/40?text=NoPic"
                  alt="No Pic"
                />
              </v-avatar>

              <div>
                <strong>{{ item.name || '-' }}</strong>
                <div style="color: #666; font-size: 0.9rem;">
                  {{ item.nick_name || '(No Nickname)' }}
                </div>
              </div>
            </div>
          </td>

          <!-- Phone -->
          <td class="px-4 py-2">{{ item.phone || '-' }}</td>

          <!-- Email -->
          <td class="px-4 py-2">{{ item.email || '-' }}</td>

          <!-- Activity Date -->
          <td class="px-4 py-2">{{ item.activity_date || '-' }}</td>

          <!-- Coupons + ปุ่มใช้คูปอง -->
          <td class="px-4 py-2">
            <div class="d-inline-flex flex-wrap gap-1">
              <v-chip
                v-for="(coupon, idx) in item.coupons"
                :key="idx"
                :color="coupon.used ? 'success' : 'primary'"
                class="text-white d-flex align-center"
                size="small"
              >
                {{ coupon.name }}
                <!-- ถ้าคูปองยังไม่ถูกใช้ แสดงปุ่ม "ใช้" -->
                <v-btn
                  v-if="!coupon.used"
                  variant="text"
                  size="x-small"
                  color="blue"
                  style="margin-left: 4px;"
                  @click.stop="useCoupon(item, coupon)"
                >
                  ใช้
                </v-btn>
                <!-- ถ้าใช้ไปแล้ว แสดง "(ใช้แล้ว)" -->
                <span v-else style="margin-left: 4px; font-size: 0.8rem;">
                  (ใช้แล้ว)
                </span>
              </v-chip>
            </div>
          </td>

          <!-- Is Active -->
          <td class="px-4 py-2">
            <v-chip
              :color="item.is_active ? 'success' : 'error'"
              size="small"
              class="text-white"
            >
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </v-chip>
          </td>

          <!-- Actions -->
          <td class="px-4 py-2 d-flex flex-row gap-2">
            <v-btn color="warning" size="x-small" @click="openEditCustomerDialog(item)">
              แก้ไข
            </v-btn>
            <v-btn color="error" size="x-small" @click="deleteCustomer(item)">
              ลบ
            </v-btn>
          </td>
        </tr>

        <!-- ส่วน Expand (รายละเอียดเพิ่มเติม) -->
        <!-- ต้อง colspan="7" ตามจำนวน columns รวมที่เรามี -->
        <tr v-if="expandedRows[item.id]" class="bg-grey-lighten-5">
          <td colspan="7" class="p-4">
            <!-- Rewards (mockup) -->
            <div class="mb-2">
              <strong>รางวัลที่ได้รับ:</strong>
              <ul style="margin: 0; padding-left: 1.2rem;">
                <li
                  v-for="(reward, idx) in item.rewards"
                  :key="idx"
                  style="margin-bottom: 0.5rem;"
                >
                  <strong>{{ reward.rewardName || 'No Reward' }}</strong>
                  <span v-if="reward.date" style="color: #666;">
                    ({{ reward.date }})
                  </span>
                  <div v-if="reward.couponName" style="font-size: 0.9rem;">
                    <em>คูปอง: {{ reward.couponName }}</em>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Note -->
            <div>
              <strong>หมายเหตุ:</strong>
              <span>{{ item.note || '-' }}</span>
            </div>
          </td>
        </tr>
      </template>
    </v-data-table>

    <!-- ======= Dialog เพิ่มลูกค้า ======= -->
    <v-dialog v-model="showAddCustomerDialog" max-width="600px">
      <v-card>
        <v-card-title>เพิ่มลูกค้าใหม่</v-card-title>
        <v-card-text>
          <v-text-field v-model="newCustomer.name" label="Name" class="mb-3" />
          <v-text-field v-model="newCustomer.phone" label="Phone" class="mb-3" />
          <v-text-field v-model="newCustomer.email" label="Email" class="mb-3" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="grey" @click="showAddCustomerDialog = false">ยกเลิก</v-btn>
          <v-btn color="primary" @click="confirmAddCustomer">บันทึก</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- ======= Dialog แก้ไขข้อมูลลูกค้า ======= -->
    <v-dialog v-model="showEditCustomerDialog" max-width="600px">
      <v-card>
        <v-card-title>แก้ไขข้อมูลลูกค้า</v-card-title>
        <v-card-text>
          <v-text-field v-model="editCustomer.name" label="Name" class="mb-3" />
          <v-text-field v-model="editCustomer.phone" label="Phone" class="mb-3" />
          <v-text-field v-model="editCustomer.email" label="Email" class="mb-3" />
          <v-switch
            v-model="editCustomer.is_active"
            label="Active?"
            class="mt-4"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="grey" @click="showEditCustomerDialog = false">ยกเลิก</v-btn>
          <v-btn color="primary" @click="confirmEditCustomer">บันทึก</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'
import {
  VAvatar,
  VBtn,
  VCard,
  VCardActions,
  VCardText,
  VCardTitle,
  VChip,
  VDataTable,
  VDialog,
  VIcon,
  VSelect,
  VSpacer,
  VSwitch,
  VTextField
} from 'vuetify/components'

// State
const customers = ref([])

// Search / filter
const searchTerm = ref('')
const selectedActive = ref('')

// Expanded rows
const expandedRows = ref({})

// Dialog
const showAddCustomerDialog = ref(false)
const showEditCustomerDialog = ref(false)

// Form model
const newCustomer = ref({
  name: '',
  phone: '',
  email: '',
})
const editCustomer = ref({
  id: null,
  name: '',
  phone: '',
  email: '',
  is_active: true,
})

// Table Headers (เพิ่มคอลัมน์วันที่เล่นกิจกรรม)
const headers = [
  { title: 'ลูกค้า', key: 'name', sortable: false },
  { title: 'Phone', key: 'phone' },
  { title: 'Email', key: 'email' },
  { title: 'วันที่เล่นกิจกรรม', key: 'activity_date', sortable: false },
  { title: 'คูปอง', key: 'coupons', sortable: false },
  { title: 'Active?', key: 'is_active', sortable: false },
  { title: 'Actions', key: 'actions', sortable: false },
]

const activeOptions = ['Active', 'Inactive']

// Lifecycle
onMounted(async () => {
  await fetchCustomers()
})

// Methods
async function fetchCustomers() {
  try {
    // ตัวอย่าง mock เรียก API จริง หรือจะ mock data เองก็ได้
    // const res = await axios.get('/api/customers')
    // customers.value = res.data

    // ---- MOCK DATA สำหรับเดโม ----
    const mockData = [
      {
        id: 1,
        name: 'คุณสมชาย ใจดี',
        nick_name: 'ชาย',
        phone: '089-123-4567',
        email: 'somchai@example.com',
        avatar: 'https://via.placeholder.com/40?text=SM',
        // วันที่เล่นกิจกรรม
        activity_date: '2025-04-10',
        is_active: true,
        // คูปอง ปรับให้เป็น object { name, used } จะได้กด "ใช้" ได้
        coupons: [
          { name: 'ส่วนลด 10%', used: false },
          { name: 'Free Shipping', used: false },
        ],
        rewards: [
          { rewardName: 'Gift Box', date: '2025-04-01', couponName: 'WELCOME2025' },
          { rewardName: 'Water Bottle', date: '2025-04-10' },
        ],
        note: 'ลูกค้า VIP'
      },
      {
        id: 2,
        name: 'คุณดวงใจ ใจดี',
        nick_name: 'ใจ',
        phone: '086-000-2222',
        email: 'duangjai@example.com',
        avatar: '',
        activity_date: '2025-04-18',
        is_active: false,
        coupons: [
          { name: 'ส่วนลด 5%', used: false },
        ],
        rewards: [
          { rewardName: 'T-Shirt', date: '2025-03-30', couponName: 'HELLO' },
        ],
        note: ''
      },
      {
        id: 3,
        name: 'คุณพิสิษฐ์ พิพัฒน์',
        nick_name: 'สิษฐ์',
        phone: '085-987-6543',
        email: 'pisit@example.com',
        avatar: '',
        activity_date: '2025-04-20',
        is_active: true,
        coupons: [
          { name: 'Free Shipping', used: true },
        ],
        rewards: [],
        note: 'ชอบสินค้าแฟชั่น'
      },
    ]
    customers.value = mockData
  } catch (err) {
    console.error(err)
    alert('ไม่สามารถโหลดข้อมูล customers ได้')
  }
}

// ฟิลเตอร์
function onSearch() {
  // trigger computed filter
}
const filteredCustomers = computed(() => {
  const term = (searchTerm.value || '').toLowerCase().trim()
  const isActiveFilter = selectedActive.value

  return customers.value.filter(customer => {
    // Filter by is_active
    if (isActiveFilter) {
      const isActive = customer.is_active ? 'Active' : 'Inactive'
      if (isActive !== isActiveFilter) {
        return false
      }
    }
    // Filter by search
    const phoneMatched = (customer.phone || '').toLowerCase().includes(term)
    const nameMatched = (customer.name || '').toLowerCase().includes(term)
    return phoneMatched || nameMatched
  })
})

// Expand
function toggleExpand(customer) {
  expandedRows.value[customer.id] = !expandedRows.value[customer.id]
}

// กดปุ่มใช้คูปอง
function useCoupon(customer, coupon) {
  if (!coupon.used) {
    if (confirm(`ยืนยันใช้คูปอง "${coupon.name}" ของ "${customer.name}" หรือไม่?`)) {
      // mock การใช้คูปอง
      coupon.used = true
      // ถ้ามี API จริง อาจ call API เช่น:
      // await axios.post(`/api/use-coupon`, { customerId: customer.id, couponName: coupon.name })
    }
  }
}

// Add customer
function openAddCustomerDialog() {
  newCustomer.value = { name: '', phone: '', email: '' }
  showAddCustomerDialog.value = true
}
async function confirmAddCustomer() {
  if (!newCustomer.value.name || !newCustomer.value.phone) {
    alert('กรุณากรอก Name, Phone อย่างน้อย')
    return
  }
  try {
    // เรียก API POST /api/customers (หรือ mock)
    // const payload = { ...newCustomer.value, is_active: true, activity_date: '...' }
    // await axios.post('/api/customers', payload)

    // MOCK เพิ่มลงใน local array
    const newId = customers.value.length + 1
    const newData = {
      id: newId,
      name: newCustomer.value.name,
      phone: newCustomer.value.phone,
      email: newCustomer.value.email,
      activity_date: '2025-04-19',
      is_active: true,
      coupons: [],
      rewards: [],
      note: ''
    }
    customers.value.push(newData)

    alert('เพิ่มลูกค้าใหม่สำเร็จ (mock)')
    showAddCustomerDialog.value = false
  } catch (err) {
    console.error(err)
    alert('ไม่สามารถเพิ่มลูกค้าได้')
  }
}

// Edit customer
function openEditCustomerDialog(customer) {
  editCustomer.value = { ...customer }
  showEditCustomerDialog.value = true
}
async function confirmEditCustomer() {
  if (!editCustomer.value.id) return
  try {
    // เรียก API PUT /api/customers/:id (หรือ mock)
    // await axios.put(`/api/customers/${editCustomer.value.id}`, editCustomer.value)

    // MOCK แก้ไขใน local array
    const targetIndex = customers.value.findIndex(c => c.id === editCustomer.value.id)
    if (targetIndex !== -1) {
      customers.value[targetIndex] = { ...editCustomer.value }
    }

    alert('แก้ไขข้อมูลลูกค้าสำเร็จ (mock)')
    showEditCustomerDialog.value = false
  } catch (err) {
    console.error(err)
    alert('ไม่สามารถแก้ไขข้อมูลลูกค้าได้')
  }
}

// Delete customer
async function deleteCustomer(customer) {
  if (!confirm(`ยืนยันลบลูกค้า "${customer.name}" ?`)) return
  try {
    // เรียก API DELETE /api/customers/:id (หรือ mock)
    // await axios.delete(`/api/customers/${customer.id}`)

    // MOCK ลบใน local array
    customers.value = customers.value.filter(c => c.id !== customer.id)

    alert('ลบลูกค้าสำเร็จ (mock)')
  } catch (err) {
    console.error(err)
    alert('ไม่สามารถลบข้อมูลลูกค้าได้')
  }
}
</script>

<style scoped>
.bg-grey-lighten-5 {
  background-color: #f9f9f9 !important;
}
</style>
