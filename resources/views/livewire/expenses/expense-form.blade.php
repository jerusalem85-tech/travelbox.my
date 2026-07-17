<div>
    <form wire:submit="save">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select wire:model="category" class="form-control @error('category') is-invalid @enderror">
                    @foreach($categories as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                <input type="date" wire:model="expense_date" class="form-control @error('expense_date') is-invalid @enderror">
                @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <input type="text" wire:model="description" class="form-control @error('description') is-invalid @enderror" placeholder="e.g. Office rent March 2026">
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" wire:model="amount" class="form-control @error('amount') is-invalid @enderror">
                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Currency</label>
                <select wire:model="currency" class="form-control">
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="EGP">EGP</option>
                    <option value="SAR">SAR</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Payment Method</label>
                <select wire:model="payment_method" class="form-control">
                    <option value="">Select</option>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank Transfer</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Vendor/Payee</label>
                <input type="text" wire:model="vendor" class="form-control" placeholder="Supplier or recipient name">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Reference #</label>
                <input type="text" wire:model="reference" class="form-control" placeholder="Invoice or receipt number">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <div class="d-flex gap-3">
                <label class="form-check"><input type="radio" wire:model="status" value="pending" class="form-check-input"> Pending</label>
                <label class="form-check"><input type="radio" wire:model="status" value="paid" class="form-check-input"> Paid</label>
                <label class="form-check"><input type="radio" wire:model="status" value="cancelled" class="form-check-input"> Cancelled</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea wire:model="notes" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" wire:click="$dispatch('expense-saved')">Cancel</button>
            <button type="submit" class="btn btn-primary">
                {{ $editing ? 'Update' : 'Create' }} Expense
            </button>
        </div>
    </form>
</div>
