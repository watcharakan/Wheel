<template>
  <div class="p-4">
    <!-- ส่วนแสดงข้อมูล Event -->
    <v-row class="mb-4" align="start">
      <v-col cols="12" md="8">
        <!-- ชื่อกิจกรรม -->
        <h2 class="mb-1">A2L Back To School 11 🎉</h2>
        <!-- รายละเอียดย่อย -->
        <p class="text-subtitle-1">A2L Back To School</p>
        <!-- ปุ่ม Event -->
          <v-btn
            variant="text"
            @click="goToEventDetail"
            class="px-0"
          >
            {{ t('event') }}
          </v-btn>
      </v-col>

      <!-- การ์ดสรุปจำนวนผู้เข้าร่วม / ของขวัญ -->
      <v-col cols="12" md="4">
        <v-row>
          <v-col cols="12" sm="6" md="12" class="mb-2">
            <v-card class="pa-2 d-flex flex-column align-center">
                <div class="text-h6 mb-2">{{ t('participantsTitle') }}</div>
              <div class="text-h5 d-flex align-center">
                <span>{{ participants }}</span>
                <span class="text-success text-subtitle-2 ml-2">+100%</span>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="12">
            <v-card class="pa-2 d-flex flex-column align-center">
                <div class="text-h6 mb-2">{{ t('giftsTitle') }}</div>
              <div class="text-h5 d-flex align-center">
                <span>{{ gifts }}</span>
                <span class="text-success text-subtitle-2 ml-2">+100%</span>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>

    <!-- ส่วนแสดงการกรอกข้อมูลผู้เข้าร่วมงาน และปุ่มสุ่มของรางวัล -->
    <v-row>
      <v-col cols="12" md="6">
        <v-card class="mb-4">
          <v-card-title class="text-h6">
            {{ t('participants') }}
          </v-card-title>
          <v-card-text>
              <v-text-field
                v-model="firstRowInput"
                :label="t('firstRowBarrier')"
                type="number"
              ></v-text-field>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="mb-4 d-flex flex-column align-center justify-center" style="min-height: 160px;">
          <v-card-title class="text-h6">{{ t('randomReward') }}</v-card-title>
          <v-card-text>
              <v-btn
                color="primary"
                @click="handleRandomGift"
              >
                {{ t('clickRandom') }}
              </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n'
export default {
  name: 'EventPage',
  setup() {
    const { t } = useI18n()
    return { t }
  },
  data() {
    return {
      participants: 61,
      gifts: 15,
      firstRowInput: 5, // ค่าตั้งต้นของแถวกั้นอันแรก
    }
  },
  methods: {
    goToEventDetail() {
      // ใส่ logic สำหรับไปหน้า Event หรือเปิด modal ได้ตามต้องการ
      alert('ไปยังรายละเอียด Event');
    },
    handleRandomGift() {
      // ใส่ฟังก์ชันสุ่มของรางวัลได้ตามต้องการ
      alert('สุ่มของรางวัลเรียบร้อย!');
    },
  },
}
</script>

<style scoped>
.text-subtitle-1 {
  font-size: 1rem;
  color: #6b6b6b;
}
</style>
