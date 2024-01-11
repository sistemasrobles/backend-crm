<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo Electrónico</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px; background-color: #fff;">
            	<!-- <img src="{{$message['logo']}}">  -->
                <h2 style="color: #333;"> {{$message['header']}}</h2>
                <br>
                <p style="color: #555;">{!! $message['body'] !!}</p>
                <br>

                 <p style="color: #555;">{!! $message['footer'] !!}</p>

				  
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr>
            <td style="padding: 20px; background-color: #05A07D; color: #fff; text-align: center;">
                &copy; 2023 {{ config('app.name') }}. Todos los derechos reservados.
            </td>
        </tr>
    </table>

    

</body>
</html>