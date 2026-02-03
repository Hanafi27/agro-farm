<div>{{ $item->keterangan ?? '-' }}</div>
@if($item->attachments && $item->attachments->count())
    <div class="mt-2">
        <div class="text-xs text-gray-600 mb-2">Bukti Transaksi:</div>
        <div class="flex flex-wrap gap-3">
            @foreach($item->attachments as $att)
                <div class="border border-gray-200 rounded-lg p-2 bg-gray-50">
                    @php
                        $isImage = in_array(strtolower($att->extension), ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif']);
                    @endphp
                    
                    @if($isImage)
                        <!-- Image Preview -->
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $att->path) }}" 
                                 alt="Bukti Transaksi" 
                                 class="w-24 h-24 object-cover rounded border border-gray-300 cursor-pointer hover:opacity-80 transition-opacity"
                                 onclick="openImageModal('{{ asset('storage/' . $att->path) }}', '{{ $att->filename }}')"
                                 onerror="this.style.display='none'">
                        </div>
                    @else
                        <!-- File Icon for non-images -->
                        <div class="mb-2 w-24 h-24 bg-gray-200 rounded border border-gray-300 flex items-center justify-center">
                            <i class="fas fa-file-alt text-2xl text-gray-500"></i>
                        </div>
                    @endif
                    
                    <!-- File Info and Download Link -->
                    <div class="text-center">
                        <div class="text-xs text-gray-600 truncate max-w-24" title="{{ $att->filename }}">
                            {{ $att->filename }}
                        </div>
                        <a href="{{ asset('storage/' . $att->path) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors mt-1">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
