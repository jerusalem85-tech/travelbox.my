<div>
    <x-page-header title="Cash Flow Statement" description="{{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}" />

    <x-card>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" wire:model.live="from" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="to" class="form-control">
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr><th colspan="2">Cash Flow Statement</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Opening Cash Balance ({{ \Carbon\Carbon::parse($from)->format('d/m/Y') }})</td>
                    <td class="text-end fw-bold">{{ number_format($openingBalance, 2) }}</td>
                </tr>
                <tr class="table-success">
                    <td colspan="2"><strong>Operating Activities</strong></td>
                </tr>
                <tr>
                    <td class="ps-4">Cash Received (Income)</td>
                    <td class="text-end text-success">{{ number_format($operatingIncome, 2) }}</td>
                </tr>
                <tr>
                    <td class="ps-4">Cash Paid (Expenses)</td>
                    <td class="text-end text-danger">({{ number_format($operatingExpenses, 2) }})</td>
                </tr>
                <tr class="fw-bold">
                    <td>Net Cash from Operations</td>
                    <td class="text-end {{ $periodChange >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($periodChange, 2) }}
                    </td>
                </tr>
                <tr class="fw-bold table-primary">
                    <td>Closing Cash Balance ({{ \Carbon\Carbon::parse($to)->format('d/m/Y') }})</td>
                    <td class="text-end">{{ number_format($closingBalance, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </x-card>
</div>
