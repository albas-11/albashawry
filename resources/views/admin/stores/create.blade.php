@extends('layouts.admin')

@section('title', __('admin.Add Store'))

@section('content')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Leaflet (Map) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        [x-cloak] { display: none !important }
    </style>

    <div class="container py-4" x-data="scheduleBuilder()" x-init="init()">
        <h4 class="mb-4">@lang('admin.Add Store')</h4>

        <form action="{{ route('admin.stores.store') }}" method="POST" @submit="beforeSubmit()">
            @csrf

            <div class="row">
                {{-- Name --}}
                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Name in English')</label>
                    <input type="text" name="name_en" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Name in Arabic')</label>
                    <input type="text" name="name_ar" class="form-control" required>
                </div>

                {{-- Address --}}
                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Address in English')</label>
                    <textarea name="address_en" class="form-control" required></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Address in Arabic')</label>
                    <textarea name="address_ar" class="form-control" required></textarea>
                </div>

                {{-- City / Country --}}
                <div class="col-md-6 mb-3">
                    <label>@lang('admin.City')</label>
                    <input type="text" name="city" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Country')</label>
                    <input type="text" name="country" class="form-control" required>
                </div>

                {{-- Latitude / Longitude (bound to Alpine) --}}
                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Latitude')</label>
                    <input type="number" step="0.0000001" name="latitude" class="form-control"
                           x-model.number="lat"
                           @input.debounce.300ms="syncMarkerFromInputs()">
                </div>

                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Longitude')</label>
                    <input type="number" step="0.0000001" name="longitude" class="form-control"
                           x-model.number="lng"
                           @input.debounce.300ms="syncMarkerFromInputs()">
                </div>

                {{-- Map --}}
                <div class="col-12 mb-3">
                    <label class="d-block mb-2">@lang('admin.Select Location on Map')</label>

                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="useMyLocation()">
                            @lang('admin.Use My Location')
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearMarker()">
                            @lang('admin.Clear Marker')
                        </button>

                        <div class="ms-auto small text-muted">
                            <span>Lat: </span><span x-text="lat ?? '-'"></span>
                            <span class="mx-2">|</span>
                            <span>Lng: </span><span x-text="lng ?? '-'"></span>
                        </div>
                    </div>

                    <div id="storeMap" style="height: 320px; border-radius: 10px; overflow: hidden;"></div>

                    <small class="text-muted d-block mt-2">
                        @lang('admin.Click on the map to select location or drag the marker.')
                    </small>
                </div>

                {{-- Phone --}}
                <div class="col-md-6 mb-3">
                    <label>@lang('admin.Phone')</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>

            {{-- SCHEDULE UI --}}
            <div class="card mt-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <strong>@lang('admin.Store Schedule')</strong>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="generate()">
                            @lang('admin.Generate')
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" @click="clearAll()">
                            @lang('admin.Clear')
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Timezone')</label>
                            <input type="text" class="form-control" x-model="timezone" placeholder="Asia/Riyadh">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Slot Minutes')</label>
                            <select class="form-control" x-model.number="slotMinutes">
                                <option :value="15">15</option>
                                <option :value="30">30</option>
                                <option :value="60">60</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Start Date')</label>
                            <input type="date" class="form-control" x-model="startDate">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Days Count')</label>
                            <input type="number" min="1" max="60" class="form-control" x-model.number="daysCount">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Work From')</label>
                            <input type="time" class="form-control" x-model="workFrom">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Work To')</label>
                            <input type="time" class="form-control" x-model="workTo">
                            <small class="text-muted">آخر Slot قبل وقت الإغلاق بـ slotMinutes</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('admin.Capacity Per Slot')</label>
                            <input type="number" min="1" class="form-control" x-model.number="capacityPerSlot">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100"
                                    style="background:#2E3192;border-color:#2E3192"
                                    @click="generate()">
                                @lang('admin.Generate Slots')
                            </button>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- DAYS TABS --}}
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <template x-for="(dayObj, date) in scheduleDays" :key="date">
                            <button type="button" class="btn btn-sm"
                                    :class="activeDate === date ? 'btn-info' : 'btn-outline-secondary'"
                                    @click="activeDate=date">
                                <span x-text="formatDateLabel(date)"></span>
                                <div class="small" style="font-weight:600">
                                    <span x-text="availableCount(date)"></span> متاح
                                </div>
                            </button>
                        </template>

                        <div x-show="Object.keys(scheduleDays).length===0" class="text-muted">
                            @lang('admin.No schedule yet. Click Generate Slots.')
                        </div>
                    </div>

                    {{-- TIME GRID --}}
                    <div x-show="activeDate && scheduleDays[activeDate]" x-cloak>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold">@lang('admin.Time')</div>
                            <div class="text-muted small">@lang('admin.Click slot to edit capacity/booked')</div>
                        </div>

                        <div class="row g-2">
                            <template x-for="time in timesOf(activeDate)" :key="time">
                                <div class="col-6 col-md-3 col-lg-2">
                                    <button type="button" class="w-100 btn btn-light border"
                                            :class="slotIsFull(activeDate, time) ? 'opacity-50' : ''"
                                            @click="openSlotEditor(activeDate, time)">
                                        <div class="fw-bold" x-text="time"></div>
                                        <div class="small text-muted">
                                            متاح:
                                            <span x-text="slotAvailable(activeDate, time)"></span>
                                        </div>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="addCustomSlot(activeDate)">
                                + @lang('admin.Add Custom Slot')
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeDay(activeDate)">
                                @lang('admin.Remove Day')
                            </button>
                        </div>
                    </div>

                    {{-- SLOT EDIT MODAL --}}
                    <div x-show="slotEditor.open" x-cloak
                         class="position-fixed top-0 start-0 w-100 h-100"
                         style="z-index:9999;background:rgba(0,0,0,.35);"
                         @click.self="slotEditor.open=false">
                        <div class="bg-white rounded shadow p-4" style="max-width:520px;margin:10vh auto;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-bold">@lang('admin.Edit Slot')</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="slotEditor.open=false">×</button>
                            </div>

                            <div class="mb-2">
                                <div class="small text-muted">@lang('admin.Date')</div>
                                <div class="fw-bold" x-text="slotEditor.date"></div>
                            </div>

                            <div class="mb-3">
                                <div class="small text-muted">@lang('admin.Time')</div>
                                <div class="fw-bold" x-text="slotEditor.time"></div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">@lang('admin.Capacity')</label>
                                    <input type="number" min="0" class="form-control" x-model.number="slotEditor.cap">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('admin.Booked')</label>
                                    <input type="number" min="0" class="form-control" x-model.number="slotEditor.booked">
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-danger" @click="deleteSlot(slotEditor.date, slotEditor.time)">
                                    @lang('admin.Delete Slot')
                                </button>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-secondary" @click="slotEditor.open=false">@lang('admin.Cancel')</button>
                                    <button type="button" class="btn btn-primary"
                                            style="background:#2E3192;border-color:#2E3192"
                                            @click="saveSlotEditor()">
                                        @lang('admin.Save')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- hidden field that goes to controller --}}
                    <input type="hidden" name="schedule_json" :value="scheduleJson">
                </div>
            </div>

            <div class="mt-4">
                <button class="btn" style="background-color: #2E3192; color:aliceblue">@lang('admin.Save')</button>
                <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">@lang('admin.Cancel')</a>
            </div>
        </form>
    </div>

    <script>
        function scheduleBuilder(initial = null) {
            const todayISO = () => {
                const d = new Date();
                const p = (n) => (n < 10 ? '0' : '') + n;
                return `${d.getFullYear()}-${p(d.getMonth()+1)}-${p(d.getDate())}`;
            };

            const parseTimeToMinutes = (t) => {
                if (!t) return 0;
                const [h, m] = t.split(':').map(Number);
                return h * 60 + m;
            };

            const minutesToHHMM = (mins) => {
                const h = Math.floor(mins / 60);
                const m = mins % 60;
                const p = (n) => (n < 10 ? '0' : '') + n;
                return `${p(h)}:${p(m)}`;
            };

            const addDaysISO = (iso, add) => {
                const d = new Date(iso + 'T00:00:00');
                d.setDate(d.getDate() + add);
                const p = (n) => (n < 10 ? '0' : '') + n;
                return `${d.getFullYear()}-${p(d.getMonth()+1)}-${p(d.getDate())}`;
            };

            return {
                // ---------------------------
                // MAP STATE
                // ---------------------------
                lat: null,
                lng: null,
                map: null,
                marker: null,

                init() {
                    this.initMap();
                },

                initMap() {
                    // Default center (Riyadh). Change to your default city if you want.
                    const defaultLat = 24.7136;
                    const defaultLng = 46.6753;

                    const startLat = (typeof this.lat === 'number') ? this.lat : defaultLat;
                    const startLng = (typeof this.lng === 'number') ? this.lng : defaultLng;

                    this.map = L.map('storeMap').setView([startLat, startLng], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    // If lat/lng already set, show marker
                    if (typeof this.lat === 'number' && typeof this.lng === 'number') {
                        this.setMarker(this.lat, this.lng, true);
                    }

                    // Click map -> set marker + fill inputs
                    this.map.on('click', (e) => {
                        const { lat, lng } = e.latlng;
                        this.setMarker(lat, lng, true);
                    });
                },

                setMarker(lat, lng, center = false) {
                    this.lat = Number(lat);
                    this.lng = Number(lng);

                    if (!this.marker) {
                        this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);

                        // Drag marker -> update inputs
                        this.marker.on('dragend', () => {
                            const p = this.marker.getLatLng();
                            this.lat = Number(p.lat);
                            this.lng = Number(p.lng);
                        });
                    } else {
                        this.marker.setLatLng([this.lat, this.lng]);
                    }

                    if (center && this.map) {
                        this.map.setView([this.lat, this.lng], Math.max(this.map.getZoom(), 14));
                    }
                },

                syncMarkerFromInputs() {
                    if (typeof this.lat !== 'number' || typeof this.lng !== 'number') return;
                    if (!isFinite(this.lat) || !isFinite(this.lng)) return;
                    if (!this.map) return;

                    this.setMarker(this.lat, this.lng, true);
                },

                clearMarker() {
                    this.lat = null;
                    this.lng = null;
                    if (this.marker && this.map) {
                        this.map.removeLayer(this.marker);
                        this.marker = null;
                    }
                },

                useMyLocation() {
                    if (!navigator.geolocation) return;

                    navigator.geolocation.getCurrentPosition(
                        (pos) => this.setMarker(pos.coords.latitude, pos.coords.longitude, true),
                        (err) => console.warn('Geolocation error', err),
                        { enableHighAccuracy: true, timeout: 8000 }
                    );
                },

                // ---------------------------
                // EXISTING SCHEDULE BUILDER
                // ---------------------------
                timezone: (initial?.timezone || 'Asia/Riyadh'),
                slotMinutes: Number(initial?.slot_minutes || 30),
                startDate: todayISO(),
                daysCount: 7,
                workFrom: '14:00',
                workTo: '22:00',
                capacityPerSlot: 1,

                scheduleDays: initial?.days || {},
                activeDate: null,

                slotEditor: { open: false, date: null, time: null, cap: 1, booked: 0 },

                get scheduleJson() {
                    return JSON.stringify({
                        timezone: this.timezone || 'Asia/Riyadh',
                        slot_minutes: Number(this.slotMinutes || 30),
                        days: this.scheduleDays || {}
                    });
                },
                set scheduleJson(v) {},

                beforeSubmit() {
                    // hidden field is computed already
                },

                clearAll() {
                    this.scheduleDays = {};
                    this.activeDate = null;
                },

                generate() {
                    const days = {};
                    const base = this.startDate || todayISO();
                    const count = Number(this.daysCount || 7);
                    const slotM = Number(this.slotMinutes || 30);

                    const fromM = parseTimeToMinutes(this.workFrom);
                    const toM = parseTimeToMinutes(this.workTo);

                    for (let i = 0; i < count; i++) {
                        const date = addDaysISO(base, i);
                        const slots = {};
                        for (let m = fromM; m + slotM <= toM; m += slotM) {
                            const t = minutesToHHMM(m);
                            slots[t] = { cap: Number(this.capacityPerSlot || 1), booked: 0 };
                        }
                        days[date] = { slots };
                    }

                    this.scheduleDays = days;
                    this.activeDate = Object.keys(days)[0] || null;
                },

                removeDay(date) {
                    if (!date) return;
                    delete this.scheduleDays[date];
                    const keys = Object.keys(this.scheduleDays);
                    this.activeDate = keys[0] || null;
                },

                addCustomSlot(date) {
                    if (!date) return;
                    const t = prompt('Enter time (HH:MM)', '14:00');
                    if (!t) return;
                    if (!this.scheduleDays[date]) this.scheduleDays[date] = { slots: {} };
                    if (!this.scheduleDays[date].slots) this.scheduleDays[date].slots = {};
                    this.scheduleDays[date].slots[t] = { cap: Number(this.capacityPerSlot || 1), booked: 0 };
                },

                timesOf(date) {
                    const slots = this.scheduleDays?.[date]?.slots || {};
                    return Object.keys(slots).sort();
                },

                availableCount(date) {
                    const slots = this.scheduleDays?.[date]?.slots || {};
                    let sum = 0;
                    for (const t of Object.keys(slots)) {
                        const cap = Number(slots[t]?.cap || 0);
                        const booked = Number(slots[t]?.booked || 0);
                        sum += Math.max(cap - booked, 0);
                    }
                    return sum;
                },

                slotAvailable(date, time) {
                    const s = this.scheduleDays?.[date]?.slots?.[time];
                    if (!s) return 0;
                    return Math.max(Number(s.cap || 0) - Number(s.booked || 0), 0);
                },

                slotIsFull(date, time) {
                    return this.slotAvailable(date, time) <= 0;
                },

                formatDateLabel(iso) {
                    return iso;
                },

                openSlotEditor(date, time) {
                    const s = this.scheduleDays?.[date]?.slots?.[time];
                    this.slotEditor = {
                        open: true,
                        date, time,
                        cap: Number(s?.cap ?? 1),
                        booked: Number(s?.booked ?? 0),
                    };
                },

                saveSlotEditor() {
                    const { date, time, cap, booked } = this.slotEditor;
                    if (!date || !time) return;
                    if (!this.scheduleDays[date]) this.scheduleDays[date] = { slots: {} };
                    if (!this.scheduleDays[date].slots) this.scheduleDays[date].slots = {};

                    this.scheduleDays[date].slots[time] = {
                        cap: Math.max(Number(cap || 0), 0),
                        booked: Math.max(Number(booked || 0), 0),
                    };
                    this.slotEditor.open = false;
                },

                deleteSlot(date, time) {
                    if (!date || !time) return;
                    const slots = this.scheduleDays?.[date]?.slots;
                    if (!slots || !slots[time]) return;
                    delete slots[time];
                    this.slotEditor.open = false;
                },
            }
        }
    </script>
@endsection
