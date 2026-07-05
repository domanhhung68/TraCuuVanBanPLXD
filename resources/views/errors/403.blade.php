<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy cập bị từ chối</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .error-container {
            background: white;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
        }
        .error-code {
            font-size: 80px;
            color: #d32f2f;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }
        a {
            display: inline-block;
            padding: 10px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px;
        }
        a:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1>Truy cập bị từ chối</h1>
        <p>Bạn không có quyền truy cập vào trang này. Vui lòng kiểm tra vai trò của bạn hoặc liên hệ quản trị viên.</p>
        <a href="{{ route('home') }}">Về trang chủ</a>
        <a href="{{ route('logout') }}">Đăng xuất</a>
    </div>
</body>
</html>
