<div class="space-y-3">
    @forelse($logs as $log)
        @php
            $oldFormatted = $log->old_value ? \Carbon\Carbon::parse($log->old_value)->format('M d, Y g:i A') : '—';
            $newFormatted = $log->new_value ? \Carbon\Carbon::parse($log->new_value)->format('M d, Y g:i A') : '—';
        @endphp
        <div class="flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm">
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900">
                    {{ $log->editor->name ?? 'Unknown' }}
                    <span class="text-gray-500 font-normal">edited {{ ucfirst(str_replace('_', ' ', $log->field)) }}</span>
                </div>
                <div class="mt-1 text-gray-600">
                    <span class="text-red-600 line-through">{{ $oldFormatted }}</span>
                    <span class="mx-1">→</span>
                    <span class="text-green-700 font-medium">{{ $newFormatted }}</span>
                </div>
                <div class="mt-1 text-xs text-gray-400">{{ $log->created_at->format('M d, Y g:i A') }}</div>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500 py-2">No edits recorded for this attendance.</p>
    @endforelse
</div>
