<template>
  <div class="pdpa-page" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <div class="pdpa-content-container">
      <img :src="owlLogo" alt="Owl Logo" class="owl-logo-centered" />

      <div class="white-box">
        <div class="pdpa-text-box">
          <p>
            นโยบายการคุ้มครองข้อมูลส่วนบุคคล (PDPA):<br /><br />
            1) เราเก็บรวบรวมข้อมูลส่วนบุคคล เช่น ชื่อ นามสกุล รูปโปรไฟล์ และคำตอบจาก Quiz เพื่อใช้ในการวิเคราะห์ ให้บริการแนะนำผลิตภัณฑ์ รวมถึงอาจใช้ข้อมูลเพื่อติดต่อกลับเมื่อจำเป็น<br /><br />
            2) ข้อมูลของคุณจะถูกเก็บไว้อย่างปลอดภัย โดยเราจะไม่ส่งต่อข้อมูลนี้ให้บุคคลภายนอก ยกเว้นในกรณีที่มีกฎหมายบังคับ<br /><br />
            3) คุณสามารถติดต่อขอแก้ไข หรือลบข้อมูลได้ทุกเมื่อ โดยส่งคำร้องมาทางช่องทางที่เรากำหนด<br /><br />
            4) การเข้าถึงข้อมูลของคุณจะถูกจำกัดเฉพาะผู้ที่ได้รับอนุญาตเท่านั้น เพื่อป้องกันการเข้าถึงโดยไม่ได้รับอนุญาต<br /><br />
            5) เราอาจใช้เทคโนโลยีในการเก็บข้อมูลสถิติและพฤติกรรมการใช้งานเว็บไซต์หรือแอปพลิเคชัน เพื่อพัฒนาการให้บริการที่ดียิ่งขึ้น<br /><br />
            6) ในกรณีที่มีการเชื่อมต่อหรือใช้งานเว็บไซต์หรือบริการภายนอก นโยบายความเป็นส่วนตัวของบริการดังกล่าวจะไม่เกี่ยวข้องกับเรา โปรดตรวจสอบนโยบายของบริการเหล่านั้นโดยตรง<br /><br />
            7) เราขอสงวนสิทธิ์ในการเปลี่ยนแปลงหรือแก้ไขนโยบายนี้โดยไม่ต้องแจ้งให้ทราบล่วงหน้า คุณสามารถตรวจสอบนโยบายล่าสุดได้จากช่องทางที่เราแจ้งไว้เสมอ<br /><br />
            8) หากคุณมีข้อสงสัยหรือต้องการสอบถามเพิ่มเติมเกี่ยวกับนโยบายนี้ โปรดติดต่อเราผ่านช่องทางที่ระบุไว้<br /><br />
            การกด “ยอมรับเงื่อนไข” หมายถึง คุณได้อ่านและยอมรับนโยบายคุ้มครองข้อมูลส่วนบุคคลฉบับนี้แล้ว
          </p>
        </div>

        <div class="pdpa-action-section">
          <div class="checkbox-wrapper">
            <input type="checkbox" id="acceptPdpa" v-model="isAccepted" />
            <label for="acceptPdpa">ยอมรับเงื่อนไขข้อตกลง</label>
          </div>

          <v-btn block :disabled="!isAccepted" class="accept-button" @click="handleAcceptPdpa">
            ยอมรับเงื่อนไข
          </v-btn>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import backgroundImage from '@images/BB/BG.jpg';
import owlLogo from '@images/BB/Logo1-01.png';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})
const isAccepted = ref(false);
const router = useRouter();

function handleAcceptPdpa() {
  router.push('/singin');
}
</script>

<style scoped>
.pdpa-page {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background-position: center;
  background-size: cover;
  block-size: 100vh;
}

.pdpa-content-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  inline-size: 100%;
  max-inline-size: 600px;
}

.owl-logo-centered {
  block-size: 100px;
  inline-size: 100px;
  margin-block-end: 16px;
}

.white-box {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  padding: 16px;
  border-radius: 24px;
  background: #fff;
  inline-size: 100%;
}

.pdpa-text-box {
  flex-grow: 1;
  padding: 16px;
  border-radius: 8px;
  background-color: #f7f7f7;
  color: #333;
  font-size: 14px;
  margin-block-end: 16px;
  max-block-size: 400px;
  overflow-y: auto;
}

.accept-button {
  border-radius: 8px;
  background-color: #2c2f36 !important;
  color: #fff;
  font-weight: 600;
}

.checkbox-wrapper {
  margin-block-end: 16px;
}
</style>
