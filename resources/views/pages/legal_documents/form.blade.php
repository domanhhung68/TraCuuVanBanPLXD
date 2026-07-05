@extends('layouts.app')

@section('title', $mode === 'edit' ? 'Chỉnh sửa văn bản' : 'Thêm văn bản')
@section('layout_type', 'admin')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $mode === 'edit' ? 'Chỉnh sửa văn bản' : 'Thêm văn bản mới' }}</h2>
            <p class="text-muted mb-0">{{ $mode === 'edit' ? 'Cập nhật thông tin văn bản pháp luật.' : 'Nhập thông tin văn bản pháp luật mới vào hệ thống.' }}</p>
        </div>
        <a href="{{ route('admin.ui.legal-documents') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="POST" action="{{ $mode === 'edit' ? route('admin.ui.law.update', $law) : route('admin.ui.law.store') }}" class="row g-3" enctype="multipart/form-data">
                @csrf
                @if ($mode === 'edit')
                    @method('PUT')
                @endif

                <div class="col-12 col-lg-6">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="title" value="{{ old('title', $law->title) }}" class="form-control" required>
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Số ký hiệu</label>
                    <input type="text" name="so_ky_hieu" value="{{ old('so_ky_hieu', $law->so_ky_hieu) }}" class="form-control">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Loại văn bản</label>
                    <input type="text" name="loai_van_ban" value="{{ old('loai_van_ban', $law->loai_van_ban) }}" class="form-control">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Lĩnh vực</label>
                    <input type="text" name="linh_vuc" value="{{ old('linh_vuc', $law->linh_vuc) }}" class="form-control">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Cơ quan ban hành</label>
                    <input type="text" name="co_quan_ban_hanh" value="{{ old('co_quan_ban_hanh', $law->co_quan_ban_hanh) }}" class="form-control">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Trạng thái hiệu lực</label>
                    <input type="text" name="tinh_trang_hieu_luc" value="{{ old('tinh_trang_hieu_luc', $law->tinh_trang_hieu_luc) }}" class="form-control">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Ngày ban hành</label>
                    <input type="date" name="ngay_ban_hanh" value="{{ old('ngay_ban_hanh', optional($law->ngay_ban_hanh)->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Ngày có hiệu lực</label>
                    <input type="date" name="ngay_co_hieu_luc" value="{{ old('ngay_co_hieu_luc', optional($law->ngay_co_hieu_luc)->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 mt-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-1">Lược đồ quan hệ văn bản</h5>
                                    <p class="text-muted mb-0">Thiết lập quan hệ của văn bản với các văn bản khác.</p>
                                </div>
                                <button type="button" id="add-relation-row" class="btn btn-outline-primary btn-sm rounded-pill">
                                    + Thêm quan hệ
                                </button>
                            </div>

                            @php
                                $relationTypeLabels = [
                                    'MODIFY' => 'Sửa đổi',
                                    'REPLACE' => 'Thay thế',
                                    'ABOLISH' => 'Bãi bỏ',
                                    'GUIDE' => 'Hướng dẫn',
                                    'SỬA ĐỔI' => 'Sửa đổi',
                                    'THAY THẾ' => 'Thay thế',
                                    'BÃI BỎ' => 'Bãi bỏ',
                                    'HƯỚNG DẪN' => 'Hướng dẫn',
                                ];

                                $oldRelationTypes = old('relation_type', []);
                                $oldRelationTypes = array_map(function ($value) use ($relationTypeLabels) {
                                    $normalized = mb_strtoupper(trim($value));
                                    return $relationTypeLabels[$normalized] ?? $value;
                                }, $oldRelationTypes);

                                $oldRelationLawIds = old('relation_law_id', []);
                                $oldRelationLawCodes = old('relation_law_code', []);
                                $oldRelationDescriptions = old('relation_description', []);
                                $relationRows = count($oldRelationTypes) > 0
                                    ? $oldRelationTypes
                                    : $existingRelations->pluck('relation_type')->map(function ($value) use ($relationTypeLabels) {
                                        $normalized = mb_strtoupper(trim($value));
                                        return $relationTypeLabels[$normalized] ?? $value;
                                    })->all();
                            @endphp

                            <div id="relation-rows">
                                @if(count($oldRelationTypes) > 0)
                                    @foreach($oldRelationTypes as $index => $relationType)
                                        <div class="relation-row row g-3 align-items-end mb-3">
                                            <div class="col-12 col-lg-3">
                                                <label class="form-label">Loại quan hệ</label>
                                                <select class="form-select" name="relation_type[]">
                                                    @foreach(['Sửa đổi','Thay thế','Bãi bỏ','Hướng dẫn'] as $type)
                                                        <option value="{{ $type }}" {{ $type === $relationType ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label class="form-label">Văn bản liên quan</label>
                                                <input type="text" name="relation_law_code[]" list="related-law-list" class="form-control relation-law-input" value="{{ $oldRelationLawCodes[$index] ?? '' }}" placeholder="Gõ số hiệu văn bản để tìm">
                                                <input type="hidden" class="relation-law-id" name="relation_law_id[]" value="{{ $oldRelationLawIds[$index] ?? '' }}">
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" name="relation_description[]" value="{{ $oldRelationDescriptions[$index] ?? '' }}">
                                            </div>
                                            <div class="col-12 col-lg-1">
                                                <button type="button" class="btn btn-outline-danger rounded-pill btn-sm w-100 remove-relation-row">Xóa</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($existingRelations->isNotEmpty())
                                    @foreach($existingRelations as $relation)
                                        <div class="relation-row row g-3 align-items-end mb-3">
                                            <div class="col-12 col-lg-3">
                                                <label class="form-label">Loại quan hệ</label>
                                                @php
                                                    $relationValue = $relationTypeLabels[mb_strtoupper(trim($relation->relation_type))] ?? $relation->relation_type;
                                                @endphp
                                                <select class="form-select" name="relation_type[]">
                                                    @foreach(['Sửa đổi','Thay thế','Bãi bỏ','Hướng dẫn'] as $type)
                                                        <option value="{{ $type }}" {{ $type === $relationValue ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label class="form-label">Văn bản liên quan</label>
                                                <input type="text" name="relation_law_code[]" list="related-law-list" class="form-control relation-law-input" value="{{ $relation->toLaw->so_ky_hieu ?? '' }}" placeholder="Gõ số hiệu văn bản để tìm">
                                                <input type="hidden" class="relation-law-id" name="relation_law_id[]" value="{{ $relation->toLaw->id ?? '' }}">
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" name="relation_description[]" value="{{ $relation->description }}">
                                            </div>
                                            <div class="col-12 col-lg-1">
                                                <button type="button" class="btn btn-outline-danger rounded-pill btn-sm w-100 remove-relation-row">Xóa</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <datalist id="related-law-list">
                                @foreach($relatedLaws as $relatedLaw)
                                    @if($relatedLaw->so_ky_hieu)
                                        <option value="{{ $relatedLaw->so_ky_hieu }}" label="{{ $relatedLaw->title }}" data-law-id="{{ $relatedLaw->id }}"></option>
                                    @endif
                                @endforeach
                            </datalist>

                            <template id="relation-row-template">
                                <div class="relation-row row g-3 align-items-end mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label class="form-label">Loại quan hệ</label>
                                        <select class="form-select" name="relation_type[]">
                                            @foreach(['Sửa đổi','Thay thế','Bãi bỏ','Hướng dẫn'] as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label class="form-label">Văn bản liên quan</label>
                                        <input type="text" name="relation_law_code[]" list="related-law-list" class="form-control relation-law-input" placeholder="Gõ số hiệu văn bản để tìm">
                                        <input type="hidden" class="relation-law-id" name="relation_law_id[]" value="">
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label class="form-label">Description</label>
                                        <input type="text" class="form-control" name="relation_description[]" value="">
                                    </div>
                                    <div class="col-12 col-lg-1">
                                        <button type="button" class="btn btn-outline-danger rounded-pill btn-sm w-100 remove-relation-row">Xóa</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">File đính kèm</label>
                    </div>

                    @if($mode === 'edit' && $law->lawFiles->isNotEmpty())
                        <div class="border rounded-3 p-3 bg-light mb-3">
                            <div class="fw-semibold mb-2">File hiện có</div>
                            <div class="list-group list-group-flush">
                                @foreach($law->lawFiles as $attachment)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium">{{ $attachment->original_name }}</div>
                                            <div class="small text-muted">
                                                {{ $attachment->file_type ?: 'file' }} • {{ number_format(max(1, (int) ceil(($attachment->file_size ?? 0) / 1024)), 0) }} KB
                                            </div>
                                        </div>
                                        <label class="btn btn-outline-danger btn-sm rounded-pill mb-0">
                                            <input type="checkbox" name="delete_attachment_ids[]" value="{{ $attachment->id }}" class="me-2">
                                            Xóa
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <input type="file" name="attachments[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.jpeg,.png">
                    <div class="form-text">Có thể chọn nhiều file cùng lúc</div>
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Nội dung</label>
                        <label class="btn btn-outline-primary btn-sm rounded-pill mb-0" for="word-import-file">
                            <i class="fas fa-file-word me-2"></i>Import Word
                        </label>
                    </div>
                    <input id="word-import-file" type="file" accept=".docx" class="d-none">
                    <textarea id="content_html" name="content_html" style="display:none;">{{ old('content_html', $law->content_html) }}</textarea>
                    <div id="editor" class="border rounded-3 p-2 bg-white"></div>
                    <div id="word-import-status" class="form-text mt-2"></div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>{{ $mode === 'edit' ? 'Cập nhật' : 'Lưu văn bản' }}
                    </button>
                    <a href="{{ route('admin.ui.legal-documents') }}" class="btn btn-outline-secondary rounded-pill">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const textarea = document.getElementById('content_html');
                const form = textarea?.closest('form');
                const editorContainer = document.getElementById('editor');

                if (!textarea || !form || !editorContainer) {
                    return;
                }

                ClassicEditor.create(editorContainer, {
                    toolbar: ['heading', 'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                    language: 'vi'
                }).then(editor => {
                    editor.setData(textarea.value || '');

                    editor.model.document.on('change:data', () => {
                        textarea.value = editor.getData();
                    });

                    form.addEventListener('submit', function () {
                        textarea.value = editor.getData();
                    });

                    const importInput = document.getElementById('word-import-file');
                    const statusBox = document.getElementById('word-import-status');

                    importInput?.addEventListener('change', async function () {
                        const file = this.files?.[0];
                        if (!file) {
                            return;
                        }

                        const currentData = editor.getData().trim();
                        if (currentData && !confirm('Bạn có muốn ghi đè nội dung hiện tại không?')) {
                            this.value = '';
                            return;
                        }

                        const formData = new FormData();
                        formData.append('document', file);

                        statusBox.textContent = 'Đang import file Word...';
                        statusBox.className = 'form-text mt-2 text-muted';

                        try {
                            const response = await fetch('{{ route('admin.ui.law.import-word') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            });

                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Không thể import file Word.');
                            }

                            editor.setData(data.html || '');
                            textarea.value = data.html || '';
                            statusBox.textContent = 'Import file Word thành công.';
                            statusBox.className = 'form-text mt-2 text-success';
                        } catch (error) {
                            statusBox.textContent = error.message || 'Đã xảy ra lỗi khi import file Word.';
                            statusBox.className = 'form-text mt-2 text-danger';
                        } finally {
                            this.value = '';
                        }
                    });

                    const relationContainer = document.getElementById('relation-rows');
                    const addRelationButton = document.getElementById('add-relation-row');
                    const rowTemplate = document.getElementById('relation-row-template');
                    const relatedLawList = document.getElementById('related-law-list');

                    function bindRelationRowEvents(row) {
                        const removeButton = row.querySelector('.remove-relation-row');
                        const lawInput = row.querySelector('.relation-law-input');
                        const lawIdInput = row.querySelector('.relation-law-id');

                        removeButton?.addEventListener('click', () => {
                            row.remove();
                        });

                        lawInput?.addEventListener('input', () => {
                            lawIdInput.value = '';
                        });

                        lawInput?.addEventListener('change', () => {
                            const options = Array.from(relatedLawList.options);
                            const matched = options.find(option => option.value === lawInput.value);
                            if (matched) {
                                lawIdInput.value = matched.dataset.lawId || '';
                            } else {
                                lawIdInput.value = '';
                            }
                        });
                    }

                    document.querySelectorAll('.relation-row').forEach(bindRelationRowEvents);

                    addRelationButton?.addEventListener('click', () => {
                        if (!rowTemplate) {
                            return;
                        }

                        const clone = rowTemplate.content.firstElementChild.cloneNode(true);
                        relationContainer.appendChild(clone);
                        bindRelationRowEvents(clone);
                    });
                }).catch(error => {
                    console.error(error);
                });
            });
        </script>
    @endpush
@endsection
