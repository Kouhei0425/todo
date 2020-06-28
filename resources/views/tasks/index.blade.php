@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col col-md-4">
                <nav class="panel panel-default">
                    <div class="panel-heading">フォルダ</div>
                    <div class="panel-body">
                        <a href="{{ route('folders.create') }}" class="btn btn-default btn-block">
                            フォルダを追加する
                        </a>
                    </div>
                    <div class="list-group">
                        @foreach($folders as $folder)
                            <a
                                href="{{ route('tasks.index', ['id' => $folder->id]) }}"
                                class="list-group-item {{ $current_folder_id === $folder->id ? 'active' : '' }}"
                            >
                                {{ $folder->title }}
                            </a>
                        @endforeach
                    </div>
                </nav>
            </div>
            <div class="column col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">タスク</div>
                    <div class="panel-body">
                        <div class="text-right">
                            <a href="{{ route('tasks.create', ['id' => $current_folder_id]) }}" class="btn btn-default btn-block">
                                タスクを追加する
                            </a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>状態</th>
                            <th>期限</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>
                                    <span class="label {{ $task->status_class }}">{{ $task->status_label }}</span>
                                </td>
                                <td>{{ $task->formatted_due_date }}</td>
                                <td><a href="{{ route('tasks.edit', ['id' => $task->folder_id, 'task_id' => $task->id]) }}">編集</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="map" style="height: 50vh; width: 80%; margin: 0 auto 5rem;"></div>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google-map.apikey') }}&libraries=places"
            ></script>
            <script type="text/javascript">

                $(function(){

                    'use strict';
                    var map;
                    var service;
                    var infowindow;
                    var pyrmont = new google.maps.LatLng(35.654918,139.694922);
                    createMap(pyrmont)

                    document.getElementById('getcurrentlocation').onclick = function() {
                        geoLocationInit();
                    }

                    function geoLocationInit() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(success, fail);

                        } else {
                            createMap(pyrmont);
                        }
                    }

                    function success(position) {
                        var currentLat = position.coords.latitude;
                        var currentLng = position.coords.longitude;

                        var pyrmont = new google.maps.LatLng(currentLat,currentLng);

                        createMap(pyrmont)

                        CurrentPositionMarker(pyrmont);
                    }

                    function fail(pyrmont) {
                        createMap(pyrmont);
                    }

                    function createMap(pyrmont) {

                        map = new google.maps.Map(document.getElementById('map'), {
                            center: pyrmont,
                            zoom: 14
                        });
                        nearbysearch(pyrmont)
                    }

                    function createMarker(latlng, icn, place)
                    {
                        var marker = new google.maps.Marker({
                            position: latlng,
                            map: map
                        });

                        var placename = place.name;
                        // 吹き出しにカフェの名前を埋め込む
                        var contentString = `<div class="sample"><p id="place_name">${placename}</p></div>`;

                        // 吹き出し
                        var infoWindow = new google.maps.InfoWindow({ // 吹き出しの追加
                            content:  contentString// 吹き出しに表示する内容
                        });


                        marker.addListener('click', function() { // マーカーをクリックしたとき
                            infoWindow.open(map, marker); // 吹き出しの表示
                        });

                    }


                    function CurrentPositionMarker(pyrmont) {
                        var image = 'http://i.stack.imgur.com/orZ4x.png';
                        var marker = new google.maps.Marker({
                            position: pyrmont,
                            map: map,
                            icon: image
                        });
                        marker.setMap(map);
                    }

                    function nearbysearch(pyrmont) {
                        var request = {
                            location: pyrmont,
                            radius: '3000',
                            type: ['gym']
                        };

                        service = new google.maps.places.PlacesService(map);
                        service.nearbySearch(request, callback);

                        function callback(results, status) {
                            if (status == google.maps.places.PlacesServiceStatus.OK) {
                                for (var i = 0; i < results.length; i++) {
                                    var place = results[i];
                                    var latlng = place.geometry.location;
                                    var icn = place.icon;

                                    createMarker(latlng, icn, place);
                                }
                            }
                        }
                    }
                });

            </script>
        </div>
    </div>
@endsection
