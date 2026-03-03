<!DOCTYPE html>
<html>
<head>
    <title>Tables</title>
</head>
<body>
    <h2>Tables</h2>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <form action="{{ route('tables.store') }}" method="POST">
        @csrf
        <input type="text" name="table_number" placeholder="Table Number">
        <button type="submit">Create Table</button>
    </form>

    <form action="{{ route('tables.generateAll') }}" method="POST" style="margin-top:20px;">
        @csrf
        <button type="submit">Generate All QR Codes</button>
    </form>

    <table border="1" cellpadding="5" cellspacing="0" style="margin-top:20px;">
        <thead>
            <tr>
                <th>Table Number</th>
                <th>QR Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tables as $table)
                <tr>
                    <td>{{ $table->table_number }}</td>
                    <td>
                        @if($table->qr_code_path)
                            <img src="{{ asset('storage/'.$table->qr_code_path) }}" width="100">
                            <a href="{{ asset('storage/'.$table->qr_code_path) }}" download>Download</a>
                        @else
                            <span>No QR</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>