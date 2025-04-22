<script setup>
import AddNewUserDrawer from '@/views/apps/user/list/AddNewUserDrawer.vue'
import axios from 'axios'
import { onMounted, ref } from 'vue'

// ถ้าต้องการส่ง Cookie ข้ามโดเมน (Session-Based Sanctum)
axios.defaults.withCredentials = true

// ----------------------------------------------------
// 1) ตัวแปร/State
// ----------------------------------------------------
const isAddNewUserDrawerVisible = ref(false)

// ฟิลด์ค้นหา
const searchQuery = ref('')
const selectedRole = ref(null)
// **เอา plan, status ออก เพราะไม่ต้องใช้แล้ว**
// const selectedPlan = ref(null)
// const selectedStatus = ref(null)

// ตัวเลือก Role (เหลือ Admin / Sell)
const roles = [
  { title: 'Admin', value: 'admin' },
  { title: 'Sell', value: 'sell' },
]

// DataTable
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref(null)
const orderBy = ref(null)
const selectedRows = ref([])

// เก็บ Users + total
const users = ref([])
const totalUsers = ref(0)

// ----------------------------------------------------
// 2) ฟังก์ชัน DataTable
// ----------------------------------------------------
const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
  fetchUsers()
}

// ฟังก์ชันสร้างชื่อย่อ (Avatar)
const avatarText = username => {
  if (!username) return ''
  return username
    .match(/\b\w/g)
    ?.join('')
    ?.toUpperCase() || ''
}

