@if(!empty($pscAllowance))
    <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-blue-800">PSC Allowance</h2>
            <span class="text-sm text-gray-500">
                Billed every {{ ($pscAllowance[0]['interval'] ?? 'month') === 'year' ? 'year' : 'month' }} on your plan
            </span>
        </div>

        <form action="{{ route('companies.psc.topup') }}" method="POST">
            @csrf
            <div class="divide-y divide-gray-100">
                @foreach($pscAllowance as $allowance)
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between px-6 py-4 gap-3">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $allowance['name'] }}</p>
                            <p class="text-sm text-gray-500">
                                £{{ number_format($allowance['price'], 2) }} / {{ $allowance['interval'] === 'year' ? 'year' : 'month' }} per slot
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold {{ $allowance['remaining'] > 0 ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ $allowance['active'] }} / {{ $allowance['billed'] }}
                                </span> used
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="psc[{{ $allowance['id'] }}]" min="0" max="100" value="0"
                                    placeholder="0"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                <button type="submit"
                                    class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-300 shadow-md whitespace-nowrap">
                                    <i class="fas fa-plus-circle mr-1"></i> Buy More
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($allowance['remaining'] <= 0 && $allowance['billed'] > 0)
                        <p class="px-6 pb-3 -mt-1 text-xs text-orange-600">
                            You have used all {{ $allowance['billed'] }} paid slot(s). Buy more to register additional Persons with Significant Control.
                        </p>
                    @endif
                @endforeach
            </div>
        </form>
    </div>
@endif