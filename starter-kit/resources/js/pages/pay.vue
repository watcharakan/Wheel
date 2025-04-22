<template>
  <div
    class="payment-page"
    :style="{
      backgroundImage: `url(${backgroundImage})`,
      backgroundPosition: 'center',
      backgroundSize: 'cover',
      backgroundRepeat: 'no-repeat',
    }"
  >
    <!-- Header (Logo + หัวข้อ Payment) -->
    <div class="header">
      <img :src="logo" alt="BeBetter Logo" class="logo" />
      <h1 class="title">Payment</h1>
    </div>

    <!-- คอนเทนต์กึ่งกลาง -->
    <div class="content">
      <div class="card-container">
        <!-- ถ้ามี packageData ให้แสดงข้อมูลแพ็กเกจ -->
        <div v-if="packageData">
          <p>
            <strong>Package Selected:</strong><br />
            {{ packageData.package_name }} ({{ packageData.coins }} coins)
          </p>
        </div>
        <!-- ถ้าไม่เจอ (หรือกำลังโหลด) ก็อาจแสดง Loading / Error -->
        <div v-else>
          <p>Loading package data...</p>
        </div>

        <!-- สรุปรายการ -->
        <div class="summary">
          <div class="row">
            <span>Subtotal</span>
            <!-- แสดง subtotal -->
            <span>฿{{ subtotal }}</span>
          </div>
          <div class="row">
            <span>Discount</span>
            <!-- แสดง discount -->
            <span>฿{{ discount }}</span>
          </div>
          <hr />
          <div class="row total">
            <span>TOTAL</span>
            <!-- แสดง total -->
            <span>฿{{ total }}</span>
          </div>
        </div>

        <!-- Promotion Code -->
        <div class="promotion-code">
          <label class="promotion-label">PROMOTION CODE</label>
          <div class="promo-input-group">
            <v-text-field
              v-model="promoCode"
              class="promo-input"
              outlined
              hide-details
            />
            <v-btn class="apply-button" @click="applyPromo">APPLY</v-btn>
          </div>
        </div>
        <hr />

        <!-- เลือกวิธีจ่ายเงิน -->
        <div class="payment-method">
          <label class="payment-label">CHOOSE & PAYMENT METHOD</label>
          <v-radio-group v-model="selectedPayment" class="payment-options">
            <v-radio label="QR Code" value="qr" color="#d4af37" />
          </v-radio-group>
        </div>

        <!-- ปุ่ม CONFIRM -->
        <div class="final-total">
          <span class="final-amount">Total ฿{{ total }}</span>
          <v-btn class="confirm-button" @click="handleConfirm">CONFIRM</v-btn>
        </div>

        <!-- ส่วนแสดง QR Code ที่ได้จาก Stripe หรือระบบอื่น -->
        <div v-if="promptpayQRUrl" class="qr-section">
          <p>Scan this QR code with PromptPay:</p>
          <img :src="promptpayQRUrl" alt="PromptPay QR Code" class="qr-image" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import backgroundImage from '@images/BB/BG.jpg'
import logo from '@images/BB/Logo2-01.png'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

definePage({
  meta: {
    layout: 'blank',
    public: true,
  },
})

// Router & Route
const route = useRoute()
const router = useRouter()

// เก็บข้อมูล package ที่ดึงจากฐานข้อมูล
const packageData = ref(null)

// สำหรับคำนวณ
const subtotal = ref(0)
const discount = ref(0)
const total    = ref(0)

// เก็บโค้ดโปรโมชัน
const promoCode = ref('')

// เลือกวิธีชำระเงิน
const selectedPayment = ref('qr')

// เก็บ URL PromptPay QR
const promptpayQRUrl = ref('')

// เมื่อหน้า mount -> ดึง package_id จาก query -> เรียก API
onMounted(async () => {
  const packageId = route.query.package_id
  if (!packageId) {
    alert('No package selected.')
    router.push('/topup')
    return
  }

  try {
    // เรียก API ดึงข้อมูลแพ็กเกจ
    const response = await axios.get(`/api/packages/${packageId}`)
    packageData.value = response.data

    // ตั้งค่า subtotal ตามราคาใน DB
    subtotal.value = packageData.value.price
    discount.value = 0  // เริ่มต้น 0 หรือถ้ามี logic อื่นค่อยเติม
    total.value    = subtotal.value - discount.value

  } catch (err) {
    console.error(err)
    alert('Cannot fetch package data. Redirecting back...')
    router.push('/topup')
  }
})

