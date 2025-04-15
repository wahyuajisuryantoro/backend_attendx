@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .office-info {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .form-section {
            display: none;
        }
        .info-section {
            display: block;
        }
    </style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Information Office Location</h5>
                    <div>
                        <button id="editButton" class="btn btn-primary btn-sm">Edit Information</button>
                        <button id="cancelButton" class="btn btn-secondary btn-sm" style="display: none;">Cancel</button>
                        <button id="saveButton" class="btn btn-success btn-sm" style="display: none;">Save</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                    
                    <div class="info-section" id="infoSection">
                        <div class="office-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Name:</h6>
                                    <p id="nameDisplay">{{ $officeLocation->name }}</p>
                                    
                                    <h6>Address:</h6>
                                    <p id="addressDisplay">{{ $officeLocation->address }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Coordinate:</h6>
                                    <p id="coordinatesDisplay">{{ $officeLocation->coordinates }}</p>
                                    
                                    <h6>Radius (m):</h6>
                                    <p id="radiusDisplay">{{ $officeLocation->radius }}</p>
                                    
                                    <h6>Status:</h6>
                                    <p id="statusDisplay">
                                        @if($officeLocation->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-section" id="formSection">
                        <form id="updateForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $officeLocation->name }}" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" required>{{ $officeLocation->address }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinates" class="form-label">Coordinate (Lat,Lng)</label>
                                        <input type="text" class="form-control" id="coordinates" name="coordinates" value="{{ $officeLocation->coordinates }}" required>
                                        <small class="text-muted">Click on the map to change coordinates</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="radius" class="form-label">Radius (meter)</label>
                                        <input type="number" class="form-control" id="radius" name="radius" value="{{ $officeLocation->radius }}" min="1" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="is_active" class="form-label">Status</label>
                                        <select class="form-select" id="is_active" name="is_active" required>
                                            <option value="1" {{ $officeLocation->is_active ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ !$officeLocation->is_active ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isEditMode = false;
            let map, marker, circle;
            
            let coordinates = "{{ $officeLocation->coordinates }}".split(',');
            let lat = parseFloat(coordinates[0]);
            let lng = parseFloat(coordinates[1]);
            let radius = {{ $officeLocation->radius }};
            
            initMap();

            document.getElementById('editButton').addEventListener('click', function() {
                toggleEditMode(true);
            });
            
            document.getElementById('cancelButton').addEventListener('click', function() {
                toggleEditMode(false);

                document.getElementById('name').value = "{{ $officeLocation->name }}";
                document.getElementById('address').value = "{{ $officeLocation->address }}";
                document.getElementById('coordinates').value = "{{ $officeLocation->coordinates }}";
                document.getElementById('radius').value = "{{ $officeLocation->radius }}";
                document.getElementById('is_active').value = "{{ $officeLocation->is_active ? '1' : '0' }}";
                
                coordinates = "{{ $officeLocation->coordinates }}".split(',');
                lat = parseFloat(coordinates[0]);
                lng = parseFloat(coordinates[1]);
                radius = {{ $officeLocation->radius }};
                updateMapElements();
            });
            
            document.getElementById('saveButton').addEventListener('click', function() {
                Swal.fire({
                    title: 'Confirmation',
                    text: 'Are you sure you want to save changes?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Save',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveChanges();
                    }
                });
            });
            
            document.getElementById('radius').addEventListener('input', function() {
                radius = parseInt(this.value);
                updateMapElements();
            });
            
            function initMap() {
                if (isNaN(lat) || isNaN(lng)) {
                    console.error('Invalid coordinates:', coordinates);
                    lat = -6.2297209;
                    lng = 106.664705;
                }

                map = L.map('map').setView([lat, lng], 16);      

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                marker = L.marker([lat, lng], {
                    draggable: false
                }).addTo(map)
                  .bindPopup("{{ $officeLocation->name }}").openPopup();
                
                circle = L.circle([lat, lng], {
                    color: 'blue',
                    fillColor: '#30c',
                    fillOpacity: 0.2,
                    radius: radius
                }).addTo(map);
            
                map.on('click', function(e) {
                    if (isEditMode) {
                        updateLocation(e.latlng.lat, e.latlng.lng);
                    }
                });
            }
            
            function toggleEditMode(edit) {
                isEditMode = edit;
                
                if (edit) {
                    document.getElementById('infoSection').style.display = 'none';
                    document.getElementById('formSection').style.display = 'block';
                    document.getElementById('editButton').style.display = 'none';
                    document.getElementById('cancelButton').style.display = 'inline-block';
                    document.getElementById('saveButton').style.display = 'inline-block';
                    
                    marker.dragging.enable();
                    marker.on('dragend', function() {
                        updateLocation(marker.getLatLng().lat, marker.getLatLng().lng);
                    });
                } else {
                    document.getElementById('infoSection').style.display = 'block';
                    document.getElementById('formSection').style.display = 'none';
                    document.getElementById('editButton').style.display = 'inline-block';
                    document.getElementById('cancelButton').style.display = 'none';
                    document.getElementById('saveButton').style.display = 'none';
                    
                    marker.dragging.disable();
                }
            }
        
            function updateLocation(newLat, newLng) {
                lat = newLat;
                lng = newLng;
                document.getElementById('coordinates').value = lat.toFixed(7) + ',' + lng.toFixed(7);
                updateMapElements();
            }
            
            function updateMapElements() {
                marker.setLatLng([lat, lng]);
                if (map.hasLayer(circle)) {
                    map.removeLayer(circle);
                }
                
                circle = L.circle([lat, lng], {
                    color: 'blue',
                    fillColor: '#30c',
                    fillOpacity: 0.2,
                    radius: radius
                }).addTo(map);
            }
            
            function saveChanges() {
                const form = document.getElementById('updateForm');
                const formData = new FormData(form);
                
                fetch('{{ route("office.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            document.getElementById('nameDisplay').textContent = document.getElementById('name').value;
                            document.getElementById('addressDisplay').textContent = document.getElementById('address').value;
                            document.getElementById('coordinatesDisplay').textContent = document.getElementById('coordinates').value;
                            document.getElementById('radiusDisplay').textContent = document.getElementById('radius').value;
                            
                            const isActive = document.getElementById('is_active').value === '1';
                            document.getElementById('statusDisplay').innerHTML = isActive 
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-danger">Inactive</span>';
                            toggleEditMode(false);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'An error occurred while saving data',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while saving data',
                        icon: 'error'
                    });
                });
            }
        });
    </script>
@endsection