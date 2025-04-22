<script setup>
import { ref } from 'vue'

// รับ Props
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    default: false,
  },
})

// ประกาศ events ที่จะ emit
const emit = defineEmits(['update:isDrawerOpen', 'user-data'])

// ฟิลด์ในฟอร์ม
const fullName = ref('')
const email = ref('')
const password = ref('')
// กำหนด role default เป็น 'Sell' หรือ 'Admin' ตามต้องการ
const role = ref('Sell')

// เมื่อกดปุ่ม "Create"
const handleCreateUser = () => {
  const userData = {
    fullName: fullName.value,
    email: email.value,
    password: password.value,
    role: role.value,
  }
  // ส่งข้อมูลกลับไปให้ Parent
  emit('user-data', userData)

  // เคลียร์ฟอร์ม
  fullName.value = ''
  email.value = ''
  password.value = ''
  role.value = 'Sell'

  // ปิด Drawer
  emit('update:isDrawerOpen', false)
}
</script>

<template>
  <!-- 
    :model-value="props.isDrawerOpen" 
    @update:model-value -> emit('update:isDrawerOpen', val)
  -->
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    @update:model-value="val => emit('update:isDrawerOpen', val)"
    location="end"
    width="400"
  >
    <VCard>
      <VCardTitle>Create New User</VCardTitle>

      <VCardText>
        <AppTextField
          v-model="fullName"
          label="Full Name"
          required
        />
        <AppTextField
          v-model="email"
          label="Email"
          required
        />
        <AppTextField
          v-model="password"
          label="Password 6 หลักทำแจ้งเตือนด้วยถ้าใส่ไม่ถึง"
          type="password"
          required
        />
        <AppSelect
          v-model="role"
          label="Role"
          :items="[
            { title: 'Sell', value: 'Sell' },
            { title: 'Admin', value: 'Admin' }
          ]"
        />
      </VCardText>

      <VCardActions>
        <VBtn color="secondary" @click="emit('update:isDrawerOpen', false)">
          Cancel
        </VBtn>
        <VBtn color="primary" @click="handleCreateUser">
          Create
        </VBtn>
      </VCardActions>
    </VCard>
  </VNavigationDrawer>
</template>