// ----------------------------------------------------
// 3) เรียก API
// ----------------------------------------------------
/** โหลด Users จาก Backend */
const fetchUsers = async () => {
  try {
    // เรียก /api/apps/users (สมมติว่าใน Controller ไม่ต้องการ plan/status)
    const resp = await axios.get('/api/apps/users', {
      params: {
        q: searchQuery.value,
        role: selectedRole.value,
        itemsPerPage: itemsPerPage.value,
        page: page.value,
        sortBy: sortBy.value,
        orderBy: orderBy.value,
      },
    })
    users.value = resp.data.users
    totalUsers.value = resp.data.totalUsers
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

/** ฟังก์ชันสร้าง User ใหม่ */
const addNewUser = async userData => {
  try {
    await axios.post('/api/apps/users', userData)
    fetchUsers()
  } catch (error) {
    console.error('Error creating user:', error)
  }
}

/** ฟังก์ชันลบ User */
const deleteUser = async id => {
  try {
    await axios.delete(`/api/apps/users/${id}`)
    const index = selectedRows.value.findIndex(row => row === id)
    if (index !== -1) selectedRows.value.splice(index, 1)
    fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
  }
}

/** ฟังก์ชันแก้ไข User */
const editUser = user => {
  console.log('Edit user =>', user)
  // TODO: นำไปเปิด Drawer แก้ไข หรือหน้าแก้ไขได้ตามต้องการ
}

// ----------------------------------------------------
// 4) Lifecycle: onMounted
// ----------------------------------------------------
onMounted(async () => {
  try {
    // เรียก /sanctum/csrf-cookie ก่อน (สำหรับ Sanctum)
    await axios.get('/sanctum/csrf-cookie')
    // แล้วค่อยโหลด Users
    fetchUsers()
  } catch (error) {
    console.error('Error in onMounted:', error)
  }
})

// ----------------------------------------------------
// 5) กำหนด Headers (Username, Email, Role, Actions)
// ----------------------------------------------------
const headers = [
  { title: 'Username', key: 'username' },
  { title: 'Email', key: 'email' },
  { title: 'Role', key: 'role' },
  { title: 'Actions', key: 'actions', sortable: false },
]

// ----------------------------------------------------
// ฟังก์ชันช่วยแสดง Role icon/color
// ----------------------------------------------------
const resolveUserRoleVariant = role => {
  const roleLowerCase = role?.toLowerCase() || ''
  if (roleLowerCase === 'admin') {
    return { color: 'primary', icon: 'bx-crown' }
  } else if (roleLowerCase === 'sell') {
    // หรือจะเปลี่ยน color / icon ตามต้องการ
    return { color: 'success', icon: 'bx-user' }
  }
  return { color: 'info', icon: 'bx-user' }
}
</script>

<template>
  <section>
    <VCard>
      <VCardText class="d-flex flex-md-row flex-column justify-space-between gap-4">
        <!-- ปุ่ม Create New User -->
        <VBtn
          color="primary"
          class="ms-auto"
          @click="isAddNewUserDrawerVisible = true"
        >
          <VIcon icon="bx-plus" class="me-1" />
          Create New User
        </VBtn>

        <!-- Items per Page -->
        <AppSelect
          :model-value="itemsPerPage"
          :items="[ { value: 10, title: '10' }, { value: 25, title: '25' }, { value: 50, title: '50' }, { value: 100, title: '100' }, { value: -1, title: 'All' } ]"
          style="inline-size: 5.5rem;"
          @update:model-value="itemsPerPage = parseInt($event, 10); fetchUsers()"
        />

        <!-- Search / Role -->
        <div class="d-flex align-start flex-column flex-sm-row flex-wrap gap-4">
          <!-- Search -->
          <div style="inline-size: 15.625rem;">
            <AppTextField
              v-model="searchQuery"
              placeholder="Search User"
              @keyup.enter="fetchUsers"
            />
          </div>

          <!-- Role -->
          <div style="inline-size: 9.375rem;">
            <AppSelect
              v-model="selectedRole"
              placeholder="Select Role"
              :items="roles"
              clearable
              clear-icon="bx-x"
              @update:model-value="fetchUsers()"
            />
          </div>
        </div>
      </VCardText>

      <VDivider />

      <!-- ตาราง Users -->
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:model-value="selectedRows"
        v-model:page="page"
        :items-per-page-options="[ { value: 10, title: '10' }, { value: 20, title: '20' }, { value: 50, title: '50' }, { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' } ]"
        :items="users"
        :items-length="totalUsers"
        :headers="headers"
        class="text-no-wrap"
        show-select
        @update:options="updateOptions"
      >
        <!-- คอลัมน์ Username -->
        <template #item.username="{ item }">
          <div class="d-flex align-center gap-x-4">
            <VAvatar
              size="34"
              :variant="!item.avatar ? 'tonal' : undefined"
              :color="!item.avatar ? resolveUserRoleVariant(item.role).color : undefined"
            >
              <VImg v-if="item.avatar" :src="item.avatar" />
              <span v-else>{{ avatarText(item.name) }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <h6 class="text-base">{{ item.name }}</h6>
            </div>
          </div>
        </template>

        <!-- คอลัมน์ Email -->
        <template #item.email="{ item }">
          {{ item.email }}
        </template>

        <!-- คอลัมน์ Role -->
        <template #item.role="{ item }">
          <div class="d-flex align-center gap-x-2">
            <VIcon
              :size="20"
              :icon="resolveUserRoleVariant(item.role).icon"
              :color="resolveUserRoleVariant(item.role).color"
            />
            <div class="text-capitalize text-high-emphasis text-body-1">
              {{ item.role }}
            </div>
          </div>
        </template>

        <!-- คอลัมน์ Actions -->
        <template #item.actions="{ item }">
          <!-- ปุ่มลบ -->
          <IconBtn @click="deleteUser(item.id)">
            <VIcon icon="bx-trash" />
          </IconBtn>

          <!-- ปุ่มแก้ไข -->
          <IconBtn @click="editUser(item)">
            <VIcon icon="bx-pencil" />
          </IconBtn>
        </template>

        <!-- Pagination ด้านล่าง -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="totalUsers"
            @update:page="fetchUsers()"
          />
        </template>
      </VDataTableServer>
    </VCard>

    <!-- Drawer สร้าง User ใหม่ -->
    <AddNewUserDrawer
      :isDrawerOpen="isAddNewUserDrawerVisible"
      @update:isDrawerOpen="val => (isAddNewUserDrawerVisible = val)"
      @user-data="addNewUser"
    />
  </section>
</template>

<style lang="scss">
.text-capitalize {
  text-transform: capitalize;
}
</style>
