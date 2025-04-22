<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar4 from '@images/avatars/avatar-4.png'
import avatar5 from '@images/avatars/avatar-5.png'
import avatar6 from '@images/avatars/avatar-6.png'
import avatar7 from '@images/avatars/avatar-7.png'

// 👉 เอาออก เพราะเราไม่ต้องใช้แล้ว:
// import avatar8 from '@images/avatars/avatar-8.png'
// import avatar9 from '@images/avatars/avatar-9.png'
// import avatar10 from '@images/avatars/avatar-10.png'
// import girlUsingLaptop from '@images/pages/girl-using-laptop.png'

// 👉 เก็บเฉพาะ 2 roles: Administrator / Manager
const roles = ref([
  {
    role: 'Administrator',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
    ],
    details: {
      name: 'Administrator',
      permissions: [
        {
          name: 'User Management',
          read: true,
          write: true,
          create: true,
        },
        {
          name: 'Disputes Management',
          read: true,
          write: true,
          create: true,
        },
        {
          name: 'API Control',
          read: true,
          write: true,
          create: true,
        },
      ],
    },
  },
  {
    role: 'Manager',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
      avatar5,
      avatar6,
      avatar7,
    ],
    details: {
      name: 'Manager',
      permissions: [
        {
          name: 'Reporting',
          read: true,
          write: true,
          create: false,
        },
        {
          name: 'Payroll',
          read: true,
          write: true,
          create: true,
        },
        {
          name: 'User Management',
          read: true,
          write: true,
          create: true,
        },
      ],
    },
  },
])

const isRoleDialogVisible = ref(false)
const roleDetail = ref()
// 👉 ไม่ใช้ Add New Role เลย ก็ไม่ต้องมี isAddRoleDialogVisible / girlUsingLaptop

const editPermission = value => {
  isRoleDialogVisible.value = true
  roleDetail.value = value
}
</script>

<template>
  <VRow>
    <!-- 👉 แสดงเฉพาะ 2 Roles ที่เหลือ -->
    <VCol
      v-for="item in roles"
      :key="item.role"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard>
        <VCardText class="d-flex align-center pb-4">
          <div class="text-body-1">
            Total {{ item.users.length }} users
          </div>

          <VSpacer />

         
        </VCardText>

        <VCardText>
          <div class="d-flex justify-space-between align-center">
            <div>
              <h5 class="text-h5 mb-1">
                {{ item.role }}
              </h5>
              <div class="d-flex align-center">
                <a
                  href="javascript:void(0)"
                  @click="editPermission(item.details)"
                >
                  Edit Role
                </a>
              </div>
            </div>
            <IconBtn class="align-self-end">
              <VIcon
                icon="bx-copy"
                class="text-disabled"
              />
            </IconBtn>
          </div>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 ลบ VCol Add New Role ออกไปเลย -->
  </VRow>

  <!-- dialog แก้ไข Permission -->
  <AddEditRoleDialog
    v-model:is-dialog-visible="isRoleDialogVisible"
    v-model:role-permissions="roleDetail"
  />
</template>
