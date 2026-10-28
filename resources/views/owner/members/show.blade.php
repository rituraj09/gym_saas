<x-owner-layout>
    <x-slot name="title">{{ $member->user->name }}</x-slot>
    <x-slot name="headerTitle">Member Details</x-slot>

    @php $isActive = $member->user->accountStatus?->code === 'active'; @endphp

    <div class="max-w-3xl space-y-6">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                @if ($member->profile_photo)
                    <img src="{{ asset('storage/' . $member->profile_photo) }}"
                        class="w-20 h-20 rounded-2xl object-cover border">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span
                            class="text-blue-700 font-bold text-2xl">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold text-gray-900">{{ $member->user->name }}</h2>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 font-mono mt-1">{{ $member->member_code }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $member->gender?->name ?? '—' }} ·
                        {{ $member->bloodGroup?->name ?? '—' }}</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('owner.members.edit', $member) }}"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                        <x-icon name="cog" class="w-4 h-4" /> Edit
                    </a>
                    <form method="POST" action="{{ route('owner.members.toggle-status', $member) }}"
                        onsubmit="return confirm('Are you sure you want to {{ $isActive ? 'deactivate' : 'activate' }} this member?')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 {{ $isActive ? 'bg-red-50 text-red-700 hover:bg-red-100 border-red-200' : 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200' }} border text-sm font-medium rounded-xl transition min-h-[44px]">
                            {{ $isActive ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact & Personal Details -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Contact & Personal Details</h3>
            </header>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Phone</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->user->phone }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Date of Birth</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->date_of_birth?->format('M d, Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Joining Date</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->joining_date?->format('M d, Y') }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-500 mb-0.5">Address</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->address ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Emergency Contact</h3>
            </header>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Name</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->emergency_contact_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Phone</p>
                    <p class="text-sm font-medium text-gray-900">{{ $member->emergency_contact_phone ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Placeholders for future phases -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php $activeMembership = $member->activeMembership; @endphp
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Current Membership</h3>
                    <a href="{{ route('owner.memberships.create', ['member_id' => $member->id]) }}"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-2 py-1 rounded-lg hover:bg-indigo-50 transition min-h-[36px] flex items-center">
                        {{ $activeMembership ? 'Renew' : 'Assign' }}
                    </a>
                </header>
                <div class="p-5">
                    @if ($activeMembership)
                        <p class="font-semibold text-gray-900">{{ $activeMembership->plan->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Expires
                            {{ $activeMembership->end_date->format('M d, Y') }} ·
                            {{ $activeMembership->end_date->diffForHumans() }}</p>
                        <a href="{{ route('owner.memberships.show', $activeMembership) }}"
                            class="text-xs text-indigo-600 hover:underline mt-2 inline-block">View details →</a>
                    @else
                        <p class="text-sm text-gray-500 text-center py-2">No active membership.</p>
                    @endif
                </div>
            </div>
            @php $currentTrainer = $member->currentTrainer(); @endphp
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Assigned Trainer</h3>
                    <a href="{{ route('owner.assignments.create', $member) }}"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-2 py-1 rounded-lg hover:bg-indigo-50 transition min-h-[36px] flex items-center">
                        {{ $currentTrainer ? 'Reassign' : 'Assign' }}
                    </a>
                </header>
                <div class="p-5">
                    @if ($currentTrainer)
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="text-green-700 font-bold">{{ strtoupper(substr($currentTrainer->user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $currentTrainer->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $currentTrainer->specialization ?? 'No specialization' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-2">No trainer assigned.</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Payment History -->
        @php $recentPayments = $member->payments()->with('method', 'status')->latest('payment_date')->take(5)->get(); @endphp
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Recent Payments</h3>
                    <p class="text-xs text-gray-500">Last 5 payments</p>
                </div>
                <a href="{{ route('owner.payments.create', ['member_id' => $member->id]) }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Record Payment</a>
            </header>
            @if ($recentPayments->isEmpty())
                <div class="p-8 text-center">
                    <x-icon name="credit-card" class="w-7 h-7 text-gray-300 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">No payments recorded.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($recentPayments as $p)
                        <a href="{{ route('owner.payments.show', $p) }}"
                            class="flex items-center justify-between px-5 sm:px-6 py-3 hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ number_format($p->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $p->payment_date->format('M d, Y') }} ·
                                    {{ $p->method->name }}</p>
                            </div>
                            <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <a href="{{ route('owner.members.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 transition">
            ← Back to Members
        </a>
    </div>
</x-owner-layout>
