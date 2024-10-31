@php
    $dataExcel = $data['data'];

@endphp
<table>
    <thead>
    <tr>
        <th>Thuộc tính</th>
        <th>Số tiền</th>
        <th>Dịch vụ</th>
    </tr>
    </thead>
    <tbody>

        @foreach($dataExcel??[] as $service)
            @php
                if ($service->params){
                    $params = json_decode($service->params);
                    $names =\App\Library\Helpers::DecodeJson('name',$service->params);
                    $prices =\App\Library\Helpers::DecodeJson('price',$service->params);
                }

            @endphp
            @if(!empty($names))
                @foreach($names??[] as $key => $name)
                    <tr>
                        <td>{{ $name??null }}</td>
                        <td>{{ $prices[$key] }}</td>
                        <td>{{ $service->title}}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach

    </tbody>
</table>
