// เลือกกำหนดสีหลัก (primary) กับสีพื้นหลัง (background) ที่ต้องการ
export const staticPrimaryColor = '#dbab61'
export const staticPrimaryDarkenColor = '#c79a55' // หรือเลือกโค้ดที่ใกล้เคียงให้เข้มขึ้นเล็กน้อย

export const themes = {
  light: {
    dark: false,
    colors: {
      // ตั้งค่าสีหลัก (Primary)
      'primary': staticPrimaryColor,           // #dbab61
      'on-primary': '#fff',
      'primary-darken-1': staticPrimaryDarkenColor,

      // ถ้าอยากให้ "พื้นหลัง" ใช้โทนสว่างที่ค่อนข้างใกล้เคียงสีเดิม ก็ยังคงได้
      // แต่ถ้าต้องการให้ดูกลมกลืนขึ้น อาจใช้สีครีม/น้ำตาลอ่อนแทน
      'background': '#f5f5f9',
      'on-background': '#23303d',             // ใช้สีกรมเข้มเป็นสีตัวอักษรหลัก
      'surface': '#fff',
      'on-surface': '#23303d',

      // ตัวอย่าง: เปลี่ยน secondary ให้ใช้สีกรมเข้ม
      'secondary': '#23303d',
      'on-secondary': '#fff',

      // สีอื่น ๆ ที่เหลือจะคงของเดิม หรือปรับตามชอบก็ได้
      'secondary-darken-1': '#1d2935',
      'secondary-light': '#405264',

      'success': '#71DD37',
      'on-success': '#fff',
      'info': '#03C3EC',
      'on-info': '#fff',
      'warning': '#FFAB00',
      'on-warning': '#fff',
      'error': '#FF3E1D',
      'on-error': '#fff',

      // ตัวอย่างหากต้องการแทนสีเทาต่าง ๆ หรือปล่อยไว้ตามเดิม
      'grey-50': '#FAFAFA',
      'grey-100': '#F5F5F5',
      // ...
    },
    variables: {
      // ตัวอย่าง: ปรับ overlay-scrim และ tooltip ให้ใช้สีกรม
      'overlay-scrim-background': '#23303d',
      'tooltip-background': '#23303d',
      // ส่วนค่าความโปร่งใสต่าง ๆ ยังใช้ค่าที่ตั้งไว้ได้
      'overlay-scrim-opacity': 0.5,
      // ...
    },
  },
  dark: {
    dark: true,
    colors: {
      // ในโหมด Dark อาจใช้สีกรม (#23303d) เป็น background
      'background': '#23303d',
      'on-background': '#fff',            // ให้ข้อความอ่านง่ายบนพื้นหลังเข้ม
      'surface': '#2B2C40',               // หรือจะเปลี่ยนเป็น #1C1C28 / #2B2C40 แล้วแต่ดีไซน์
      'on-surface': '#fff',

      // ยังคง primary ให้เป็นสีทอง #dbab61
      'primary': staticPrimaryColor,
      'on-primary': '#23303d',
      'primary-darken-1': staticPrimaryDarkenColor,

      // secondary อาจให้เป็นโทนอื่น หรือกลับมาใช้สีกรมเข้มเพิ่มความหลากหลาย
      'secondary': '#8592A3',
      'on-secondary': '#fff',

      // ส่วนอื่น ๆ ที่เหลือจะคงเดิม หรือปรับตามชอบได้
      'success': '#71DD37',
      'on-success': '#fff',
      'info': '#03C3EC',
      'on-info': '#fff',
      'warning': '#FFAB00',
      'on-warning': '#fff',
      'error': '#FF3E1D',
      'on-error': '#fff',

      // ตัวอย่างปรับสีเทาให้ออกหม่นขึ้น ถ้าต้องการ
      'grey-50': '#26293A',
      'grey-100': '#2F3349',
      // ...
    },
    variables: {
      'overlay-scrim-background': '#1D1D2A',
      'tooltip-background': '#E6E6F1',
      'overlay-scrim-opacity': 0.6,
      // ...
    },
  },
}

export default themes
