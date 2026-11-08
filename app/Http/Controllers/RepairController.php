<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepairBookingRequest;
use App\Models\Master;
use App\Models\RepairBooking;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RepairController extends Controller
{
    public function create(Request $request): View
    {
        $selected = $request->validate(['service' => ['nullable', Rule::in(array_keys(config('repair.services')))], 'master' => ['nullable', 'integer', Rule::exists('masters', 'id')->where('is_active', true)]]);

        return view('repairs.create', ['masters' => Master::where('is_active', true)->orderBy('name')->get(), 'selected' => $selected, 'submissionKey' => (string) Str::uuid()]);
    }

    public function store(RepairBookingRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photos', 'consent']);
        $existing = RepairBooking::where('submission_key', $data['submission_key'])->first();
        if ($existing) {
            abort_unless($existing->user_id === $request->user()->id, 403);

            return redirect()->route('repairs.show', $existing)->with('success', 'Эта заявка уже отправлена.');
        }
        if (! empty($data['master_id']) && ! Master::whereKey($data['master_id'])->whereJsonContains('services', $data['service'])->exists()) {
            throw ValidationException::withMessages(['master_id' => 'Выберите мастера, который работает с этой неисправностью.']);
        }
        $paths = [];
        try {
            foreach ($request->file('photos', []) as $photo) {
                $paths[] = $photo->store('repairs', 'local');
            }
            $repair = RepairBooking::create($data + ['user_id' => $request->user()->id, 'status' => 'new', 'photos' => $paths, 'consented_at' => now()]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($paths);
            if ($exception instanceof QueryException && ($existing = RepairBooking::where('submission_key', $data['submission_key'])->where('user_id', $request->user()->id)->first())) {
                return redirect()->route('repairs.show', $existing)->with('success', 'Эта заявка уже отправлена.');
            }
            throw $exception;
        }

        return redirect()->route('repairs.show', $repair)->with('success', 'Заявка отправлена. Теперь можно следить за её этапами.');
    }

    public function account(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', Rule::in(array_keys(RepairBooking::STATUSES))]]);

        return view('account.index', ['repairs' => $request->user()->repairs()->with('master')->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(8)->withQueryString(), 'activeCount' => $request->user()->repairs()->whereNotIn('status', ['closed', 'cancelled'])->count(), 'filter' => $filters['status'] ?? null]);
    }

    public function show(RepairBooking $repair): View
    {
        Gate::authorize('view-repair', $repair);

        return view('repairs.show', ['repair' => $repair->load(['master', 'events'])]);
    }

    public function photo(RepairBooking $repair, int $index): StreamedResponse
    {
        Gate::authorize('view-repair', $repair);
        abort_unless(isset($repair->photos[$index]), 404);

        return Storage::disk('local')->response($repair->photos[$index], null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function cancel(Request $request, RepairBooking $repair): RedirectResponse
    {
        abort_unless($repair->user_id === $request->user()->id, 403);
        DB::transaction(function () use ($repair): void {
            $current = RepairBooking::whereKey($repair->id)->lockForUpdate()->firstOrFail();
            if (! in_array($current->status, ['new', 'accepted', 'diagnosing', 'approval'], true)) {
                throw ValidationException::withMessages(['status' => 'На этом этапе отмена через сайт недоступна. Свяжитесь с мастерской.']);
            }
            $current->update(['status' => 'cancelled', 'public_message' => 'Клиент отменил заявку.']);
        });

        return back()->with('success', 'Заявка отменена.');
    }

    public function approveQuote(Request $request, RepairBooking $repair): RedirectResponse
    {
        abort_unless($repair->user_id === $request->user()->id, 403);
        $request->validate(['consent' => ['accepted']]);
        DB::transaction(function () use ($request, $repair): void {
            $current = RepairBooking::whereKey($repair->id)->lockForUpdate()->firstOrFail();
            if ($current->status !== 'approval' || ! $current->quoted_price) {
                throw ValidationException::withMessages(['status' => 'Согласование стоимости сейчас недоступно.']);
            }
            $request->validate(['quoted_price' => ['required', 'integer', Rule::in([$current->quoted_price])]]);
            $current->update(['status' => 'repairing', 'quote_accepted_at' => now(), 'public_message' => 'Клиент согласовал стоимость ремонта.']);
        });

        return back()->with('success', 'Стоимость согласована. Мастер может приступить к ремонту.');
    }
}
