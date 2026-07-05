<?php

namespace App\Http\Controllers;

use App\Models\FavoriteLaw;
use App\Models\Law;
use App\Models\LawFile;
use App\Models\LawRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// use Illuminate\Support\Str;

class LawController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $filters = [
            'linh_vuc' => trim((string) $request->input('linh_vuc', '')),
            'co_quan_ban_hanh' => trim((string) $request->input('co_quan_ban_hanh', '')),
            'tinh_trang_hieu_luc' => trim((string) $request->input('tinh_trang_hieu_luc', '')),
            'ngay_ban_hanh' => trim((string) $request->input('ngay_ban_hanh', '')),
        ];

        $laws = Law::query()
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('so_ky_hieu', 'like', "%{$query}%")
                        ->orWhere('loai_van_ban', 'like', "%{$query}%")
                        ->orWhere('content_html', 'like', "%{$query}%")
                        ->orWhere('co_quan_ban_hanh', 'like', "%{$query}%")
                        ->orWhere('linh_vuc', 'like', "%{$query}%")
                        ->orWhere('nganh', 'like', "%{$query}%");
                });
            })
            ->when($filters['linh_vuc'] !== '', function ($q) use ($filters) {
                $q->where('linh_vuc', 'like', "%{$filters['linh_vuc']}%" );
            })
            ->when($filters['co_quan_ban_hanh'] !== '', function ($q) use ($filters) {
                $q->where('co_quan_ban_hanh', 'like', "%{$filters['co_quan_ban_hanh']}%" );
            })
            ->when($filters['tinh_trang_hieu_luc'] !== '', function ($q) use ($filters) {
                $q->where('tinh_trang_hieu_luc', 'like', "%{$filters['tinh_trang_hieu_luc']}%" );
            })
            ->when($filters['ngay_ban_hanh'] !== '', function ($q) use ($filters) {
                $q->whereDate('ngay_ban_hanh', $filters['ngay_ban_hanh']);
            })
            ->latest('id')
            ->paginate(10)
            ->appends($request->query());

        $fields = Law::query()
            ->select('linh_vuc')
            ->distinct()
            ->whereNotNull('linh_vuc')
            ->where('linh_vuc', '!=', '')
            ->orderBy('linh_vuc')
            ->pluck('linh_vuc');

        $agencies = Law::query()
            ->select('co_quan_ban_hanh')
            ->distinct()
            ->whereNotNull('co_quan_ban_hanh')
            ->where('co_quan_ban_hanh', '!=', '')
            ->orderBy('co_quan_ban_hanh')
            ->pluck('co_quan_ban_hanh');

        $statuses = Law::query()
            ->select('tinh_trang_hieu_luc')
            ->distinct()
            ->whereNotNull('tinh_trang_hieu_luc')
            ->where('tinh_trang_hieu_luc', '!=', '')
            ->orderBy('tinh_trang_hieu_luc')
            ->pluck('tinh_trang_hieu_luc');

        return view('pages.legal_documents.index', compact('laws', 'query', 'filters', 'fields', 'agencies', 'statuses'));
    }

    public function create()
    {
        return view('pages.legal_documents.form', [
            'mode' => 'create',
            'law' => new Law(),
            'relatedLaws' => Law::query()->orderBy('title')->get(),
            'existingRelations' => collect(),
        ]);
    }

    public function edit(Law $law)
    {
        $law->loadMissing('lawFiles');

        return view('pages.legal_documents.form', [
            'mode' => 'edit',
            'law' => $law,
            'relatedLaws' => Law::query()
                ->where('id', '!=', $law->id)
                ->orderBy('title')
                ->get(),
            'existingRelations' => LawRelation::query()
                ->where('from_law_id', $law->id)
                ->with(['toLaw'])
                ->get(),
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $searchScope = in_array($request->input('search_scope'), ['all', 'title', 'so_ky_hieu', 'content_html'], true)
            ? $request->input('search_scope')
            : 'all';

        $selectedFilters = [
            'loai_van_ban' => array_values(array_filter(array_map('trim', (array) $request->input('loai_van_ban', [])))),
            'linh_vuc' => array_values(array_filter(array_map('trim', (array) $request->input('linh_vuc', [])))),
            'tinh_trang_hieu_luc' => array_values(array_filter(array_map('trim', (array) $request->input('tinh_trang_hieu_luc', [])))),
        ];

        $filterOptions = [
            'loai_van_ban' => Law::query()
                ->select('loai_van_ban')
                ->distinct()
                ->whereNotNull('loai_van_ban')
                ->where('loai_van_ban', '!=', '')
                ->orderBy('loai_van_ban')
                ->get()
                ->pluck('loai_van_ban')
                ->filter()
                ->unique()
                ->values()
                ->all(),
            'linh_vuc' => Law::query()
                ->select('linh_vuc')
                ->distinct()
                ->whereNotNull('linh_vuc')
                ->where('linh_vuc', '!=', '')
                ->orderBy('linh_vuc')
                ->get()
                ->pluck('linh_vuc')
                ->filter()
                ->unique()
                ->values()
                ->all(),
            'tinh_trang_hieu_luc' => Law::query()
                ->select('tinh_trang_hieu_luc')
                ->distinct()
                ->whereNotNull('tinh_trang_hieu_luc')
                ->where('tinh_trang_hieu_luc', '!=', '')
                ->orderBy('tinh_trang_hieu_luc')
                ->get()
                ->pluck('tinh_trang_hieu_luc')
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];

        $laws = collect();
        $hasSearch = $query !== '' || !empty($selectedFilters['loai_van_ban']) || !empty($selectedFilters['linh_vuc']) || !empty($selectedFilters['tinh_trang_hieu_luc']);

        if ($hasSearch) {
            if ($query !== '') {
                $history = (array) session('recent_searches', []);
                $history = array_values(array_filter($history, function ($item) use ($query) {
                    return ($item['query'] ?? '') !== $query;
                }));
                array_unshift($history, [
                    'query' => $query,
                    'search_scope' => $searchScope,
                    'created_at' => now()->toDateTimeString(),
                ]);
                session(['recent_searches' => array_slice($history, 0, 6)]);
            }

            try {
                $lawsQuery = Law::query();

                if ($searchScope === 'title') {
                    $lawsQuery->where('title', 'like', "%{$query}%");
                } elseif ($searchScope === 'so_ky_hieu') {
                    $lawsQuery->where('so_ky_hieu', 'like', "%{$query}%");
                } elseif ($searchScope === 'content_html') {
                    $lawsQuery->where('content_html', 'like', "%{$query}%");
                } elseif ($query !== '') {
                    $lawsQuery->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                            ->orWhere('so_ky_hieu', 'like', "%{$query}%")
                            ->orWhere('content_html', 'like', "%{$query}%")
                            ->orWhere('co_quan_ban_hanh', 'like', "%{$query}%")
                            ->orWhere('linh_vuc', 'like', "%{$query}%")
                            ->orWhere('nganh', 'like', "%{$query}%");
                    });
                }

                if (!empty($selectedFilters['loai_van_ban'])) {
                    $lawsQuery->whereIn('loai_van_ban', $selectedFilters['loai_van_ban']);
                }

                if (!empty($selectedFilters['linh_vuc'])) {
                    $lawsQuery->whereIn('linh_vuc', $selectedFilters['linh_vuc']);
                }

                if (!empty($selectedFilters['tinh_trang_hieu_luc'])) {
                    $lawsQuery->whereIn('tinh_trang_hieu_luc', $selectedFilters['tinh_trang_hieu_luc']);
                }

                $laws = $lawsQuery
                    ->latest('id')
                    ->paginate(8)
                    ->withQueryString();
            } catch (\Throwable $e) {
                $laws = collect();
            }
        }

        return view('search', compact('laws', 'query', 'hasSearch', 'searchScope', 'filterOptions', 'selectedFilters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'so_ky_hieu' => ['nullable', 'string', 'max:100'],
            'loai_van_ban' => ['nullable', 'string', 'max:100'],
            'ngay_ban_hanh' => ['nullable', 'date'],
            'ngay_co_hieu_luc' => ['nullable', 'date'],
            'nganh' => ['nullable', 'string', 'max:100'],
            'linh_vuc' => ['nullable', 'string', 'max:100'],
            'co_quan_ban_hanh' => ['nullable', 'string', 'max:255'],
            'chuc_danh' => ['nullable', 'string', 'max:100'],
            'nguoi_ky' => ['nullable', 'string', 'max:100'],
            'pham_vi' => ['nullable', 'string'],
            'thong_tin_ap_dung' => ['nullable', 'string'],
            'tinh_trang_hieu_luc' => ['nullable', 'string', 'max:100'],
            'nguon_thu_thap' => ['nullable', 'string', 'max:255'],
            'content_html' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt,zip,rar', 'max:20480'],
        ]);

        $data['source_id'] = $request->input('source_id') ?: (Law::max('source_id') + 1);

        DB::transaction(function () use ($data, $request) {
            $law = Law::create($data);
            $this->syncLawRelations($law, $request);
            $this->storeLawFiles($law, $request);
        });

        return redirect()->route('admin.ui.legal-documents')->with('success', 'Đã thêm văn bản thành công.');
    }

    public function update(Request $request, Law $law)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'so_ky_hieu' => ['nullable', 'string', 'max:100'],
            'loai_van_ban' => ['nullable', 'string', 'max:100'],
            'ngay_ban_hanh' => ['nullable', 'date'],
            'ngay_co_hieu_luc' => ['nullable', 'date'],
            'nganh' => ['nullable', 'string', 'max:100'],
            'linh_vuc' => ['nullable', 'string', 'max:100'],
            'co_quan_ban_hanh' => ['nullable', 'string', 'max:255'],
            'chuc_danh' => ['nullable', 'string', 'max:100'],
            'nguoi_ky' => ['nullable', 'string', 'max:100'],
            'pham_vi' => ['nullable', 'string'],
            'thong_tin_ap_dung' => ['nullable', 'string'],
            'tinh_trang_hieu_luc' => ['nullable', 'string', 'max:100'],
            'nguon_thu_thap' => ['nullable', 'string', 'max:255'],
            'content_html' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt,zip,rar', 'max:20480'],
            'delete_attachment_ids' => ['nullable', 'array'],
            'delete_attachment_ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($law, $data, $request) {
            $law->update($data);
            $this->syncLawRelations($law, $request);
            $this->deleteLawFiles($law, $request);
            $this->storeLawFiles($law, $request);
        });

        return redirect()->route('admin.ui.legal-documents')->with('success', 'Đã cập nhật văn bản thành công.');
    }

    public function destroy(Law $law)
    {
        $law->delete();

        return redirect()->route('admin.ui.legal-documents')->with('success', 'Đã xóa văn bản thành công.');
    }

    public function downloadFile($id)
    {
        $file = LawFile::find($id);

        if (!$file) {
            abort(404, 'Không tìm thấy file đính kèm.');
        }

        if (!$file->file_path || !Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File đính kèm không tồn tại trên hệ thống.');
        }

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    protected function deleteLawFiles(Law $law, Request $request): void
    {
        $deleteIds = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('delete_attachment_ids', [])))));

        if ($deleteIds === []) {
            return;
        }

        $attachments = $law->lawFiles()->whereIn('id', $deleteIds)->get();

        if ($attachments->count() !== count($deleteIds)) {
            abort(422, 'Một hoặc nhiều file đính kèm không hợp lệ.');
        }

        foreach ($attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();
        }
    }

    protected function storeLawFiles(Law $law, Request $request): void
    {
        $uploadedFiles = $request->file('attachments', []);

        if (empty($uploadedFiles)) {
            return;
        }

        $files = is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles];
        $storedPaths = [];

        try {
            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    throw new \RuntimeException('Một hoặc nhiều file đính kèm không hợp lệ.');
                }

                $timestamp = now()->format('YmdHis');
                $randomPart = Str::random(8);
                $extension = strtolower($file->getClientOriginalExtension());
                $storedName = $timestamp . '_' . $randomPart . ($extension ? '.' . $extension : '');

                $filePath = $file->storeAs('laws', $storedName, 'public');
                $storedPaths[] = $filePath;

                LawFile::create([
                    'law_id' => $law->id,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $storedName,
                    'file_path' => $filePath,
                    'file_type' => $extension,
                    'file_size' => $file->getSize(),
                ]);
            }
        } catch (\Throwable $e) {
            foreach ($storedPaths as $storedPath) {
                if (Storage::disk('public')->exists($storedPath)) {
                    Storage::disk('public')->delete($storedPath);
                }
            }

            throw $e;
        }
    }

    public function syncLawRelations(Law $law, Request $request): void
    {
        $relationTypeMap = [
            'MODIFY' => 'Sửa đổi',
            'REPLACE' => 'Thay thế',
            'ABOLISH' => 'Bãi bỏ',
            'GUIDE' => 'Hướng dẫn',
            'SỬA ĐỔI' => 'Sửa đổi',
            'THAY THẾ' => 'Thay thế',
            'BÃI BỎ' => 'Bãi bỏ',
            'HƯỚNG DẪN' => 'Hướng dẫn',
        ];

        $rawRelationCodes = array_map('trim', (array) $request->input('relation_law_code', []));
        $rawRelationIds = array_map('trim', (array) $request->input('relation_law_id', []));
        $rawRelationTypes = array_map('trim', (array) $request->input('relation_type', []));
        $rawRelationDescriptions = array_map('trim', (array) $request->input('relation_description', []));

        $validatedRelations = [];
        $rowCount = max(count($rawRelationCodes), count($rawRelationIds), count($rawRelationTypes), count($rawRelationDescriptions));

        for ($index = 0; $index < $rowCount; $index++) {
            $lawCode = $rawRelationCodes[$index] ?? '';
            $toLawId = $rawRelationIds[$index] ?? null;
            $relationType = $rawRelationTypes[$index] ?? null;
            $description = $rawRelationDescriptions[$index] ?? null;

            if ($lawCode === '' && (!$toLawId || $toLawId === '')) {
                continue;
            }

            $normalizedRelationType = mb_strtoupper(trim($relationType));
            if (!isset($relationTypeMap[$normalizedRelationType])) {
                abort(422, 'Relation type không hợp lệ.');
            }

            $relationType = $relationTypeMap[$normalizedRelationType];

            if (!$toLawId || !Law::whereKey($toLawId)->exists()) {
                abort(422, 'Văn bản liên quan không tồn tại.');
            }

            if ((int) $law->id === (int) $toLawId) {
                abort(422, 'Không được phép thiết lập quan hệ với chính văn bản này.');
            }

            $validatedRelations[] = [
                'from_law_id' => $law->id,
                'to_law_id' => $toLawId,
                'relation_type' => $relationType,
                'description' => $description,
            ];
        }

        LawRelation::where('from_law_id', $law->id)->delete();

        if (!empty($validatedRelations)) {
            LawRelation::insert($validatedRelations);
        }
    }

    public function show($id)
    {
        $law = Law::with(['outgoingRelations.toLaw', 'incomingRelations.fromLaw', 'lawFiles'])->findOrFail($id);

        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = FavoriteLaw::where('user_id', Auth::id())
                ->where('law_id', $law->id)
                ->exists();
        }

        $recentViews = (array) session('recent_views', []);
        $recentViews = array_values(array_filter($recentViews, function ($item) use ($law) {
            return ($item['id'] ?? null) != $law->id;
        }));
        array_unshift($recentViews, [
            'id' => $law->id,
            'title' => $law->title,
            'created_at' => now()->toDateTimeString(),
        ]);
        session(['recent_views' => array_slice($recentViews, 0, 6)]);

        return view('law.show', compact('law', 'isFavorite'));
    }
}
