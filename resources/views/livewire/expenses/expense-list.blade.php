<div>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Expense Management</h2>
            <p class="text-sm text-gray-500 mt-1">General ledger expenses</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="Search expenses..." style="width:200px">
            <button wire:click="create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Expense</button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <select wire:model.live="category" class="form-select form-select-sm">
                <option value="">All Categories</option>
                @foreach($categories as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model.live="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="row mb-3 g-2">
        @foreach($totalByCategory as $cat => $total)
            <div class="col-md-2 col-6 mb-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3">
                    <small class="text-muted">{{ \Illuminate\Support\Str::title($cat) }}</small>
                    <h6 class="mb-0 fw-bold">{{ number_format($total, 2) }}</h6>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('expense_date')" class="text-decoration-none text-dark">
                                Date @if($sortField === 'expense_date') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                            </a>
                        </th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Vendor</th>
                        <th class="text-end">
                            <a href="#" wire:click.prevent="sortBy('amount')" class="text-decoration-none text-dark">
                                Amount @if($sortField === 'amount') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                            </a>
                        </th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td>{{ $e->expense_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-secondary">{{ \Illuminate\Support\Str::title($e->category) }}</span></td>
                            <td>{{ $e->description }}</td>
                            <td>{{ $e->vendor ?? '-' }}</td>
                            <td class="text-end">{{ number_format($e->amount, 2) }} {{ $e->currency }}</td>
                            <td>{{ $e->payment_method ? \Illuminate\Support\Str::title(str_replace('_', ' ', $e->payment_method)) : '-' }}</td>
                            <td>
                                @if($e->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($e->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="edit({{ $e->id }})" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                <button wire:click="delete({{ $e->id }})" wire:confirm="Delete this expense?" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No expenses found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $expenses->links() }}</div>
    </div>

    @if($showForm)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingExpense ? 'Edit Expense' : 'New Expense' }}</h5>
                        <button type="button" class="btn-close" wire:click="$dispatch('expense-saved')"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:expenses.expense-form :expense="$editingExpense" :wire:key="$editingExpense?->id ?? 'new'" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
