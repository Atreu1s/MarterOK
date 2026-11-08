<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterRequest;
use App\Http\Requests\RepairUpdateRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Master;
use App\Models\RepairBooking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkspaceController extends Controller
{
    public function index(): View
    {
        Gate::authorize('moderate');

        return view('workspace.index', ['newRepairs' => RepairBooking::where('status', 'new')->count(), 'pendingComments' => Comment::where('status', 'pending')->count(), 'articleCount' => Article::count(), 'activeRepairs' => RepairBooking::whereNotIn('status', ['closed', 'cancelled'])->count(), 'repairs' => RepairBooking::with(['user', 'master'])->latest()->take(5)->get()]);
    }

    public function repairs(Request $request): View
    {
        Gate::authorize('moderate');
        $filters = $request->validate(['status' => ['nullable', Rule::in(array_keys(RepairBooking::STATUSES))], 'q' => ['nullable', 'string', 'max:100']]);

        return view('workspace.repairs', ['repairs' => RepairBooking::with(['user', 'master'])->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))->when($filters['q'] ?? null, fn ($query, $value) => $query->where(fn ($query) => $query->where('device', 'like', '%'.$value.'%')->orWhere('contact_name', 'like', '%'.$value.'%')))->latest()->paginate(10)->withQueryString(), 'masters' => Master::where('is_active', true)->orderBy('name')->get(), 'filters' => $filters]);
    }

    public function updateRepair(RepairUpdateRequest $request, RepairBooking $repair): RedirectResponse
    {
        DB::transaction(function () use ($request, $repair): void {
            $current = RepairBooking::whereKey($repair->id)->lockForUpdate()->firstOrFail();
            $data = $request->validated();
            if (isset($data['quoted_price'])) {
                $data['quoted_price'] = (int) $data['quoted_price'];
            }
            if ($data['status'] !== $current->status && ! in_array($data['status'], RepairBooking::TRANSITIONS[$current->status], true)) {
                throw ValidationException::withMessages(['status' => 'Такой переход между этапами недоступен.']);
            }
            if ($data['status'] === 'repairing' && ! $current->quote_accepted_at) {
                throw ValidationException::withMessages(['status' => 'Сначала клиент должен согласовать стоимость.']);
            }
            if (in_array($current->status, ['repairing', 'ready', 'closed', 'cancelled'], true) && ($data['quoted_price'] ?? null) !== $current->quoted_price) {
                throw ValidationException::withMessages(['quoted_price' => 'Согласованную стоимость нельзя менять на этом этапе.']);
            }
            if ($data['status'] === 'approval' && ! ($data['quoted_price'] ?? null)) {
                throw ValidationException::withMessages(['quoted_price' => 'Укажите стоимость для согласования с клиентом.']);
            }
            $masterId = $data['master_id'] ?? $current->master_id;
            if ($data['status'] !== 'new' && $data['status'] !== 'cancelled' && ! $masterId) {
                throw ValidationException::withMessages(['master_id' => 'Назначьте мастера перед изменением этапа.']);
            }
            if ($masterId && ! Master::whereKey($masterId)->whereJsonContains('services', $current->service)->exists()) {
                throw ValidationException::withMessages(['master_id' => 'Мастер не работает с выбранной неисправностью.']);
            }
            $data['master_id'] = $masterId;
            $current->update($data);
        });

        return back()->with('success', 'Заявка обновлена. Клиент увидит изменения в своём кабинете.');
    }

    public function masters(): View
    {
        Gate::authorize('moderate');

        return view('workspace.masters', ['masters' => Master::orderBy('id')->paginate(10)]);
    }

    public function createMaster(): View
    {
        Gate::authorize('moderate');

        return view('workspace.master-form', ['master' => new Master]);
    }

    public function storeMaster(MasterRequest $request): RedirectResponse
    {
        Master::create($request->validated());

        return redirect()->route('workspace.masters')->with('success', 'Профиль мастера создан.');
    }

    public function editMaster(Master $master): View
    {
        Gate::authorize('moderate');

        return view('workspace.master-form', compact('master'));
    }

    public function updateMaster(MasterRequest $request, Master $master): RedirectResponse
    {
        $master->update($request->validated());

        return redirect()->route('workspace.masters')->with('success', 'Профиль мастера обновлён.');
    }
}