function applyPromo() {
  console.log('Apply promo code:', promoCode.value)
  // หากมี logic คำนวณส่วนลดจาก promo code ก็ทำที่นี่
  // discount.value = ...
  // total.value = subtotal.value - discount.value
}

function toSatang(baht) {
  return baht * 100
}

async function handleConfirm() {
  // เช็คว่ามี packageData หรือไม่
  if (!packageData.value) {
    alert('Package data not loaded.')
    return
  }

  console.log('Confirm payment with:', {
    package_id: packageData.value.id,
    package_name: packageData.value.package_name,
    price: packageData.value.price,
    coins: packageData.value.coins,
    subtotal: subtotal.value,
    discount: discount.value,
    total: total.value,
    promoCode: promoCode.value,
    paymentMethod: selectedPayment.value
  })

  if (selectedPayment.value !== 'qr') {
    return
  }

  try {
    // เรียก API บนฝั่ง Laravel เพื่อสร้าง PaymentIntent
    // note: ส่งเฉพาะ package_id ไปให้ Back-end
    const res = await axios.post('/api/create-promptpay-intent', {
      package_id: packageData.value.id
    })

    const data = res.data
    if (data.error) {
      throw new Error(data.error)
    }

    // ถ้ามี PromptPay QR code ให้เอามาโชว์
    const paymentIntent = data.paymentIntent
    console.log('Created PaymentIntent:', paymentIntent)

    if (
      paymentIntent.next_action &&
      paymentIntent.next_action.promptpay_display_qr_code &&
      paymentIntent.next_action.promptpay_display_qr_code.image_url_png
    ) {
      promptpayQRUrl.value =
        paymentIntent.next_action.promptpay_display_qr_code.image_url_png
    }

  } catch (err) {
    console.error('Payment error:', err)
    alert(`Payment error: ${err.message}`)
  }
}
</script>

<style scoped>
.payment-page {
  position: relative;
  min-block-size: 100vh;
  padding-inline: 24px;
}

/* Header */
.header {
  position: absolute;
  display: flex;
  flex-direction: column;
  gap: 8px;
  inset-block-start: 24px;
  inset-inline-start: 24px;
}

.logo {
  inline-size: 150px;
}

.title {
  margin: 0;
  color: #2c2f36;
  font-size: 42px;
  font-weight: 700;
  margin-block-start: 60px;
}

/* Content */
.content {
  display: flex;
  align-items: center;
  justify-content: center;
  min-block-size: 100vh;
}

.card-container {
  border-radius: 12px;
  backdrop-filter: blur(4px);
  background-color: rgba(255, 255, 255, 22%);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%);
  inline-size: 100%;
  max-inline-size: 350px;
  padding-block: 32px;
  padding-inline: 24px;
}

/* Summary */
.summary {
  margin-block-end: 24px;
}

.row {
  display: flex;
  justify-content: space-between;
  color: #2c2f36;
  font-size: 1rem;
  margin-block: 8px;
}

.row.total {
  font-size: 1.2rem;
  font-weight: 700;
}

/* Promotion Code */
hr {
  border: none;
  border-block-start: 1px solid rgba(0, 0, 0, 10%);
  margin-block: 12px;
}

.promotion-code {
  margin-block-end: 24px;
}

.promotion-label {
  font-weight: 500;
  margin-block-end: 6px;
}

.promo-input-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.promo-input.v-text-field {
  flex: 1;
}

.apply-button.v-btn {
  background-color: #2c2f36 !important;
  color: #fff;
  font-weight: 600;
  text-transform: none;
}

/* Payment method */
.payment-method {
  margin-block-end: 24px;
}

.payment-label {
  font-weight: 500;
  margin-block-end: 6px;
}

.payment-options {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

/* Final total & Confirm */
.final-total {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-start: 12px;
}

.final-amount {
  color: #2c2f36 !important;
  font-size: 1.2rem;
  font-weight: 600;
}

.confirm-button.v-btn {
  background-color: #2c2f36 !important;
  color: #fff;
  font-weight: 600;
  text-transform: none;
}

/* QR Section */
.qr-section {
  margin-block-start: 24px;
  text-align: center;
}

.qr-image {
  margin-block-start: 12px;
  max-inline-size: 250px;
}
</style>
