<h2>Lịch hội đồng</h2>
<h3>GV: {{ data_get($data, 'lecturer.code') }} - {{ data_get($data, 'lecturer.name') }}</h3>
<table style="border-collapse: collapse;">
    <thead>
    <tr>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">STT</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">MSSV</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">Tên SV</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">Mã nhóm</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">Tên đề tài</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">Thời gian</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">Địa điểm</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid black;">Vai trò</th>
    </tr>
    </thead>
    <tbody>
    @php
        $lecturer = data_get($data, 'lecturer');
    @endphp
    @foreach(data_get($data, 'topics') as $index => $topic)
        @php
            $count = $topic->students->count();
            $students = $topic->students;
            $role = null;
            if ($topic->lecturer_id == data_get($lecturer, 'id')) {
                $role = "GVHD";
            } elseif ($topic->critical_id == data_get($lecturer, 'id')) {
                $role = "GVPB";
            } elseif (data_get($topic, 'committee.president_id') == data_get($lecturer, 'id')) {
                $role = "Chủ tịch HĐ";
            } else {
                $role = "Thư ký HĐ";
            }
        @endphp
        @foreach($students as $studentIndex => $student)
            @if($studentIndex == 0)
                <tr>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;"
                        rowspan="{{$count}}">{{ $index + 1 }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;">
                        {{ data_get($student, 'code') }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;">
                        {{ data_get($student, 'name') }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;"
                        rowspan="{{$count}}">{{ data_get($topic, 'code') }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;"
                        rowspan="{{$count}}">{{ data_get($topic, 'title') }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;"
                        rowspan="{{$count}}">{{ data_get($topic, 'committee.defense_date') ? \Carbon\Carbon::parse(data_get($topic, 'committee.defense_date'), 'Asia/Ho_Chi_Minh')->format("d/m/Y H:i") : '' }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;"
                        rowspan="{{$count}}">{{ data_get($topic, 'committee.address') }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;"
                        rowspan="{{$count}}">{{ $role }}</td>
                </tr>
            @else
                <tr>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;">{{ data_get($student, 'code') }}</td>
                    <td style="vertical-align: center; text-align: center; border: 1px solid black;">{{ data_get($student, 'name') }}</td>
                </tr>
            @endif
        @endforeach
    @endforeach
    <tr></tr>
    </tbody>
</table>
<p>Quý thầy cô vui lòng truy cập http://hungpq.click để xem thông tin chi tiết</p>