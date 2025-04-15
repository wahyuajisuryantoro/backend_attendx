@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Attendance Details</h5>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                <i class="ti ti-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        @if ($attendance->user->profile && $attendance->user->profile->profile_photo)
                            <img src="{{ asset('storage/' . $attendance->user->profile->profile_photo) }}"
                                class="rounded-circle me-3" width="60">
                        @else
                            <div class="rounded-circle me-3"
                                style="width: 60px; height: 60px; background-color: #ddd; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                {{ strtoupper(substr($attendance->user->profile->name ?? 'N/A', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $attendance->user->profile->name ?? 'N/A' }}</h5>
                            <p class="mb-0 text-muted">
                                {{ $attendance->user->profile->position ?? 'N/A' }} |
                                {{ $attendance->user->profile->department ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Date: {{ $attendance->date->format('l, F d, Y') }}</h5>
                    <p>
                        @if (!$attendance->clock_in)
                            <span class="badge bg-danger">Absent</span>
                        @elseif(Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') <= '09:00:00')
                            <span class="badge bg-success">On Time</span>
                        @else
                            <span class="badge bg-warning">Late</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Clock In Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Time:</div>
                                <div class="col-md-8">
                                    {{ $attendance->clock_in ? Carbon\Carbon::parse($attendance->clock_in)->format('h:i:s A') : 'Not clocked in' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Method:</div>
                                <div class="col-md-8">{{ $attendance->clock_in_method ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Location:</div>
                                <div class="col-md-8">
                                    @php
                                        if ($attendance->clock_in_location) {
                                            $location = json_decode($attendance->clock_in_location, true);
                                            if (is_array($location) && isset($location['address'])) {
                                                echo $location['address'];
                                                if (isset($location['distance'])) {
                                                    echo ' (Distance: ' .
                                                        number_format($location['distance'], 2) .
                                                        ' meters)';
                                                }
                                            } else {
                                                echo $attendance->clock_in_location;
                                            }
                                        } else {
                                            echo 'No location data';
                                        }
                                    @endphp
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($attendance->clock_in_photo)
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $attendance->clock_in_photo) }}"
                                                class="img-fluid img-thumbnail" style="max-height: 200px;">
                                        </div>
                                    @else
                                        <p class="text-center text-muted">No photo available</p>
                                    @endif
                                </div>
                            </div>
                            @if ($attendance->clock_in_location)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div id="clockInMap" style="height: 200px;"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Clock Out Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Time:</div>
                                <div class="col-md-8">
                                    {{ $attendance->clock_out ? Carbon\Carbon::parse($attendance->clock_out)->format('h:i:s A') : 'Not clocked out' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Method:</div>
                                <div class="col-md-8">{{ $attendance->clock_out_method ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Location:</div>
                                <div class="col-md-8">
                                    @php
                                        if ($attendance->clock_in_location) {
                                            $location = json_decode($attendance->clock_in_location, true);
                                            if (is_array($location) && isset($location['address'])) {
                                                echo $location['address'];
                                                if (isset($location['distance'])) {
                                                    echo ' (Distance: ' .
                                                        number_format($location['distance'], 2) .
                                                        ' meters)';
                                                }
                                            } else {
                                                echo $attendance->clock_in_location;
                                            }
                                        } else {
                                            echo 'No location data';
                                        }
                                    @endphp
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($attendance->clock_out_photo)
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $attendance->clock_out_photo) }}"
                                                class="img-fluid img-thumbnail" style="max-height: 200px;">
                                        </div>
                                    @else
                                        <p class="text-center text-muted">No photo available</p>
                                    @endif
                                </div>
                            </div>
                            @if ($attendance->clock_out_location)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div id="clockOutMap" style="height: 200px;"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Additional Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Work Duration:</div>
                        <div class="col-md-9">
                            @if ($attendance->clock_in && $attendance->clock_out)
                                @php
                                    $clockIn = Carbon\Carbon::parse($attendance->clock_in);
                                    $clockOut = Carbon\Carbon::parse($attendance->clock_out);
                                    
                                    if ($clockOut->lt($clockIn)) {
                                        $clockOut->addDay();
                                    }

                                    $diffInMinutes = $clockOut->diffInMinutes($clockIn);
                                    $diffInHours = floor($diffInMinutes / 60);
                                    $remainingMinutes = $diffInMinutes % 60;

                                    echo $diffInHours . ' hours ' . $remainingMinutes . ' minutes';
                                @endphp
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 fw-bold">Notes:</div>
                        <div class="col-md-9">{{ $attendance->notes ?? 'No notes available' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        $(document).ready(function() {
            @if ($attendance->clock_in_location)
                const clockInLocationData = {!! json_encode(json_decode($attendance->clock_in_location), JSON_HEX_APOS | JSON_HEX_QUOT) !!};
                if (clockInLocationData) {
                    const clockInCoords = {
                        lat: parseFloat(clockInLocationData.latitude || 0),
                        lng: parseFloat(clockInLocationData.longitude || 0),
                        address: clockInLocationData.address || '',
                        office_name: clockInLocationData.office_name || 'Office',
                        distance: clockInLocationData.distance || 0
                    };

                    const clockInMap = L.map('clockInMap').setView([clockInCoords.lat, clockInCoords.lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(clockInMap);
                    let popupContent = `
            <strong>${clockInCoords.office_name}</strong><br>
            ${clockInCoords.address}<br>
            <small>Distance: ${parseFloat(clockInCoords.distance).toFixed(2)} meters</small>
        `;

                    L.marker([clockInCoords.lat, clockInCoords.lng]).addTo(clockInMap)
                        .bindPopup(popupContent)
                        .openPopup();
                }
            @endif

            @if ($attendance->clock_out_location)
                const clockOutLocationData = {!! json_encode(json_decode($attendance->clock_out_location), JSON_HEX_APOS | JSON_HEX_QUOT) !!};
                if (clockOutLocationData) {
                    const clockOutCoords = {
                        lat: parseFloat(clockOutLocationData.latitude || 0),
                        lng: parseFloat(clockOutLocationData.longitude || 0),
                        address: clockOutLocationData.address || '',
                        office_name: clockOutLocationData.office_name || 'Office',
                        distance: clockOutLocationData.distance || 0
                    };

                    const clockOutMap = L.map('clockOutMap').setView([clockOutCoords.lat, clockOutCoords.lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(clockOutMap);
                    let popupContent = `
            <strong>${clockOutCoords.office_name}</strong><br>
            ${clockOutCoords.address}<br>
            <small>Distance: ${parseFloat(clockOutCoords.distance).toFixed(2)} meters</small>
        `;

                    L.marker([clockOutCoords.lat, clockOutCoords.lng]).addTo(clockOutMap)
                        .bindPopup(popupContent)
                        .openPopup();
                }
            @endif
            function parseCoordinates(locationString) {
                if (!locationString) return null;

                try {
                    const parsed = JSON.parse(locationString);
                    if (parsed.latitude && parsed.longitude) {
                        return {
                            lat: parseFloat(parsed.latitude),
                            lng: parseFloat(parsed.longitude)
                        };
                    }
                    if (parsed.lat && parsed.lng) {
                        return parsed;
                    }
                    if (parsed.address) {
                        return {
                            lat: parseFloat(parsed.latitude),
                            lng: parseFloat(parsed.longitude),
                            address: parsed.address,
                            office_name: parsed.office_name,
                            distance: parsed.distance
                        };
                    }
                } catch (e) {
                    console.error("Error parsing location data:", e);
                }
                const parts = locationString.split(',');
                if (parts.length === 2) {
                    const lat = parseFloat(parts[0].trim());
                    const lng = parseFloat(parts[1].trim());

                    if (!isNaN(lat) && !isNaN(lng)) {
                        return {
                            lat,
                            lng
                        };
                    }
                }
                return null;
            }
        });
    </script>
@endsection
