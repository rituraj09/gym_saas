<x-owner-layout>
    <x-slot name="title">Record Payment</x-slot>
    <x-slot name="headerTitle">Record Payment</x-slot>

    <div class="max-w-2xl">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex gap-3">
            <x-icon name="check-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Recording Payments</p>
                <p>Select a member, choose a membership (optional), enter amount and method. You can also upload a
                    receipt as proof.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('owner.payments.store') }}" enctype="multipart/form-data"
            class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 space-y-5">
            @csrf

            <div>
                <label for="member_id" class="block text-sm font-medium text-gray-700 mb-1.5">Member <span
                        class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('member_id') border-red-500 @enderror"
                    onchange="loadMemberships(this.value)">
                    <option value="">— Select Member —</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}"
                            {{ old('member_id', $selectedMember?->id) == $member->id ? 'selected' : '' }}>
                            {{ $member->user->name }} ({{ $member->member_code }})
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="membership_id" class="block text-sm font-medium text-gray-700 mb-1.5">Membership <span
                        class="text-gray-400 text-xs">(optional)</span></label>
                <select name="membership_id" id="membership_id"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    <option value="">— No specific membership —</option>
                    @if ($selectedMember)
                        @foreach ($selectedMember->memberships as $m)
                            <option value="{{ $m->id }}" {{ old('membership_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->plan->name }} · {{ $m->start_date->format('M d, Y') }} —
                                {{ $m->end_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <p class="mt-1 text-xs text-gray-500">Links the payment to a specific membership for better tracking.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01"
                        min="0.01" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-1.5">Payment Method
                        <span class="text-red-500">*</span></label>
                    <select name="payment_method_id" id="payment_method_id" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('payment_method_id') border-red-500 @enderror">
                        <option value="">— Select Method —</option>
                        @foreach ($methods as $method)
                            <option value="{{ $method->id }}"
                                {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>{{ $method->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1.5">Payment Date <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" id="payment_date"
                        value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
                <div>
                    <label for="transaction_reference"
                        class="block text-sm font-medium text-gray-700 mb-1.5">Transaction Reference</label>
                    <input type="text" name="transaction_reference" id="transaction_reference"
                        value="{{ old('transaction_reference') }}" placeholder="e.g. UPI ref, cheque no."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label for="receipt" class="block text-sm font-medium text-gray-700 mb-1.5">Receipt <span
                        class="text-gray-400 text-xs">(JPG, PNG or PDF, max 5MB)</span></label>
                <input type="file" name="receipt" id="receipt" accept="image/*,application/pdf"
                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                @error('receipt')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row-reverse gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
                    <x-icon name="check-circle" class="w-5 h-5" /> Record Payment
                </button>
                <a href="{{ route('owner.payments.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition min-h-[44px]">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function loadMemberships(memberId) {
            if (!memberId) return;
            window.location.href = '{{ route('owner.payments.create') }}?member_id=' + memberId;
        }
    </script>
</x-owner-layout>
