<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use App\Models\FavoriteLaw;
use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteLawController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $favorites = FavoriteLaw::query()
            ->where('user_id', $user->id)
            ->with('law')
            ->latest('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $favorites->items(),
            'pagination' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }

    public function profileIndex(Request $request)
    {
        $user = Auth::user();

        $query = $request->input('q', '');
        $status = $request->input('status', '');
        $sort = $request->input('sort', 'latest');

        $favorites = FavoriteLaw::query()
            ->where('user_id', $user->id)
            ->with('law')
            ->when($query !== '', function ($q) use ($query) {
                $q->whereHas('law', function ($lawQuery) use ($query) {
                    $lawQuery->where('title', 'like', "%{$query}%")
                        ->orWhere('so_ky_hieu', 'like', "%{$query}%")
                        ->orWhere('loai_van_ban', 'like', "%{$query}%")
                        ->orWhere('co_quan_ban_hanh', 'like', "%{$query}%")
                        ->orWhere('linh_vuc', 'like', "%{$query}%");
                });
            })
            ->when($status !== '', function ($q) use ($status) {
                $q->whereHas('law', function ($lawQuery) use ($status) {
                    $lawQuery->where('tinh_trang_hieu_luc', 'like', "%{$status}%" );
                });
            })
            ->when($sort === 'oldest', function ($q) {
                $q->oldest('created_at');
            }, function ($q) {
                $q->latest('created_at');
            })
            ->paginate(10)
            ->appends($request->query());

        return view('profile.favorites', compact('favorites', 'query', 'status', 'sort'));
    }

    public function store(FavoriteRequest $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $law = Law::find($request->input('law_id'));
        if (! $law) {
            return response()->json(['success' => false, 'message' => 'Văn bản không tồn tại'], 404);
        }

        $exists = FavoriteLaw::where('user_id', $user->id)
            ->where('law_id', $law->id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Văn bản đã có trong danh sách yêu thích'], 422);
        }

        return DB::transaction(function () use ($user, $law, $request) {
            FavoriteLaw::create([
                'user_id' => $user->id,
                'law_id' => $law->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm vào yêu thích',
                ]);
            }

            return redirect()->back()->with('success', 'Đã thêm vào yêu thích');
        });
    }

    public function destroy(Request $request, $law_id)
    {
        if ($request->isMethod('post') && $request->input('_method') === 'DELETE') {
            $request->setMethod('DELETE');
        }
        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $favorite = FavoriteLaw::where('user_id', $user->id)
            ->where('law_id', $law_id)
            ->first();

        if (! $favorite) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy mục yêu thích'], 404);
        }

        $this->authorize('delete', $favorite);
        $favorite->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Bỏ khỏi yêu thích thành công');
    }
}
