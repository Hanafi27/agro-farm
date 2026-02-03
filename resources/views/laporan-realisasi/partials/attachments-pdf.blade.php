{{ $item->keterangan ?? '-' }}
@if($item->attachments && $item->attachments->count())
    <div style="margin-top: 5px; font-size: 10px; color: #666;">
        <strong>Bukti Transaksi:</strong><br>
        @foreach($item->attachments as $att)
            <span style="display: inline-block; margin: 2px 5px 2px 0; padding: 2px 5px; background-color: #f0f0f0; border-radius: 3px; border: 1px solid #ddd;">
                {{ $att->filename }}
            </span>
        @endforeach
    </div>
@endif
