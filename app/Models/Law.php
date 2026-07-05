<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    protected $table = 'laws';

    protected $fillable = [
        'source_id',
        'title',
        'so_ky_hieu',
        'loai_van_ban',
        'ngay_ban_hanh',
        'ngay_co_hieu_luc',
        'ngay_het_hieu_luc',
        'ngay_dang_cong_bao',
        'nganh',
        'linh_vuc',
        'co_quan_ban_hanh',
        'chuc_danh',
        'nguoi_ky',
        'pham_vi',
        'thong_tin_ap_dung',
        'tinh_trang_hieu_luc',
        'nguon_thu_thap',
        'content_html',
    ];

    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_laws');
    }

    public function outgoingRelations()
    {
        return $this->hasMany(LawRelation::class, 'from_law_id');
    }

    public function incomingRelations()
    {
        return $this->hasMany(LawRelation::class, 'to_law_id');
    }

    public function lawFiles()
    {
        return $this->hasMany(LawFile::class);
    }
}
