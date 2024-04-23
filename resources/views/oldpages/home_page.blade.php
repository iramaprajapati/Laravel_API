<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>

<body>
    <h1>Hello, {{ $name ?? 'Guest' }}</h1>
    <h2>Welcome to the Home page!</h2>
    <div>
        @php
            $countries = ['India', 'Bhutan', 'Italy', 'Japan', 'Nepal', 'Norway'];
            // echo '<pre>';
            // print_r($countries);

        @endphp
        <select name="" id="">
            @foreach ($countries as $key => $country)
                <option value="{{ $key }}">{{ $country }}</option>
            @endforeach
        </select>
    </div>
</body>

</html>
