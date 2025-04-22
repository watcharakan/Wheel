APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:8Hs1jTTyDlMxah4QU9DyrJf/t+H+7xZiWQl45PYa6DM=   # ใช้คีย์เดิมหรือสั่ง php artisan key:generate ในเซิร์ฟเวอร์จริงก็ได้
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://quiz.brand-better.com  # แก้ไขตามโดเมนจริงของคุณ

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_LEVEL=info

#-------------------------------------------
# ตั้งค่าฐานข้อมูล MySQL
#-------------------------------------------
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=card_quiz
DB_USERNAME=card_quiz
DB_PASSWORD="T6q@2c0w3"

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

#-------------------------------------------
# หากยังไม่ได้ตั้งค่าอีเมลจริง สามารถใช้ mailhog / log หรือ SMTP จริงในภายหลัง
#-------------------------------------------
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

#-------------------------------------------
# อื่น ๆ ตามต้องการ (Redis, AWS, ฯลฯ)
#-------------------------------------------
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

VITE_APP_NAME="${APP_NAME}"
