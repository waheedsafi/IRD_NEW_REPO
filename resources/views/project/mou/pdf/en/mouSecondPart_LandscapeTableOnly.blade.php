<table width="90%" style="border-collapse: collapse; margin: 20px auto; font-size: 14px;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid black; padding: 8px;">Province</th>
            <th style="border: 1px solid black; padding: 8px;">Health Facilities</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($health_facilities as $facilities)
            <tr>
                <td style="border: 1px solid black; padding: 8px;">{{ $facilities['province'] }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $facilities['facilities'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
