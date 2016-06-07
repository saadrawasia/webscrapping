<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        {{--<style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>--}}
    </head>
    <body>
       {{-- <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>
            </div>
        </div>--}}
            <table border="1px">
                <tr>
                    <th>Id</th>
                    <th>Author</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Date</th>
                    <th>Link</th>
                    <th>Image Src</th>
                </tr>
                @foreach($scrapped_data as $data)
                    <tr>
                        <td>{{$data->id}}</td>
                        <td>{{$data->name}}</td>
                        <td>{{$data->title}}</td>
                        <td>{{$data->subtitle}}</td>
                        <td>{{$data->date}}</td>
                        <td>{{$data->link}}</td>
                        <td>{{$data->img}}</td>
                    </tr>
                @endforeach()
            </table>
    </body>
</html>
