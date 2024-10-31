@if(isset($data) && count($data))
    @foreach($data as $key => $item)
    <tr class="odd">
        <td style="width: 24px">{{ ++$key }}</td>
        <td class="sorting_1"><a href="{{ $item }}" target="_blank">{{ $item }}</a></td>
    </tr>
    @endforeach
@endif
