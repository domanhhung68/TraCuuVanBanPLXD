<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$law = new App\Models\Law();
$law->source_id = 999;
$law->title = 'Văn bản thử chức năng yêu thích';
$law->so_ky_hieu = 'TEST-001';
$law->loai_van_ban = 'Thông tư';
$law->tinh_trang_hieu_luc = 'Có hiệu lực';
$law->content_html = '<p>Đây là nội dung thử nghiệm</p>';
$law->save();
echo $law->id;
