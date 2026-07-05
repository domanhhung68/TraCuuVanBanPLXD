# 📧 Hướng dẫn Cấu hình Mail cho Development & Production

## 🔧 Development Environment

### Option 1: Log Driver (Recommended cho Dev)
Email sẽ được ghi vào log file thay vì gửi thực.

Cập nhật `.env`:
```
MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Demo App"
```

Xem email trong file:
```
storage/logs/laravel.log
```

### Option 2: Array Driver
Email sẽ không được gửi, chỉ lưu trong memory.

```
MAIL_MAILER=array
```

---

## 🌐 Production Environment

### Option A: Gmail (Google Account)
1. Bật **2-Factor Authentication** trên Gmail
2. Tạo **App Password**: https://myaccount.google.com/apppasswords
3. Cập nhật `.env.production`:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="Your App Name"
```

### Option B: Mailtrap (Testing)
1. Đăng ký: https://mailtrap.io
2. Tạo Inbox mới
3. Lấy credentials từ SMTP Settings
4. Cập nhật `.env.production`:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Your App Name"
```

### Option C: SendGrid
1. Đăng ký: https://sendgrid.com
2. Tạo API Key
3. Cập nhật `.env.production`:

```
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-api-key
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Your App Name"
```

### Option D: Mailgun
1. Đăng ký: https://www.mailgun.com
2. Lấy Domain và API Key
3. Cập nhật `.env.production`:

```
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-api-key
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Your App Name"
```

### Option E: AWS SES
1. Cấu hình AWS credentials
2. Xác minh email address
3. Cập nhật `.env.production`:

```
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Your App Name"
```

---

## 📋 Tóm tắt Drivers

| Driver | Dev | Prod | Chi phí | Tốc độ |
|--------|-----|------|---------|---------|
| log | ✅ | ❌ | Miễn phí | N/A |
| array | ✅ | ❌ | Miễn phí | N/A |
| smtp | ⚠️ | ✅ | Phụ thuộc | Nhanh |
| Gmail | ⚠️ | ✅ | Miễn phí | Nhanh |
| Mailtrap | ✅ | ⚠️ | Miễn phí/Trả phí | Nhanh |
| SendGrid | ❌ | ✅ | Trả phí | Rất nhanh |
| Mailgun | ❌ | ✅ | Trả phí | Rất nhanh |
| AWS SES | ❌ | ✅ | Trả phí | Rất nhanh |

---

## ✅ Test Email

Chạy lệnh Tinker để test:

```bash
php artisan tinker

$user = App\Models\User::first();
$user->notify(new App\Notifications\ResetPasswordNotification('token', $user->email));

exit
```

Sau đó:
- **Log Driver**: Xem `storage/logs/laravel.log`
- **SMTP Driver**: Email sẽ được gửi thực tế

---

## 🔒 Bảo mật

1. **Không commit credentials**: Thêm vào `.gitignore`:
   ```
   .env
   .env.production
   ```

2. **Sử dụng Environment Variables**: Trên server production, đặt biến môi trường thay vì file `.env`

3. **Xác thực Domain**: Nếu dùng SMTP, hãy xác thực domain của bạn

4. **Rate Limiting**: Đặt giới hạn email để tránh spam

---

## 📝 Recommended Setup

**Development** → `MAIL_MAILER=log`
**Staging** → `MAIL_MAILER=smtp` (Mailtrap)
**Production** → `MAIL_MAILER=sendgrid` hoặc `mailgun` (scalable)
